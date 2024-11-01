<?php
class frameWcu {
    private $_modules = array();
    private $_tables = array();
    private $_allModules = array();
    /**
     * bool Uses to know if we are on one of the plugin pages
     */
    private $_inPlugin = false;
    /**
     * Array to hold all scripts and add them in one time in addScripts method
     */
    private $_scripts = array();
    private $_scriptsInitialized = false;
    private $_styles = array();
	private $_stylesInitialized = false;
	private $_useFootAssets = false;
    
    private $_scriptsVars = array();
    private $_mod = '';
    private $_action = '';
    /**
     * Object with result of executing non-ajax module request
     */
    private $_res = null;
	private $_orders = array();
    
    public function __construct() {
        $this->_res = toeCreateObjWcu('response', array());
        
    }
    static public function getInstance() {
        static $instance;
        if(!$instance) {
            $instance = new frameWcu();
        }
        return $instance;
    }
    static public function _() {
        return self::getInstance();
    }
    public function parseRoute() {
        // Check plugin
        $pl = reqWcu::getVar('pl');
        if($pl == WCU_CODE) {
            $mod = reqWcu::getMode();
            if($mod)
                $this->_mod = $mod;
            $action = reqWcu::getVar('action');
            if($action)
                $this->_action = $action;
        }
    }
    public function setMod($mod) {
        $this->_mod = $mod;
    }
    public function getMod() {
        return $this->_mod;
    }
    public function setAction($action) {
        $this->_action = $action;
    }
    public function getAction() {
        return $this->_action;
    }
    protected function _extractModules() {
        $activeModules = $this->getTable('modules')
                ->innerJoin( $this->getTable('modules_type'), 'type_id' )
				->orderBy('id ASC')
                ->get($this->getTable('modules')->alias(). '.*, '. $this->getTable('modules_type')->alias(). '.label as type_name');
        if($activeModules) {
            foreach($activeModules as $m) {
                $code = $m['code'];
                $moduleLocationDir = WCU_MODULES_DIR;
                if(!empty($m['ex_plug_dir'])) {
                    $moduleLocationDir = utilsWcu::getExtModDir( $m['ex_plug_dir'] );
                }
                if(is_dir($moduleLocationDir. $code)) {
                    $this->_allModules[$m['code']] = 1;
                    if((bool)$m['active']) {
                        importClassWcu($code. strFirstUp(WCU_CODE), $moduleLocationDir. $code. DS. 'mod.php');
                        $moduleClass = toeGetClassNameWcu($code);
                        if(class_exists($moduleClass)) {
                            $this->_modules[$code] = new $moduleClass($m);
                            if(is_dir($moduleLocationDir. $code. DS. 'tables')) {
                                $this->_extractTables($moduleLocationDir. $code. DS. 'tables'. DS);
                            }
                        }
                    }
                }
            }
        }
        //$operationTime = microtime(true) - $startTime;
    }
    protected function _initModules() {
        if(!empty($this->_modules)) {
            foreach($this->_modules as $mod) {
                 $mod->init();
            }
        }
    }
    public function init() {
        //$startTime = microtime(true);
        reqWcu::init();
        $this->_extractTables();
        $this->_extractModules();

        $this->_initModules();

		dispatcherWcu::doAction('afterModulesInit');
		
		modInstallerWcu::checkActivationMessages();
		
        $this->_execModules();
        
		$addAssetsAction = $this->usePackAssets() && !is_admin() ? 'wp_footer' : 'init';

        add_action($addAssetsAction, array($this, 'addScripts'));
        add_action($addAssetsAction, array($this, 'addStyles'));

        register_activation_hook(  WCU_DIR. DS. WCU_MAIN_FILE, array('utilsWcu', 'activatePlugin')  ); //See classes/install.php file
        register_uninstall_hook(WCU_DIR. DS. WCU_MAIN_FILE, array('utilsWcu', 'deletePlugin'));
		register_deactivation_hook(WCU_DIR. DS. WCU_MAIN_FILE, array( 'utilsWcu', 'deactivatePlugin' ) );

		add_action('init', array($this, 'connectLang'));
        //$operationTime = microtime(true) - $startTime;
    }
	public function connectLang() {
		load_plugin_textdomain(WCU_LANG_CODE, false, WCU_PLUG_NAME. '/languages/');
	}
    /**
     * Check permissions for action in controller by $code and made corresponding action
     * @param string $code Code of controller that need to be checked
     * @param string $action Action that need to be checked
     * @return bool true if ok, else - should exit from application
     */
    public function checkPermissions($code, $action) {
        if($this->havePermissions($code, $action))
            return true;
        else {
            exit(_e('You have no permissions to view this page', WCU_LANG_CODE));
        }
    }
    /**
     * Check permissions for action in controller by $code
     * @param string $code Code of controller that need to be checked
     * @param string $action Action that need to be checked
     * @return bool true if ok, else - false
     */
    public function havePermissions($code, $action) {
        $res = true;
        $mod = $this->getModule($code);
        $action = strtolower($action);
        if($mod) {
            $permissions = $mod->getController()->getPermissions();
            if(!empty($permissions)) {  // Special permissions
                if(isset($permissions[WCU_METHODS])
                    && !empty($permissions[WCU_METHODS])
                ) {
                    foreach($permissions[WCU_METHODS] as $method => $permissions) {   // Make case-insensitive
                        $permissions[WCU_METHODS][strtolower($method)] = $permissions;
                    }
                    if(array_key_exists($action, $permissions[WCU_METHODS])) {        // Permission for this method exists
                        $currentUserPosition = frameWcu::_()->getModule('user')->getCurrentUserPosition();
                        if((is_array($permissions[ WCU_METHODS ][ $action ]) && !in_array($currentUserPosition, $permissions[ WCU_METHODS ][ $action ]))
                            || (!is_array($permissions[ WCU_METHODS ][ $action ]) && $permissions[WCU_METHODS][$action] != $currentUserPosition)
                        ) {
                            $res = false;
                        }
                    }
                }
                if(isset($permissions[WCU_USERLEVELS])
                    && !empty($permissions[WCU_USERLEVELS])
                ) {
                    $currentUserPosition = frameWcu::_()->getModule('user')->getCurrentUserPosition();
					// For multi-sites network admin role is undefined, let's do this here
					if(is_multisite() && is_admin() && is_super_admin()) {
						$currentUserPosition = WCU_ADMIN;
					}
                    foreach($permissions[WCU_USERLEVELS] as $userlevel => $methods) {
                        if(is_array($methods)) {
                            $lowerMethods = array_map('strtolower', $methods);          // Make case-insensitive
                            if(in_array($action, $lowerMethods)) {                      // Permission for this method exists
                                if($currentUserPosition != $userlevel) 
                                    $res = false;
                                break;
                            }
                        } else {
                            $lowerMethod = strtolower($methods);            // Make case-insensitive
                            if($lowerMethod == $action) {                   // Permission for this method exists
                                if($currentUserPosition != $userlevel) 
                                    $res = false;
                                break;
                            }
                        }
                    }
                }
            }
			if($res) {	// Additional check for nonces
				$noncedMethods = $mod->getController()->getNoncedMethods();
				if(!empty($noncedMethods)) {
					$noncedMethods = array_map('strtolower', $noncedMethods);
					if(in_array($action, $noncedMethods)) {
						$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : reqCfs::getVar('_wpnonce');
						if(!wp_verify_nonce( $nonce, $action )) {
							$res = false;
						}
					}
				}
			}
        }
        return $res;
    }
    public function getRes() {
        return $this->_res;
    }
	public function execAfterWpInit() {
		$this->_doExec();
	}
	/**
	 * Check if method for module require some special permission. We can detect users permissions only after wp init action was done.
	 */
	protected function _execOnlyAfterWpInit() {
		$res = false;
        $mod = $this->getModule( $this->_mod );
        $action = strtolower( $this->_action );
        if($mod) {
            $permissions = $mod->getController()->getPermissions();
            if(!empty($permissions)) {  // Special permissions
                if(isset($permissions[WCU_METHODS])
                    && !empty($permissions[WCU_METHODS])
                ) {
                    foreach($permissions[WCU_METHODS] as $method => $permissions) {   // Make case-insensitive
                        $permissions[WCU_METHODS][strtolower($method)] = $permissions;
                    }
                    if(array_key_exists($action, $permissions[WCU_METHODS])) {        // Permission for this method exists
						$res = true;
					}
                }
                if(isset($permissions[WCU_USERLEVELS])
                    && !empty($permissions[WCU_USERLEVELS])
                ) {
					$res = true;
                }
            }
        }
        return $res;
	}
    protected function _execModules() {
        if($this->_mod) {
            // If module exist and is active
            $mod = $this->getModule($this->_mod);
            if($mod && !empty($this->_action)) {
				if($this->_execOnlyAfterWpInit()) {
					add_action('init', array($this, 'execAfterWpInit'));
				} else {
					$this->_doExec();
				}
            }
        }
    }
	protected function _doExec() {
		$mod = $this->getModule($this->_mod);
		if($mod && $this->checkPermissions($this->_mod, $this->_action)) {
			switch(reqWcu::getVar('reqType')) {
				case 'ajax':
					add_action('wp_ajax_'. $this->_action, array($mod->getController(), $this->_action));
					add_action('wp_ajax_nopriv_'. $this->_action, array($mod->getController(), $this->_action));
					break;
				default:
					$this->_res = $mod->exec($this->_action);
					break;
			}
		}
	}
    protected function _extractTables($tablesDir = WCU_TABLES_DIR) {
        $mDirHandle = opendir($tablesDir);
        while(($file = readdir($mDirHandle)) !== false) {
            if(is_file($tablesDir. $file) && $file != '.' && $file != '..' && strpos($file, '.php')) {
                $this->_extractTable( str_replace('.php', '', $file), $tablesDir );
            }
        }
    }
    protected function _extractTable($tableName, $tablesDir = WCU_TABLES_DIR) {
        importClassWcu('noClassNameHere', $tablesDir. $tableName. '.php');
        $this->_tables[$tableName] = tableWcu::_($tableName);
    }
    /**
     * public alias for _extractTables method
     * @see _extractTables
     */
    public function extractTables($tablesDir) {
        if(!empty($tablesDir))
            $this->_extractTables($tablesDir);
    }
    public function exec() {
        /**
         * @deprecated
         */
        /*if(!empty($this->_modules)) {
            foreach($this->_modules as $mod) {
                $mod->exec();
            }
        }*/
    }
    public function getTables () {
        return $this->_tables;
    }
    /**
     * Return table by name
     * @param string $tableName table name in database
     * @return object table
     * @example frameWcu::_()->getTable('products')->getAll()
     */
    public function getTable($tableName) {
        if(empty($this->_tables[$tableName])) {
            $this->_extractTable($tableName);
        }
        return $this->_tables[$tableName];
    }
    public function getModules($filter = array()) {
        $res = array();
        if(empty($filter))
            $res = $this->_modules;
        else {
            foreach($this->_modules as $code => $mod) {
                if(isset($filter['type'])) {
                    if(is_numeric($filter['type']) && $filter['type'] == $mod->getTypeID())
                        $res[$code] = $mod;
                    elseif($filter['type'] == $mod->getType())
                        $res[$code] = $mod;
                }
            }
        }
        return $res;
    }
    
