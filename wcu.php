<?php
/**
 * Plugin Name: WBW Currency Switcher for WooCommerce
 * Description: WBW Currency Switcher for WooCommerce allows the customers switch products prices to any currencies. Get rates converted in the real time with dynamic currency switcher
 * Version: 2.0.9
 * Author: woobewoo
 * Author URI: https://woobewoo.com
 * WC requires at least: 3.4.0
 * WC tested up to: 9.3.3
 * Text Domain: woo-currency
 * Domain Path: /languages
 **/
	/**
	 * Base config constants and functions
	 */
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
	add_action( 'before_woocommerce_init', function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	} );
	/**
	 * Connect all required core classes
	 */
    importClassWcu('dbWcu');
    importClassWcu('installerWcu');
    importClassWcu('baseObjectWcu');
    importClassWcu('moduleWcu');
    importClassWcu('modelWcu');
    importClassWcu('viewWcu');
    importClassWcu('controllerWcu');
    importClassWcu('helperWcu');
    importClassWcu('dispatcherWcu');
    importClassWcu('fieldWcu');
    importClassWcu('tableWcu');
    importClassWcu('frameWcu');
	/**
	 * @deprecated since version 1.0.1
	 */
    importClassWcu('langWcu');
    importClassWcu('reqWcu');
    importClassWcu('uriWcu');
    importClassWcu('htmlWcu');
    importClassWcu('responseWcu');
    importClassWcu('fieldAdapterWcu');
    importClassWcu('validatorWcu');
    importClassWcu('errorsWcu');
    importClassWcu('utilsWcu');
    importClassWcu('modInstallerWcu');
	importClassWcu('installerDbUpdaterWcu');
	importClassWcu('dateWcu');
	/**
	 * Check plugin version - maybe we need to update database, and check global errors in request
	 */
    installerWcu::update();
    errorsWcu::init();
    /**
	 * Start application
	 */
    frameWcu::_()->parseRoute();
    frameWcu::_()->init();
    frameWcu::_()->exec();

	//var_dump(frameWcu::_()->getActivationErrors()); exit();
