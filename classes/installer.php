<?php
class installerWcu {
	static public $update_to_version_method = '';
	static private $_firstTimeActivated = false;
	static public function init( $isUpdate = false ) {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. WCU_DB_PREF. 'db_version', 0);
		if(!$current_version)
			self::$_firstTimeActivated = true;
		/**
		 * modules 
		 */
		if (!dbWcu::exist("@__modules")) {
			dbDelta(dbWcu::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `code` varchar(32) NOT NULL,
			  `active` tinyint(1) NOT NULL DEFAULT '0',
			  `type_id` tinyint(1) NOT NULL DEFAULT '0',
			  `label` varchar(64) DEFAULT NULL,
			  `ex_plug_dir` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8;"));
			dbWcu::query("INSERT INTO `@__modules` (id, code, active, type_id, label) VALUES
				(NULL, 'adminmenu',1,1,'Admin Menu'),
				(NULL, 'options',1,1,'Options'),
				(NULL, 'user',1,1,'Users'),
				(NULL, 'pages',1,1,'Pages'),
				(NULL, 'templates',1,1,'Templates'),
				(NULL, 'promo',1,1,'Promo'),
				(NULL, 'admin_nav',1,1,'Admin Nav'),
				(NULL, 'mail',1,1,'Mail'),
				(NULL, 'currency',1,1,'Currency'),
				(NULL, 'currency_switcher',1,1,'Currency Switcher'),
				(NULL, 'currency_widget',1,1,'Currency Widget');");
		}
		/**
		 *  modules_type 
		 */
		if(!dbWcu::exist("@__modules_type")) {
			dbDelta(dbWcu::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules_type` (
			  `id` smallint(3) NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;"));
			dbWcu::query("INSERT INTO `@__modules_type` VALUES
				(1,'system'),
				(6,'addons');");
		}
		/**
		* Plugin usage statistics
		*/
		if(!dbWcu::exist("@__usage_stat")) {
			dbDelta(dbWcu::prepareQuery("CREATE TABLE `@__usage_stat` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `visits` int(11) NOT NULL DEFAULT '0',
			  `spent_time` int(11) NOT NULL DEFAULT '0',
			  `modify_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE INDEX `code` (`code`),
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8"));
			dbWcu::query("INSERT INTO `@__usage_stat` (code, visits) VALUES ('installed', 1)");
		}
		
		$wcuOrdersCurrencies = get_option(WCU_DB_PREF. 'orders_currencies');
		if (!empty($wcuOrdersCurrencies)) {
			$wcuOrdersCurrencies = json_decode($wcuOrdersCurrencies, true);
			foreach ($wcuOrdersCurrencies as $orderId => $wcuCurrency) {
				$order = wc_get_order($orderId);
				if ($order) {
					$order->set_currency($wcuCurrency);
				}
				//update_post_meta($orderId, '_order_currency', $wcuCurrency);
			}
			delete_option(WCU_DB_PREF. 'orders_currencies');
		}
	}

	static public function setUsed() {
		update_option(WCU_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		//return true;	// No welcome page for now
		return (int) get_option(WCU_DB_PREF. 'plug_was_used');
	}
	static public function delete() {
		self::_checkSendStat('delete');
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.WCU_DB_PREF."modules`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.WCU_DB_PREF."modules_type`");
		$wpdb->query("DROP TABLE IF EXISTS `".$wpPrefix.WCU_DB_PREF."usage_stat`");
		delete_option($wpPrefix. WCU_DB_PREF. 'db_version');
		delete_option($wpPrefix. WCU_DB_PREF. 'db_installed');
		delete_option(WCU_DB_PREF. 'orders_currencies');
	}
	static public function deactivate() {
		self::_checkSendStat('deactivate');
		self::_updateOrdersCurrency();
	}
	static private function _checkSendStat($statCode) {
		/*if(class_exists('frameWcu')
			&& frameWcu::_()->getModule('promo')
			&& frameWcu::_()->getModule('options')
		) {
			frameWcu::_()->getModule('promo')->getModel()->saveUsageStat( $statCode );
			frameWcu::_()->getModule('promo')->getModel()->checkAndSend( true );
		}*/
	}
	static private function _updateOrdersCurrency() {
		$wcuCurrency = get_option('woocommerce_currency', 'USD');
		if (function_exists('wc_get_orders')) {
			$oldCurrencies = array();
			$orders = wc_get_orders(array(
				'posts_per_page' => -1,
				'type' => 'shop_order',
				'return' => 'ids'
			));
			
			if (!empty($orders)) {
				foreach ($orders as $orderId) {
					$order = wc_get_order($orderId);
					if ($order) {
						$oldCurrencies[$orderId] = $order->get_currency();
						$order->set_currency($wcuCurrency);
					}
					//$oldCurrencies[$orderId] = get_post_meta($orderId, '_order_currency', true);
					//update_post_meta($orderId, '_order_currency', $wcuCurrency);
				}
			}
			
			if (!empty($oldCurrencies)) {
				update_option(WCU_DB_PREF. 'orders_currencies', json_encode($oldCurrencies, JSON_UNESCAPED_UNICODE));
			}
		}
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$currentVersion = get_option($wpPrefix. WCU_DB_PREF. 'db_version', 0);
		if(!$currentVersion || version_compare(WCU_VERSION, $currentVersion, '>')) {
			self::init( true );
			update_option($wpPrefix. WCU_DB_PREF. 'db_version', WCU_VERSION);
		}
	}
}