    public function getModule($code) {
        return (isset($this->_modules[$code]) ? $this->_modules[$code] : NULL);
    }
    public function inPlugin() {
        return $this->_inPlugin;
    }
	public function usePackAssets() {
		if(!$this->_useFootAssets && $this->getModule('options') && $this->getModule('options')->get('foot_assets')) {
			$this->_useFootAssets = true;
		}
		return $this->_useFootAssets;
	}
    /**
     * Push data to script array to use it all in addScripts method
     * @see wp_enqueue_script definition
     */
    public function addScript($handle, $src = '', $deps = array(), $ver = false, $in_footer = false, $vars = array()) {
		$src = empty($src) ? $src : uriWcu::_($src);
		if(!$ver)
			$ver = WCU_VERSION;
        if($this->_scriptsInitialized) {
            wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
        } else {
            $this->_scripts[] = array(
                'handle' => $handle, 
                'src' => $src, 
                'deps' => $deps, 
                'ver' => $ver, 
                'in_footer' => $in_footer,
                'vars' => $vars
            );
        }
    }
    /**
     * Add all scripts from _scripts array to worwcuess
     */
    public function addScripts() {
        if(!empty($this->_scripts)) {
            foreach($this->_scripts as $s) {
                wp_enqueue_script($s['handle'], $s['src'], $s['deps'], $s['ver'], $s['in_footer']);
                
                if($s['vars'] || isset($this->_scriptsVars[$s['handle']])) {
                    $vars = array();
                    if($s['vars'])
                        $vars = $s['vars'];
                    if($this->_scriptsVars[$s['handle']])
                        $vars = array_merge($vars, $this->_scriptsVars[$s['handle']]);
                    if($vars) {
                        foreach($vars as $k => $v) {
                            wp_localize_script($s['handle'], $k, $v);
                        }
                    }
                }
            }
        }
        $this->_scriptsInitialized = true;
    }

