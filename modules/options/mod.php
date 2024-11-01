<?php
class optionsWcu extends moduleWcu {
	private $_tabs = array();
	private $_options = array();
	private $_optionsToCategoires = array();	// For faster search
	
	public function init() {
		//dispatcherWcu::addAction('afterModulesInit', array($this, 'initAllOptValues'));
		add_action('init', array($this, 'initAllOptValues'), 99);	// It should be init after all languages was inited (frame::connectLang)
		dispatcherWcu::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function initAllOptValues() {
		// Just to make sure - that we loaded all default options values
		$this->getAll();
	}
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($code) {
        return $this->getModel()->get($code);
    }
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($code) {
		return $this->getModel()->isEmpty($code);
	}
	public function getAllowedPublicOptions() {
		$allowKeys = array('add_love_link', 'disable_autosave');
		$res = array();
		foreach($allowKeys as $k) {
			$res[ $k ] = $this->get($k);
		}
		return $res;
	}
	public function getAdminPage() {
		if(!installerWcu::isUsed()) {
			installerWcu::setUsed();	// Show this welcome page - only one time
			frameWcu::_()->getModule('promo')->getModel()->bigStatAdd('Welcome Show');
			frameWcu::_()->getModule('options')->getModel()->save('plug_welcome_show', time());	// Remember this
		}/* else {
			 * // No actually welcome page for now
			return frameWcu::_()->getModule('promo')->showWelcomePage();
		}*/
		return $this->getView()->getAdminPage();
	}
	public function addAdminTab($tabs) {
		$tabs['settings'] = array(
			'label' => __('Settings', WCU_LANG_CODE), 'callback' => array($this, 'getSettingsTabContent'), 'fa_icon' => 'fa-gear', 'sort_order' => 30,
		);
		return $tabs;
	}
	public function getSettingsTabContent() {
		return $this->getView()->getSettingsTabContent();
	}
	public function getTabs() {
		if(empty($this->_tabs)) {
			$this->_tabs = dispatcherWcu::applyFilters('mainAdminTabs', array(
				//'main_page' => array('label' => __('Main Page', WCU_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'wp_icon' => 'dashicons-admin-home', 'sort_order' => 0),
			));
			foreach($this->_tabs as $tabKey => $tab) {
				if(!isset($this->_tabs[ $tabKey ]['url'])) {
					$this->_tabs[ $tabKey ]['url'] = $this->getTabUrl( $tabKey );
				}
			}
			uasort($this->_tabs, array($this, 'sortTabsClb'));
		}
		return $this->_tabs;
	}
	public function sortTabsClb($a, $b) {
		if(isset($a['sort_order']) && isset($b['sort_order'])) {
			if($a['sort_order'] > $b['sort_order'])
				return 1;
			if($a['sort_order'] < $b['sort_order'])
				return -1;
		}
		return 0;
	}
	public function getTab($tabKey) {
		$this->getTabs();
		return isset($this->_tabs[ $tabKey ]) ? $this->_tabs[ $tabKey ] : false;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getActiveTab() {
		$reqTab = sanitize_text_field(reqWcu::getVar('tab'));
		return empty($reqTab) ? 'settings' : $reqTab;
	}
	public function getTabUrl($tab = '') {
		static $mainUrl;
		if(empty($mainUrl)) {
			$mainUrl = frameWcu::_()->getModule('adminmenu')->getMainLink();
		}
		return empty($tab) ? $mainUrl : $mainUrl. '&tab='. $tab;
	}
	public function getRolesList() {
		if(!function_exists('get_editable_roles')) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}
		return get_editable_roles();
	}
	public function getAvailableUserRolesSelect() {
		$rolesList = $this->getRolesList();
		$rolesListForSelect = array();
		foreach($rolesList as $rKey => $rData) {
			$rolesListForSelect[ $rKey ] = $rData['name'];
		}
		return $rolesListForSelect;
	}
	public function getAll() {
		if(empty($this->_options)) {
			$defSendmailPath = @ini_get('sendmail_path');
			 if (empty($defSendmailPath) && !stristr($defSendmailPath, 'sendmail')) {
				$defSendmailPath = '/usr/sbin/sendmail';
			}
			$this->_options = dispatcherWcu::applyFilters('optionsDefine', array(
				'general' => array(
					'label' => __('General', WCU_LANG_CODE),
					'opts' => array(
						//'send_stats' => array('label' => __('Send usage statistics', WCU_LANG_CODE), 'desc' => __('Send information about what plugin options you prefer to use, this will help us make our solution better for You.', WCU_LANG_CODE), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						//'use_local_cdn' => array('label' => __('Disable CDN usage', PPS_LANG_CODE), 'desc' => esc_html(sprintf(__('By default our plugin is using CDN server to store there part of it\'s files - images, javascript and CSS libraries. This was designed in that way to reduce plugin size, make it lighter and easier for usage. But if you need to store all files - on your server - you can disable this option here, then upload plugin CDN files to your own site. To make it work correctly - check our article that describe how you need to do this <a href="%s" target="_blank">here</a>.', PPS_LANG_CODE), 'https://woobewoo.com/disable-cdn-usage/')), 'def' => '0', 'html' => 'checkboxHiddenVal'),
						'access_roles' => array('label' => __('User role can use plugin', WCU_LANG_CODE), 'desc' => __('User with next roles will have access to whole plugin from admin area.', WCU_LANG_CODE), 'def' => 'administrator', 'html' => 'selectlist', 'options' => array($this, 'getAvailableUserRolesSelect'), 'pro' => '', 'attrs' => 'style="display: none;"'),
					),
				),
			));
			$isPro = frameWcu::_()->getModule('promo')->isPro();
			foreach($this->_options as $catKey => $cData) {
				foreach($cData['opts'] as $optKey => $opt) {
					$this->_optionsToCategoires[ $optKey ] = $catKey;
					if(isset($opt['pro']) && !$isPro) {
						$this->_options[ $catKey ]['opts'][ $optKey ]['pro'] = frameWcu::_()->getModule('promo')->generateMainLink('utm_source=plugin&utm_medium='. $optKey. '&utm_campaign=woo-currency');
					}
				}
			}
			$this->getModel()->fillInValues( $this->_options );
		}
		return $this->_options;
	}
	public function getFullCat($cat) {
		$this->getAll();
		return isset($this->_options[ $cat ]) ? $this->_options[ $cat ] : false;
	}
	public function getCatOpts($cat) {
		$opts = $this->getFullCat($cat);
		return $opts ? $opts['opts'] : false;
	}
}

