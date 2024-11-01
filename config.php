<?php
    global $wpdb;
    if (!defined('WPLANG') || WPLANG == '') {
        define('WCU_WPLANG', 'en_GB');
    } else {
        define('WCU_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('WCU_PLUG_NAME', basename(dirname(__FILE__)));
    define('WCU_DIR', WP_PLUGIN_DIR. DS. WCU_PLUG_NAME. DS);
    define('WCU_TPL_DIR', WCU_DIR. 'tpl'. DS);
    define('WCU_CLASSES_DIR', WCU_DIR. 'classes'. DS);
    define('WCU_TABLES_DIR', WCU_CLASSES_DIR. 'tables'. DS);
	define('WCU_HELPERS_DIR', WCU_CLASSES_DIR. 'helpers'. DS);
    define('WCU_LANG_DIR', WCU_DIR. 'languages'. DS);
    define('WCU_IMG_DIR', WCU_DIR. 'img'. DS);
    define('WCU_TEMPLATES_DIR', WCU_DIR. 'templates'. DS);
    define('WCU_MODULES_DIR', WCU_DIR. 'modules'. DS);
    define('WCU_FILES_DIR', WCU_DIR. 'files'. DS);
    define('WCU_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

	define('WCU_PLUGINS_URL', plugins_url());
    define('WCU_SITE_URL', get_bloginfo('wpurl'). '/');
    define('WCU_JS_PATH', WCU_PLUGINS_URL. '/'. WCU_PLUG_NAME. '/js/');
    define('WCU_CSS_PATH', WCU_PLUGINS_URL. '/'. WCU_PLUG_NAME. '/css/');
    define('WCU_IMG_PATH', WCU_PLUGINS_URL. '/'. WCU_PLUG_NAME. '/img/');
    define('WCU_MODULES_PATH', WCU_PLUGINS_URL. '/'. WCU_PLUG_NAME. '/modules/');
    define('WCU_TEMPLATES_PATH', WCU_PLUGINS_URL. '/'. WCU_PLUG_NAME. '/templates/');
    define('WCU_JS_DIR', WCU_DIR. 'js/');

    define('WCU_URL', WCU_SITE_URL);

    define('WCU_LOADER_IMG', WCU_IMG_PATH. 'loading.gif');
	define('WCU_TIME_FORMAT', 'H:i:s');
    define('WCU_DATE_DL', '/');
    define('WCU_DATE_FORMAT', 'm/d/Y');
    define('WCU_DATE_FORMAT_HIS', 'm/d/Y ('. WCU_TIME_FORMAT. ')');
    define('WCU_DATE_FORMAT_JS', 'mm/dd/yy');
    define('WCU_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('WCU_WPDB_PREF', $wpdb->prefix);
    define('WCU_DB_PREF', 'wcu_');
    define('WCU_MAIN_FILE', 'wcu.php');

    define('WCU_DEFAULT', 'default');
    define('WCU_CURRENT', 'current');

	define('WCU_EOL', "\n");

    define('WCU_PLUGIN_INSTALLED', true);
    define('WCU_VERSION', '2.0.9');
	define('WCU_DEV_VER', 1);
    define('WCU_USER', 'user');

    define('WCU_CLASS_PREFIX', 'wcuc');
    define('WCU_FREE_VERSION', false);
	define('WCU_TEST_MODE', true);

    define('WCU_SUCCESS', 'Success');
    define('WCU_FAILED', 'Failed');
	define('WCU_ERRORS', 'wcuErrors');

	define('WCU_ADMIN',	'admin');
	define('WCU_LOGGED','logged');
	define('WCU_GUEST',	'guest');

	define('WCU_ALL',		'all');

	define('WCU_METHODS',		'methods');
	define('WCU_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code
	 */
	define('WCU_CODE', 'wcu');

	define('WCU_LANG_CODE', 'woo-currency');
	/**
	 * Plugin name
	 */
	define('WCU_WP_PLUGIN_NAME', 'WooCurrency');
	/**
	 * Allow minification
	 */
	define('WCU_MINIFY_ASSETS', false);
	/**
	 * Custom defined for plugin
	 */
	define('WCU_COMMON', 'common');
	define('WCU_FB_LIKE', 'fb_like');
	define('WCU_VIDEO', 'video');
	define('WCU_HOME_PAGE_ID', 0);

    define('WCU_SHORTCODE_FRONTEND_SWITCHER', 'woo-currency-frontend-switcher');
    define('WCU_SHORTCODE_FRONTEND_SWITCHER_EXTENDED', 'woo-currency-frontend-switcher-extended');
	define('WCU_SHORTCODE_SWITCHER', 'woo-currency-switcher');
	define('WCU_SHORTCODE_CONVERTER', 'woo-currency-converter');
	define('WCU_SHORTCODE_RATES', 'woo-currency-rates');

	define('WCU_DEFAULT_CURRENCY', 'USD');