	public function addJSVar( $script, $name, $val ) {
		if ( $this->_scriptsInitialized ) {
			if ( is_array( $val ) ) {
				wp_localize_script( $script, $name, $val );
			} else {
				$code = "var {$name} = '{$val}';";
				wp_add_inline_script( $script, $code, 'before' );
			}
		} else {
			$this->_scriptsVars[ $script ][ $name ] = $val;
		}
	}
    
    public function addStyle($handle, $src = false, $deps = array(), $ver = false, $media = 'all') {
		$src = empty($src) ? $src : uriWcu::_($src);
		if(!$ver)
			$ver = WCU_VERSION;
		if($this->_stylesInitialized) {
			wp_enqueue_style($handle, $src, $deps, $ver, $media);
		} else {
			$this->_styles[] = array(
				'handle' => $handle,
				'src' => $src,
				'deps' => $deps,
				'ver' => $ver,
				'media' => $media 
			);
		}
    }
    public function addStyles() {
        if(!empty($this->_styles)) {
            foreach($this->_styles as $s) {
                wp_enqueue_style($s['handle'], $s['src'], $s['deps'], $s['ver'], $s['media']);
            }
        }
		$this->_stylesInitialized = true;
    }
    //Very interesting thing going here.............
    public function loadPlugins() {
        require_once(ABSPATH. 'wp-includes/pluggable.php'); 
    }
    public function loadWPSettings() {
        require_once(ABSPATH. 'wp-settings.php'); 
    }
	public function loadLocale() {
		require_once(ABSPATH. 'wp-includes/locale.php'); 
	}
    public function moduleActive($code) {
        return isset($this->_modules[$code]);
    }
    public function moduleExists($code) {
        if($this->moduleActive($code))
            return true;
        return isset($this->_allModules[$code]);
    }
    public function isTplEditor() {
        $tplEditor = reqWcu::getVar('tplEditor');
        return (bool) $tplEditor;
    }
	/**
	 * This is custom method for each plugin and should be modified if you create copy from this instance.
	 */
	public function isAdminPlugOptsPage() {
		$page = reqWcu::getVar('page');
		$tab = reqWcu::getVar('tab');

		if(is_admin() && !empty($page)
			&& (strpos($page, frameWcu::_()->getModule('adminmenu')->getMainSlug()) !== false
				|| (strpos($page, 'wc-settings') !== false && !empty($tab) && strpos($tab, frameWcu::_()->getModule('currency')->getCurrencyTabSlug()) !== false))
		) {
			return true;
		}
		return false;
	}
	public function isAdminPlugPage() {
		if($this->isAdminPlugOptsPage()) {
			return true;
		}
		return false;
	}
	public function licenseDeactivated() {
		return (!$this->getModule('license') && $this->moduleExists('license'));
	}
	public function savePluginActivationErrors() {
		update_option(WCU_CODE. '_plugin_activation_errors',  ob_get_contents());
	}
	public function getActivationErrors() {
		return get_option(WCU_CODE. '_plugin_activation_errors');
	}
	
	public function getOrder( $id ) {
		if (!isset($this->_orders[$id])) {
			if (!function_exists('wc_get_order')) {
				return false;
			}
			$this->_orders[$id] = wc_get_order($id);
		}
		return $this->_orders[$id];
	}
}
