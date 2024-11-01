<?php
class currencyWcu extends moduleWcu {

	public $currencyTabSlug = 'wcu_currency';
	// it is better to use wp_options because woocommerce hooks need to already created table to try to get Currency Tab content
	public $currencyDbOpt = 'wcu_currencies';
	public $currencyDbOptGeo = 'wcu_currencies_geo';
	public $optionsDbOpt = 'wcu_options';
	public $optionsDbOptPro = 'wcu_options_pro';
	public $currencyCookieName = 'wcu_current_currency';

	public $defaultCurrency = WCU_DEFAULT_CURRENCY;
	public $currentCurrency = WCU_DEFAULT_CURRENCY;

	public $currencyNames = array();
	public $currencyPositions = array();
	public $currencySymbols = array();
	public $currencyUserSymbols = array();
	public $nowCalcCartTotals = false;
	public $nowCalcOrderTotals = false;

	public $decimalSep = '.';
	public $thousandsSep = ',';
	public $priceNumDecimals = 2;
	public $priceNumDecimalsCrypto = 8;
	public $optionsPro;
	public $convertByCheckout = false;
	
	public $isYithProductAddon = false;
	public $customCache = null;

	public static $orderId = null;
	public static $orderCurrency = null;

	public function init() {
		parent::init();

		$this->initCurrency();

		if ($optionsProModule = frameWcu::_()->getModule('options_pro')) {
			$this->optionsPro = $optionsProModule->getModel()->getOptionsPro();
		}

		$options = $this->getModel()->getOptions();
		if(isset($options['options']['convert_checkout']) && $options['options']['convert_checkout'] == '1') {
			$this->convertByCheckout = true;
		}
		
		add_filter('wp_head', array($this, 'headerActions'), 9999);
		add_filter('wc_price_args', array($this, 'setPriceArgs'), 9999, 1);

		add_filter('woocommerce_get_settings_general', array($this, 'updateGeneralTabContent'), 9999);
		add_action('woocommerce_settings_tabs_array', array($this, 'updateSettingsTabs'), 9999);
		add_action('woocommerce_settings_tabs_wcu_currency', array($this, 'getCurrencyTabContent'), 9999);

		add_filter('woocommerce_currency', array($this, 'getCurrentCurrency'), 9999);
		add_filter('woocommerce_currency_symbol', array($this, 'getCurrencySymbol'), 9999);
		add_filter('wc_get_price_thousand_separator', array($this, 'getCurrencyThousandSeparator'), 9999);
		add_filter('wc_get_price_decimal_separator', array($this, 'getCurrencyDecimalSeparator'), 9999);

		//add_filter('pre_option_woocommerce_price_num_decimals', array($this, 'getPriceDecimalsCount'));
		add_filter('wc_get_price_decimals', array($this, 'getPriceDecimalsCount'));

		add_action('woocommerce_blocks_loaded', array($this, 'addWoocommerceBlocksHooks'));

		if (!empty($_GET['wc-ajax']) && ($_GET['wc-ajax'] == 'ppc-create-order')) {
			add_filter('woocommerce_cart_get_total', array($this, 'getCurrencyPriceCart'), 9999);
		}
		
		add_filter('raw_woocommerce_price', array($this, 'getCurrencyPrice'), 9999, 2);
		add_filter('woocommerce_price_filter_widget_min_amount', array($this, 'getCurrencyPriceMinWidget'), 9999);
		add_filter('woocommerce_price_filter_widget_max_amount', array($this, 'getCurrencyPriceMaxWidget'), 9999);

		add_filter('woocommerce_order_get_total', array($this, 'getTotalCurrencyPrice'), 9999, 2);
		add_action('woocommerce_email_header', array($this, 'removeConvertTotalPrice'), 10);

		add_action('woocommerce_before_calculate_totals', array($this, 'beforeCartTotals'), 9999, 2);
		add_action('woocommerce_after_calculate_totals', array($this, 'afterCartTotals'), 9999);

		add_action('woocommerce_before_checkout_process', array($this, 'beforeOrderTotals'), 9999);
		add_action('woocommerce_checkout_order_processed', array($this, 'afterOrderTotals'), 9999);

		if(isset($_GET['startcheckout'])) {
			add_filter('woocommerce_calculated_total', array($this, 'getCurrencyPrice'), 9999);
		}
		if($this->convertByCheckout) {
			add_filter('woocommerce_paypal_express_checkout_get_details', array($this, 'recalcPaypalExpressAmounts'), 9999);
			add_action('woocommerce_checkout_order_processed', array($this, 'controlPayPalSupportedCurrencies'), 9999);
            // convert for payment_paynet
			add_action('woocommerce_checkout_order_processed', array($this, 'convertCustomCurrencies'), 9999);
		}
		add_filter('wpg_request_param', array($this, 'recalcWpgAmounts'), 9999);

		add_filter('woocommerce_price_format', array($this, 'getCurrencyPriceFormat'), 9999);
		
		// filter for tm-woo-extra-product-options
		add_filter('wc_epo_get_element_for_display', array($this, 'getCurrencyPriceForTMExtraProductOptions'), 9999, 1);
		add_filter('woocommerce_tm_epo_price_on_cart', array($this, 'getCurrencyPriceForCartTMExtraProductOptions'), 9999, 1);
		add_filter('wc_epo_product_price', array($this, 'getCurrencyPrice'), 9999, 1);
		
		add_filter('woocommerce_available_variation', array($this, 'setCorrectVariationPrices'), 9999, 1);
		
		add_filter('woocommerce_get_variation_regular_price', array($this, 'getCurrencyPrice'), 9999, 4);
		add_filter('woocommerce_get_variation_sale_price', array($this, 'getCurrencyPrice'), 9999, 4);
		add_filter('woocommerce_variation_prices', array($this, 'getCurrencyVariationPrices'), 9999, 3);
		add_filter('woocommerce_before_add_to_cart_form', array($this, 'getCurrencyVariationPrices'), 9999, 3);

		add_filter('woocommerce_admin_order_preview_line_items', array($this, 'setCorrectOrderCurrency'), 9999, 2);

		add_filter('wc_get_template', array($this, 'updateCurrencyForEmailTemplateOrder'), 9999, 5);				// from woocommerce 2.7 it is necessary for new order email
		add_action('wpo_wcpdf_process_template_order', array($this, 'updateCurrencyForPdfTemplateOrder'), 1, 2);	// compatibility for https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/
		add_action('woocommerce_order_get_currency', array($this, 'getOrderCurrency'), 1, 2);						// callback for woocommerce get_order_currency() function
		add_filter('woocommerce_checkout_update_order_review', array($this, 'updateCheckoutOrderReview'), 9999);	// callback for ajax recalc of order review on checkout

		add_filter('woocommerce_get_formatted_order_total', array($this, 'getCurrencyOrderTotal'));	// callback for ajax recalc of order review on checkout

		add_filter('woocommerce_before_resend_order_emails', array($this, 'woocommerceBeforeResendOrderEmails'), 1);
		add_filter('woocommerce_email_actions', array($this, 'checkWoocommerceEmailActions'), 10);

		add_action('the_post', array($this, 'checkThePostOrder'), 1);
		add_action('load-post.php', array($this, 'checkAdminActionPostOrder'), 1);

		if (class_exists('Printful_Shipping')) {
			add_filter('http_request_args', array($this, 'checkPrintfulHttpRequestArgs'), 10, 2);
			add_action('woocommerce_checkout_order_processed', array($this, 'controlPrintfulCurrencies'), 9999);
		}

		dispatcherWcu::addAction('getChildrenMultipleTab', array($this->getView(), 'getChildrenMultipleTab'), 10, 1);
		dispatcherWcu::addAction('getChildrenOneTab', array($this->getView(), 'getChildrenOneTab'), 10, 1);

		add_action('widgets_init', array($this, 'overrideWcWidgetLayeredNavFilters'), 11);

		if ( ! is_admin() && $this->optionsPro ) {
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'loadProductsFilter' ) );
			add_action( 'woocommerce_product_query', array( $this, 'loadProductsFilter' ) );
			add_filter( 'pre_handle_404', array( $this, 'checkAvailabilityForCountry' ), 10, 2 );
		}

		// woo-payment-gateway plugin doesn't use raw_woocommerce_price filter when getting total price
		if ( !function_exists( 'is_plugin_active' ) ) {
            include_once (ABSPATH . 'wp-admin/includes/plugin.php');
		}
		if ( is_plugin_active( 'woo-payment-gateway/braintree-payments.php' ) ) {
			add_filter( 'wc_braintree_output_display_items', array( $this, 'getBraintreeCurrencyPrice' ), 9999 );
		}

		add_filter( 'woocommerce_format_localized_price', array( $this, 'getFormatLocalizedPrice' ), 10, 2 );

		add_filter( 'wcu_get_currencies_data', array( $this, 'setOrderRate' ) );
		
		if (!is_admin()) {
			add_action( 'woocommerce_before_mini_cart_contents', array( $this, 'addCompatibilityMiniCart' ) );
			add_action( 'woocommerce_mini_cart_contents', array( $this, 'removeCompatibilityMiniCart' ) );
		}
		
		// for Yith Product Add-ons & Extra Options
		add_action('yith_wapo_before_main_container', array($this, 'enableYithAddonConverter'));
		add_action('yith_wapo_after_main_container', array($this, 'disableYithAddonConverter'));
		add_filter('yith_wapo_product_price', array($this, 'getCurrencyPrice'), 9999, 1);
		add_filter('yith_wapo_get_addon_price', array($this, 'getYithAddonOptionPrice'), 9999, 1);
		add_filter('yith_wapo_option_price', array($this, 'resetYithAddonOptionPrice'), 9999, 1);
		
		add_filter('woocommerce_hydration_dispatch_request', array($this, 'restApiRequest'), 9999, 4);
	}
	
	public function enableYithAddonConverter() {
		$this->isYithProductAddon = true;
	}
	public function disableYithAddonConverter() {
		$this->isYithProductAddon = false;
	}
	public function getYithAddonOptionPrice( $price ) {
		if ($this->isYithProductAddon) {
			$this->customCache = $price;
			return $this->getModel()->getCurrencyPrice($price, null);
		}
		return $price;
	}
	public function resetYithAddonOptionPrice( $price ) {
		if ($this->isYithProductAddon && !empty($price) && !empty($this->customCache) && !is_null($this->customCache)) {
			$price = $this->customCache;
			$this->customCache = null;
			return $price;
		}
		return $price;
	}


	function getBraintreeCurrencyPrice( $data ) {
		if ( isset( $data['total'] ) ) {
			$data['total'] = $this->getCurrencyPrice( $data['total'] );
		}
		if ( isset( $data['order_total'] ) ) {
			$data['order_total'] = $this->getCurrencyPrice( $data['order_total'] );
		}

		return $data;
	}

	public function getFormatLocalizedPrice( $formated, $price ) {
		global $theorder, $post;
		//if ( 0 != $price && is_admin() && is_object( $theorder ) && is_object( $post ) && $post->post_type == 'shop_order' ) {
		if ( 0 != $price && is_admin() && is_object( $theorder ) && is_object( $post ) && utilsWcu::isOrderType($post->ID) ) {
			$currentCurrency = $this->getCurrentCurrency();
			$orderCurrency   = $theorder->get_currency();
			if ( $currentCurrency !== $orderCurrency ) {
				$this->setCurrentCurrency( $orderCurrency, true );
				$price    = $this->getModel()->getCurrencyPrice( $price, null );
				$formated = str_replace( '.', wc_get_price_decimal_separator(), strval( $price ) );
			}
		}

		return $formated;
	}

	public function setOrderRate( $currencies ) {
		global $theorder;
		$orderId       = null;
		$orderCurrency = null;

		if ( ! is_null( self::$orderId ) && ! is_null( self::$orderCurrency ) ) {
			$orderId       = self::$orderId;
			$orderCurrency = self::$orderCurrency;
		} elseif ( is_object( $theorder ) ) {
			$orderId       = $theorder->get_id();
			$orderCurrency = $theorder->get_currency();
		}
		if ( ! is_null( $orderId ) && ! is_null( $orderCurrency ) ) {
			$rate = utilsWcu::getOrderMeta($orderId, 'wcu_order_rate');
			//$rate = get_post_meta( $orderId, 'wcu_order_rate', true );
			if ( '' !== $rate && isset( $currencies[ $orderCurrency ] ) ) {
				$currencies[ $orderCurrency ]['rate_custom'] = $rate;
			}
		}

		return $currencies;
	}

	function setPriceArgs($args) {
		if ($this->nowCalcOrderTotals) {
			$args['decimals'] = $this->getModel()->_getRealPriceDecimalsCount(empty($args['currency']) ? $this->defaultCurrency : $args['currency']);
		}
		return $args;
	}
	function beforeCartTotals($cart) {
		dispatcherWcu::doAction('setCartItemsPrice', $cart);
		$this->nowCalcCartTotals = true;
	}
	function afterCartTotals() {
		$this->nowCalcCartTotals = false;
	}
	function beforeOrderTotals() {
		$this->nowCalcOrderTotals = true;
	}
	function afterOrderTotals() {
		$this->nowCalcOrderTotals = false;
	}
	public function addCompatibilityMiniCart() {
		if (!empty($_GET['wc-ajax']) && $_GET['wc-ajax'] == 'get_refreshed_fragments') {
			$settings = get_option('wcu_options_pro', array());
        	$settings = isset($settings['manual_prices']) ? $settings['manual_prices'] : array();
        	if (isset($settings['toggle_manual_prices']) && $settings['toggle_manual_prices'] == '1') {
				add_filter('woocommerce_get_price_including_tax', array($this, 'getCurrencyPriceCartProduct'), 9999, 3);
				add_filter('woocommerce_get_price_excluding_tax', array($this, 'getCurrencyPriceCartProduct'), 9999, 3);
			}
		}
	}
	public function removeCompatibilityMiniCart() {
		remove_filter('woocommerce_get_price_including_tax', array($this, 'getCurrencyPriceCartProduct'), 9999, 3);
		remove_filter('woocommerce_get_price_excluding_tax', array($this, 'getCurrencyPriceCartProduct'), 9999, 3);
	}
	
	public function getCurrencyPriceCartProduct( $price, $qty = 1, $product = null ) {
		if (!is_null($product)) {
			$origin = (float) $product->get_price();
			$qty = empty($qty) ? 1 : (float) $qty;
			$newPrice = $price / $qty;
			if ($price == $origin) {
				return $this->getModel()->getCurrencyPrice($price, $product) * $qty;
			}
		}
		return $price;
	}

	function addWoocommerceBlocksHooks() {
		add_filter('woocommerce_get_price_excluding_tax', array($this, 'getCurrencyPriceCart'), 9999);
		add_filter('woocommerce_get_price_including_tax', array($this, 'getCurrencyPriceCart'), 9999);
		add_filter('woocommerce_cart_get_subtotal', array($this, 'getCurrencyPriceCart'), 9999);
		add_filter('woocommerce_cart_get_total', array($this, 'getCurrencyPriceCart'), 9999);
		add_action('woocommerce_calculate_totals', array($this, 'calcLineSubtotal'), 9999);
	}
	public function restApiRequest($response, $request, $path, $handler) {
		if ('/wc/store/v1/cart' == $path && has_block('woocommerce/cart')) {
			WC()->cart->calculate_totals();
		}
		return $response;
	}
	public function calcLineSubtotal( $cart ) {
		if (has_block('woocommerce/cart')) {
			foreach ( $cart->get_cart() as $key => $cartItem ) {
				if (!empty($cartItem['line_subtotal']) && !empty($cart->cart_contents[$key])) {
					$cart->cart_contents[$key]['line_subtotal'] = $this->getModel()->getCurrencyPrice($cartItem['line_subtotal'], null);
				}
			}
		}
	}
	
	public function getCurrencyPriceCart($price) {	
		if (!empty($_GET['wc-ajax']) && ($_GET['wc-ajax'] == 'ppc-create-order')) {
			$payload = file_get_contents( 'php://input' );
			if ($payload) {
				$payloadJson   = json_decode( $payload, true );
				if ($payloadJson && !empty($payloadJson['context']) && ($payloadJson['context'] == 'cart' || $payloadJson['context'] == 'checkout')) {
					return $this->getModel()->getCurrencyPrice($price, null);
				}
			}
		}
    	if (!has_block('woocommerce/cart')) {
        	return $price;
    	}
		return $this->getModel()->getCurrencyPrice($price, null);
	}

	function removeConvertTotalPrice() {
		remove_filter('woocommerce_order_get_total', array($this, 'getTotalCurrencyPrice'), 9999, 2);
	}

	
   function getCurrencyOrderTotal($order_total)
   {
      return $order_total;
   }
	function recalcPaypalExpressAmounts($details)
	{
		$settings = wc_gateway_ppec()->settings;
		$decimals = $settings->get_number_of_decimal_digits();

		$total = 0;
		$model = $this->getModel();
		if(!empty($details['items'])) {
			foreach($details['items'] as $i => $values) {
				$v = $model->getCurrencyPrice($values['amount']);
				$details['items'][$i]['amount'] = round($v, $decimals);
				$total += $v * $values['quantity'];
			}
		}
		$details['total_item_amount'] = round($total, $decimals);
		$tax = $model->getCurrencyPrice($details['order_tax']);
		$details['order_tax'] = round($tax, $decimals);
		$ship = $model->getCurrencyPrice($details['shipping']);
		$details['shipping'] = round($ship, $decimals);
		$details['order_total'] = round($total + $tax + $ship, $decimals);

		return $details;
	}

	function recalcWpgAmounts($params) {
		$model = $this->getModel();
		$total = 0;
		foreach($params as $key => $value) {
			if(substr($key, -3) == 'AMT') {
				$params[$key] = wpg_number_format($model->getCurrencyPrice($value));
			} else if(stripos($key, 'L_PAYMENTREQUEST_0_AMT') === 0) {
				$v = wpg_number_format($model->getCurrencyPrice($value));
				$params[$key] = $v;
				$k = 'L_PAYMENTREQUEST_0_QTY'.str_replace('L_PAYMENTREQUEST_0_AMT', '', $key);
				if(isset($params[$k])) {
					$total += $v * $params[$k];
				}
			}
		}
		$params['PAYMENTREQUEST_0_ITEMAMT'] = wpg_number_format($total);
		$params['PAYMENTREQUEST_0_AMT'] = wpg_number_format($total + $params['PAYMENTREQUEST_0_SHIPPINGAMT'] + $params['PAYMENTREQUEST_0_TAXAMT']);
		//Woo_PayPal_Gateway_Express_Checkout_NVP::log('test'.print_r($params, true));
		return $params;
	}

	public function convertCustomCurrencies( $id ) {
		$order  = wc_get_order( $id );
		$method = $order->get_payment_method();
		if ( $method == 'payment_paynet' ) {
			$currentCurrency = $this->getCurrentCurrency();
			$paymentCurrency = 'TRY';
			$this->setCurrentCurrency( $paymentCurrency, true );
			$paymentTotal = $this->getCurrencyPrice( WC()->cart->get_total( 'edit' ) );

			utilsWcu::updateOrderMeta($id, array('wcu_order_currency' => $paymentCurrency, 'wcu_order_total' => $paymentTotal));
			//update_post_meta( $id, 'wcu_order_currency', $paymentCurrency );
			//update_post_meta( $id, 'wcu_order_total', $paymentTotal);

			$this->setCurrentCurrency( $currentCurrency, true );
		}
	}

	public function controlPayPalSupportedCurrencies($id)
	{
		$currentCurrency = $this->getCurrentCurrency();
		utilsWcu::updateOrderMeta($id, array('wcu_order_currency' => $currentCurrency, 'wcu_order_total' => $this->getCurrencyPrice( WC()->cart->get_total('edit'))));
		//update_post_meta( $id, 'wcu_order_currency', $currentCurrency );
		//update_post_meta( $id, 'wcu_order_total', $this->getCurrencyPrice( WC()->cart->get_total( 'edit' ) ) );
		$options = $this->getModel()->getOptions();
		if ( isset( $options['options']['save_order_rate'] ) && '1' === $options['options']['save_order_rate'] ) {
			$currencies = $this->getCurrencies();
			if ( isset( $currencies[ $currentCurrency ]['rate'] ) ) {
				utilsWcu::updateOrderMeta($id, array('wcu_order_rate' => $currencies[$currentCurrency]['rate']));
				//update_post_meta( $id, 'wcu_order_rate', $currencies[ $currentCurrency ]['rate'] );
			}
		}
		
		$order = wc_get_order($id);
		$method = $order->get_payment_method();
		if($method == 'nmwoo_2co') {
			add_filter('woocommerce_twoco_args', array($this, 'recalcPricesFor2CO'), 99999);
		}
		if($method == 'nmwoo_2co' /*|| strpos($method, 'paypal') !== false*/) {
			if(class_exists('WC_Gateway_Paypal')) {
				$this->resetCurrentCurrency();
				$order->set_currency( $this->getDefaultCurrency() );
				$order->set_total( $this->getCurrencyPrice(WC()->cart->get_total( 'edit' )) );
				$order->save();
			}
		}
	}
	
	public function controlPrintfulCurrencies( $id )
	{
		utilsWcu::updateOrderMeta($id, array('wcu_order_currency' => $this->getCurrentCurrency(), 'wcu_order_total' => $this->getCurrencyPrice(WC()->cart->get_total('edit'))));
		//update_post_meta($id, 'wcu_order_currency', $this->getCurrentCurrency());
		//update_post_meta($id, 'wcu_order_total', $this->getCurrencyPrice(WC()->cart->get_total( 'edit' )));
		
		$order = wc_get_order($id);
		$shippingMethod = $order->get_shipping_method();
		if (false !== stripos($shippingMethod, 'Printful')) {
			$this->resetCurrentCurrency();
			$order->set_currency( $this->getDefaultCurrency() );
			$order->set_total( $this->getCurrencyPrice(WC()->cart->get_total( 'edit' )) );
			$order->save();
		}
	}

	public function recalcPricesFor2CO($twoco_args)
	{
		$model = $this->getModel();
		foreach($twoco_args as $key => $value) {
			if(strpos($key, 'li_') === 0 && strpos($key, '_price') == strlen($key) - 6) {
				$twoco_args[$key] = $model->getCurrencyPrice($value);
			}
		}
		return $twoco_args;
	}
	public function wcuIsWcfmPage() {
			global $post;
			if (!empty($post)) {
				$currentPageId = $post->ID;
				$pages = get_option("wcfm_page_options");
				$wcpage = isset($pages['wc_frontend_manager_page_id']) ? $pages['wc_frontend_manager_page_id'] : 999999;
				if ($wcpage == $currentPageId) {
					return true;
				}
			}
			return false;
	}
	public function headerActions() {
		if (!class_exists('WooCommerce')) {
			return;
		}
		if (!$this->convertByCheckout && (is_checkout() || is_checkout_pay_page() || isset($_GET['startcheckout']))) {
			$this->resetCurrentCurrency(true);
		}
		if (is_order_received_page()) {
			$this->beforeOrderTotals();
		}
	}
	public function initCurrency() {
		$currencies = $this->getCurrencies();

		foreach ($currencies as $key => $currency) {
			if(!empty($currency['etalon'])) {
				$this->defaultCurrency = $key;
				break;
			}
		}
		$this->currentCurrency = $this->defaultCurrency;

		if(!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
			if(isset($_COOKIE[$this->currencyCookieName])) {
				$this->setCurrentCurrency($_COOKIE[$this->currencyCookieName]);
			}
			if(!empty($_GET['currency']) && array_key_exists(strtoupper($_GET['currency']), $currencies)) {
				$this->setCurrentCurrency(strtoupper(utilsWcu::escape($_GET['currency'])));
				add_action('wp_loaded', array($this, 'recalcCart'));
			}
		}

		$this->priceNumDecimals = get_option('woocommerce_price_num_decimals', $this->priceNumDecimals);
	}
	public function recalcCart() {
		if (isset(WC()->cart) && !empty(WC()->cart)) {
			WC()->cart->calculate_totals();
		}
	}

	public function disableWcuByCurrentUrl() {
		$options = $this->getModel()->getOptions();
		$links = !empty($options['options']['disable_uris']) ? $options['options']['disable_uris'] : '';
		if (!empty($links)) {
			$links = preg_split('/[\n\r]+/', $links);
			
			$curLink = explode('/?', $_SERVER['REQUEST_URI']);
			$curLink = $curLink[0];

			if (substr($curLink,-1) !== '/') {
				$curLinkWithSlash = $curLink.'/';
				$curLinkWithoutSlash = $curLink;
			} else {
				$curLinkWithSlash = $curLink;
				$curLinkWithoutSlash = substr($curLink, 0, -1);
			}

			if ( in_array($curLinkWithSlash, $links) || in_array($curLinkWithoutSlash, $links) ) {
				return true;
			}
		}
		return false;
	}

	public function getDefaultCurrency() {
		return $this->defaultCurrency;
	}

	public function getCurrentCurrency() {
		if(!$this->convertByCheckout && ($this->wcuIsWcfmPage() 
			|| isset($_GET['startcheckout']) || (isset($_GET['wc-ajax']) && strpos($_GET['wc-ajax'], 'checkout') !== false))) {
			$this->setCurrentCurrency($this->defaultCurrency, true);
		} else if(!$this->convertByCheckout && in_the_loop() && (is_checkout() || is_checkout_pay_page())) {
			$this->setCurrentCurrency($this->defaultCurrency, true);
		} else if ($this->detectRobot()) {
			$this->setCurrentCurrency($this->defaultCurrency, true);
		} else if(!$this->convertByCheckout && isset($_GET['wc-ajax']) && $_GET['wc-ajax'] == 'ppc-create-order') {
			$this->setCurrentCurrency($this->defaultCurrency, true);
		}
		return $this->currentCurrency;
	}


	public function detectRobot() {
		if (isset($this->optionsPro['geoip_rules']) && !empty($this->optionsPro['geoip_rules']['default_for_robots'])) {
			if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])) {
				return true;
			}
		}
		return false;
	}

	public function getCurrencyCodes() {

		$currencies = $this->getCurrencies();
		$currenciesCodeArray = array();
		foreach ($currencies as $currency) {
			if ($currency['name'] === $this->currentCurrency) continue;
			$currenciesCodeArray[$currency['name']] = $currency['name'];
		}
		return $currenciesCodeArray;
	}

	public function setCurrentCurrency($currency = '', $notCookies = false) {
		if(empty($currency)) {
			$currency = $this->defaultCurrency;
		}
		if(!isset($_COOKIE[$this->currencyCookieName]) || (isset($_COOKIE[$this->currencyCookieName]) && $_COOKIE[$this->currencyCookieName] != $currency)) {
			if (!$notCookies) {
				setcookie('wcu_current_currency', $currency, time() + 1 * 24 * 3600, '/', '', isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])); //1 day
			}
		}
		$this->currentCurrency = $currency;
		
        if (class_exists('GFForms')) {
            update_option('rg_gforms_currency', $currency);
        }
	}
	public function resetCurrentCurrency($notCookies = false) {
		$this->setCurrentCurrency('',$notCookies);
	}
	public function getCurrencySymbol($symbol) {
		global $pagenow, $post;
		//if ($pagenow == 'edit.php' && (get_post_type() == 'shop_order')) {
		if ($pagenow == 'edit.php' && is_object($post) && utilsWcu::isOrderType($post->ID)) {
			return $symbol;
		}
		$currencies = $this->getCurrencies();
		$currencySymbols = $this->getCurrencySymbolsList();

		if(!isset($currencies[$this->currentCurrency])) {
			$this->resetCurrentCurrency();
		}
		return isset($currencies[$this->currentCurrency]) && isset($currencySymbols[$currencies[$this->currentCurrency]['symbol']])
			? $currencySymbols[$currencies[$this->currentCurrency]['symbol']]
			: $symbol;
	}
	public function getCurrencyThousandSeparator($default) {
		$currencies = $this->getCurrencies();
		if (isset($currencies[$this->currentCurrency]) && !empty($currencies[$this->currentCurrency]['tho_separator'])) {
			$default = $currencies[$this->currentCurrency]['tho_separator'];
		}
		return $default;
	}
	public function getCurrencyDecimalSeparator($default) {
		$currencies = $this->getCurrencies();
		if (isset($currencies[$this->currentCurrency]) && !empty($currencies[$this->currentCurrency]['dec_separator'])) {
			$default = $currencies[$this->currentCurrency]['dec_separator'];
		}
		return $default;
	}
	public function getPriceDecimalsCount($default) {
		$this->priceNumDecimals = $this->getModel()->_getPriceDecimalsCount($this->currentCurrency);
		if ( array_key_exists( $this->currentCurrency, $this->getCryptoCurrencyList() ) ) {
			return $this->priceNumDecimalsCrypto;
		}
		if ($this->priceNumDecimals) {
			return $this->priceNumDecimals;
		} elseif ($this->priceNumDecimals === 0) {
			return 0;
		} else {
			return $default;
		}
	}
	public function getCurrencyPriceFormat($format) {
		return $this->getModel()->getCurrencyPriceFormat($format);
	}
	public function getTotalCurrencyPrice($price, $order) {
		if ( is_object( $order ) ) {
			self::$orderId = $order->get_id();
			self::$orderCurrency = $order->get_currency();
			$meta = utilsWcu::getOrderMeta(self::$orderId, '_order_total');
			//$meta          = get_post_meta( self::$orderId, '_order_total', true );
			if ( ! empty( $meta ) ) {
				$price = $meta;
			}
		}

		if ( ( is_order_received_page() || is_account_page() || ( is_admin() && ! isset( $_REQUEST['save'] ) ) || isset( $_REQUEST['st'] ) ) ) {
			return $price;
		}

		return $this->getModel()->getCurrencyPrice($price, null);
	}
	public function getCurrencyPrice($price, $product = null) {
      if (is_checkout_pay_page()) {
         return $price;
      }
		$tooltipModule = frameWcu::_()->getModule('currency_tooltip');
		if ( isset($tooltipModule) ) {
			$tooltipModule->addTooltipHiddenField($price);
		}
		return $this->getModel()->getCurrencyPrice($price, $product);
	}
	public function getCurrencyPriceMinWidget($price) {
		
		return floor($this->getModel()->getCurrencyPrice($price));
	}
	public function getCurrencyPriceMaxWidget($price) {
		
		return ceil($this->getModel()->getCurrencyPrice($price));
	}
	public function getCurrencyVariationPrices($pricesArr) {
		return $this->getModel()->getCurrencyVariationPrices($pricesArr);
	}
	public function getOrderCurrency($orderCurrency, $order) {
		if (!is_ajax() && !is_admin() && is_object($order)) {
			$orderId = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
			$currency = $this->getWcuOrderCurrency($orderId);

			if(!empty($currency)) {
				$this->currentCurrency = $currency;
			}
		}
		return $orderCurrency;
	}
	public function getCurrencyPriceForTMExtraProductOptions($element){
		foreach ($element['options'] as $key => $option) {
			if ( isset( $element['original_rules_filtered'][0] ) && isset( $element['original_rules_filtered'][0][0] ) ) {
				$element['original_rules_filtered'][0][0] = $this->getModel()->getCurrencyPrice($element['original_rules_filtered'][0][0]);
			} else {
				$original_amount = $element['original_rules_filtered'][ esc_attr( $key ) ];
				if ( isset( $original_amount[0] ) ) {
					$element['original_rules_filtered'][ esc_attr( $key ) ][0] = $this->getModel()->getCurrencyPrice($original_amount[0]);
				}
			}
			if ( isset( $element['rules_filtered'][0] ) && isset( $element['rules_filtered'][0][0] ) ) {
				$element['rules_filtered'][0][0] = $this->getModel()->getCurrencyPrice($element['rules_filtered'][0][0]);
			} else {
				$amount = $element['rules_filtered'][ esc_attr( $key ) ];
				if ( isset( $amount[0] ) ) {
					$element['rules_filtered'][ esc_attr( $key ) ][0] = $this->getModel()->getCurrencyPrice($amount[0]);
				}
			}
		}
		return $element;
	}
	public function getCurrencyPriceForCartTMExtraProductOptions($price) {
		return $this->getModel()->getCurrencyPrice($price);
	}
	public function updateCheckoutOrderReview() {
		if(!$this->convertByCheckout) {
			$this->resetCurrentCurrency(true);
		}
	}
    public function woocommerceBeforeResendOrderEmails($order) {
        $order_id = method_exists($order, 'get_id') ? $order->get_id() : $order->id;
	
	    $currency = $this->getWcuOrderCurrency($order_id);
        if (!empty($currency)) {
            $this->setCurrentCurrency($currency);
        }
    }
    public function checkWoocommerceEmailActions($email_actions) {
        global $post;
        //if (is_object($post) AND $post->post_type == 'shop_order') {
		if (is_object($post) AND utilsWcu::isOrderType($post->ID)) {
	        $currency = $this->getWcuOrderCurrency($post->ID);
            if (!empty($currency)) {
                $this->setCurrentCurrency($currency);
            }
        } else {
            if (isset($_POST['order_status']) AND isset($_POST['post_ID'])) {
	            $currency = $this->getWcuOrderCurrency((int) $_POST['post_ID']);
                if (!empty($currency)) {
                    $this->setCurrentCurrency($currency);
                }
            }
        }

        return $email_actions;
    }
    public function setCorrectOrderCurrency($order_items, $order) {
    	$this->setCurrentCurrency($order->get_currency());
        return $order_items;
    }
    public function setCorrectVariationPrices($variation_data) {
		if (isset($variation_data['display_price'])) {
			$variation_data['display_price'] = $this->getModel()->getCurrencyPrice($variation_data['display_price']);
		}
	    if (isset($variation_data['display_regular_price'])) {
		    $variation_data['display_regular_price'] = $this->getModel()->getCurrencyPrice($variation_data['display_regular_price']);
	    }
		
		return $variation_data;
    }
	public function updateCurrencyForEmailTemplateOrder($located, $template_name, $args, $template_path, $default_path) {
		if(isset($args['order'])) {
			if(is_object($args['order']) && !is_null($args['order'])) {
				$order = $args['order'];

				if(substr($template_name, 0, 6) === 'emails') {
					if(method_exists($order, 'get_currency')) {
						$this->setCurrentCurrency($order->get_currency());
						$this->beforeOrderTotals();
					}
				}
			}
		}
		return $located;
	}
	public function updateCurrencyForPdfTemplateOrder($templateType, $orderId) {
		if(!empty($orderId) && is_numeric($orderId)) {
			
			$currency = $this->getWcuOrderCurrency($orderId);

			if(!empty($currency)) {
				$this->currentCurrency = $currency;
				$this->beforeOrderTotals();
			}
		}
	}
	public function addToCartHash($hash) {
		//for normal shipping update if to change currency
		return '';
	}
    public function checkThePostOrder($post) {
        //if (is_object($post) AND $post->post_type == 'shop_order') {
		if (is_object($post) AND utilsWcu::isOrderType($post->ID)) {
	        $currency = $this->getWcuOrderCurrency($post->ID);
            if (!empty($currency)) {
                $this->setCurrentCurrency($currency);
                $this->beforeOrderTotals();
            }
        }

        return $post;
    }
    public function checkAdminActionPostOrder() {
        if (isset($_GET['post'])) {
            $post_id = $_GET['post'];
            $post = get_post($post_id);
            //if (is_object($post) AND $post->post_type == 'shop_order') {
			if (is_object($post) AND utilsWcu::isOrderType($post->ID)) {
	            $currency = $this->getWcuOrderCurrency($post->ID);
                if (!empty($currency)) {
                    $this->setCurrentCurrency($currency);
                    $this->beforeOrderTotals();
                }
            }
        }
    }
    public function checkPrintfulHttpRequestArgs( $args, $url ) {
		if (false !== strpos($url, 'printful.com/shipping/rates')) {
			$body = !is_null($args['body']) ? json_decode($args['body'], true) : null;
			
			if ($body && isset($body['currency'])) {
				$body['currency'] = $this->getDefaultCurrency();
				
				$args['body'] = json_encode( $body );
			}
		}
		
		return $args;
    }
	public function updateGeneralTabContent($settings) {
		// remove currency options from general woocommerce tab: woocommerce_currency, woocommerce_currency_pos
		foreach($settings as $k => $s) {
			if($settings[$k]['id'] == 'woocommerce_currency' || $settings[$k]['id'] == 'woocommerce_currency_pos') {
				unset($settings[$k]);
			}
		}
		$settings = array_values($settings);
		return $settings;
	}
	public function updateSettingsTabs($tabs) {
		$tabs[$this->getCurrencyTabSlug()] = __('Currency', WCU_LANG_CODE);
		return $tabs;
	}
	public function getCurrencyTabContent() {
		// Just make little notices here
		frameWcu::_()->getModule('promo')->getModel()->bigStatAdd('Welcome Show');
		if(!installerWcu::isUsed()) {
			installerWcu::setUsed();	// Show this welcome page - only one time
			frameWcu::_()->getModule('promo')->getModel()->bigStatAdd('Welcome Show');
			frameWcu::_()->getModule('options')->getModel()->save('plug_welcome_show', time());	// Remember this
		}
		$this->getView()->getCurrencyTabContent();
	}
	public function getCurrencies() {
		return $this->getModel()->getCurrencies();
	}
	public function getCurrencyTabSlug() {
		return $this->currencyTabSlug;
	}
	public function getCurrencyTabUrl() {
		return admin_url('admin.php?page=wc-settings&tab=wcu_currency#wcuCurrenciesTab');
	}
	public function getCurrencyNames() {
		if(empty($this->currencyNames)) {
			$this->currencyNames = array_combine(array_keys($this->getCurrencySymbolsList(false)), array_keys($this->getCurrencySymbolsList(false)));
		}
		return $this->currencyNames;
	}
	public function getCurrencyPositions() {
		if(empty($this->currencyPositions)) {
			$this->currencyPositions = array(
				'left' => __('left', WCU_LANG_CODE),
				'right' => __('right', WCU_LANG_CODE),
				'left_space' => __('left space', WCU_LANG_CODE),
				'right_space' => __('right space', WCU_LANG_CODE),
			);
		}
		return $this->currencyPositions;
	}
	public function getCurrencySymbols() {
		if(empty($this->currencySymbols)) {
			$this->currencySymbols = $this->getCurrencySymbolsList();
		}
		return $this->currencySymbols;
	}
	public function getAllPagesListForSelect() {
        global $wpdb;
        // We are not using wp methods here - as list can be very large - and it can take too much memory
        $postTypesForPostsList = array('page', 'post', 'product', 'blog', 'grp_pages', 'documentation');
        $allPages = dbWcu::get("SELECT ID, post_title FROM $wpdb->posts WHERE post_type IN ('". implode("','", $postTypesForPostsList). "') AND post_status IN ('publish','draft') ORDER BY post_title");
        $array = array( WCU_HOME_PAGE_ID => __('Main Home page', WCU_LANG_CODE) );
        if (!empty($allPages)) {
            foreach ($allPages as $p) {
                $array[ $p['ID'] ] = $p['post_title'];
            }
        }
        return $array;
    }
    public function getAllPagesListForSelectByType($type) {
        global $wpdb;
        $postTypesForPostsList = array('page', 'post', 'product', 'blog', 'grp_pages', 'documentation');
        $allPages = dbWcu::get("SELECT ID, post_title FROM $wpdb->posts WHERE post_type = '$type' AND post_status IN ('publish','draft') ORDER BY post_title");
        $array = array();
        if (!empty($allPages)) {
            foreach ($allPages as $p) {
                $array[ $p['ID'] ] = $p['post_title'];
            }
        }
        return $array;
    }
    public function getAllProductCategories() {
        global $wpdb;
        $orderby = 'name';
        $order = 'asc';
        $hide_empty = false;
        $cat_args = array(
            'orderby'    => $orderby,
            'order'      => $order,
            'hide_empty' => $hide_empty,
        );
        $array = array();
        $product_categories = get_terms('product_cat', $cat_args);
        if (!empty($product_categories)) {
            foreach ($product_categories as $p) {
                $array[ $p->term_taxonomy_id ] = $p->name;
            }
        }
        return $array;
    }
    public function getAllPostTypes() {
        $post_types = get_post_types(array('publicly_queryable'=>1));
        $post_types['page'] = 'page';
        unset($post_types['attachment']);
        if (!empty($post_types)) {
            foreach ($post_types as $p) {
                $array[$p] = $p;
            }
        }
        return $array;
    }
	public function checkDisplayRules($options) {
        global $wp_query;
        $currentPageId = (int) isset($wp_query->post->ID) ? $wp_query->post->ID : 0;
        if( is_shop() ) {
            $currentPageId = get_option( 'woocommerce_shop_page_id' );
        } else if (is_product_category()) {
            $currentPageId = get_queried_object()->term_id;
        }
        $show = true;
        $displayMode = !empty($options['display_by_default']) ? $options['display_by_default'] : '';

		$pagesArr = !empty($options['pages_to_show']) ? $options['pages_to_show'] : '';
		$productCategoriesArr = !empty($options['product_categories_to_show']) ? $options['product_categories_to_show'] : '';
		$customPostArr = !empty($options['custom_post_types_to_show']) ? $options['custom_post_types_to_show'] : '';

		$pagesArrShow = !empty($options['pages_to_show_checkbox']) ? $options['pages_to_show_checkbox'] : '';
		$productCategoriesArrShow =  !empty($options['product_categories_to_show_checkbox']) ? $options['product_categories_to_show_checkbox'] : '';
		$customPostArrShow =  !empty($options['custom_post_types_to_show_checkbox']) ? $options['custom_post_types_to_show_checkbox'] : '';

		$show = false;

		if (!$show && !empty($pagesArrShow) && !empty($pagesArr)) {
			$show = in_array($currentPageId, $pagesArr);
		}
		if (!$show && !empty($productCategoriesArrShow) && !empty($productCategoriesArr)) {
			if (is_product_category()) {
				$show = in_array($currentPageId, $productCategoriesArr);
			}
			if (!$show && is_product()) {
				$productpage_id = get_queried_object()->ID;
				$terms = get_the_terms($productpage_id, 'product_cat');
				$show = in_array($terms[0]->term_id, $productCategoriesArr);
			}
		}
		if (!$show && !empty($customPostArrShow) && !empty($customPostArr)) {
			$show = in_array(get_post_type($currentPageId), $customPostArr);;
		}

		if ($displayMode === 'enable') {
			$show = (!$show) ? true : false;
		}

        return $show;
    }
	public function getShowModule($moduleName, $moduleIsPro = false) {
		if ( !empty($moduleIsPro) && $moduleIsPro && frameWcu::_()->getModule('options_pro') ) {
			$options = frameWcu::_()->getModule('options_pro')->getModel()->getOptionsPro();
		} else {
			$options = $this->getModel()->getOptions();
		}

		$show = false;
		if (!empty($options[$moduleName]['design_tab']['enable']) && ($options[$moduleName]['design_tab']['enable'] === '1') && $this->checkDisplayRules($options[$moduleName]['display_rules_tab'])) {
	            $show = false;
	            $showOn = !empty($options[$moduleName]['display_rules_tab']['show_on']) ? $options[$moduleName]['display_rules_tab']['show_on'] : 'both';

	            switch ($showOn) {
	                case 'both':
	                    $show = true;
	                    break;
	                case 'mobile':
	                    if (wp_is_mobile()) {
	                        $show = true;
	                    }
	                    break;
	                case 'desktops':
	                    if (!wp_is_mobile()) {
	                        $show = true;
	                    }
	                    break;
	         }
		}
		if ($this->disableWcuByCurrentUrl()) {
			$show = false;
		}
		return $show;
	}
	public function drawModuleAjax( $moduleName, $data, $isPro = false ) {
		$moduleName = isset( $moduleName ) ? $moduleName : '';
		$resHtml = '';
		$styleLink2 = '';
		if ( isset( $data ) ) {

			if ( $isPro ) {
				$data = $data[ 'wcu_options_pro' ];
			} else {
				$data = $data[ 'wcu_options' ];
			}

			if ($moduleName === 'currency_switcher') {
				$templateType = strtolower(isset( $data[ $moduleName ][ 'design_tab' ][ 'type' ] ) ? $data[ $moduleName ][ 'design_tab' ][ 'type' ] : '');
				$templateTypeDesign = '';
				$templateTypeStyle = '';
				if ($templateType === 'simple') {
					$templateTypeDesign = isset( $data[ $moduleName ][ 'design_tab' ][ 'design' ] ) ? $data[ $moduleName ][ 'design_tab' ][ 'design' ] : '';
					$templateTypeStyle = strtolower(isset( $data[ $moduleName ][ 'design_tab' ][ 'design' ] ) ? '.' . $data[ $moduleName ][ 'design_tab' ][ 'design' ] : '');
				}
				$styleLink = frameWcu::_()->getModule( $moduleName )->getModPath() . 'css/switcher.' . $templateType . $templateTypeStyle . '.css';
				$styleLink2 = frameWcu::_()->getModule( $moduleName )->getModPath() . 'css/frontend.switcher.css';
				$scriptLink = frameWcu::_()->getModule( $moduleName )->getModPath() . 'js/frontend.switcher.js';
			}

			if ($moduleName === 'currency_converter') {
				$styleLink = frameWcu::_()->getModule( $moduleName )->getModPath() . 'css/currency.converter.css';
				$scriptLink = frameWcu::_()->getModule( $moduleName )->getModPath() . 'js/frontend.currency.converter.js';
			}

			if ($moduleName === 'currency_rates') {
				$styleLink = frameWcu::_()->getModule( $moduleName )->getModPath() . 'css/currency.rates.css';
				$scriptLink = frameWcu::_()->getModule( $moduleName )->getModPath() . 'js/frontend.currency.rates.js';
			}

			$resHtml .= "<link rel='stylesheet' href='$styleLink' type='text/css' media='all' />";
			$resHtml .= !empty($styleLink2) ? "<link rel='stylesheet' href='$styleLink2' type='text/css' media='all' />" : '';
			$resHtml .= "<script type='text/javascript' src='".$scriptLink."'></script>";

			$resHtml .= frameWcu::_()->getModule( $moduleName )->drawModule( $data );
		}
		return $resHtml;
	}
	public function getWcuOrderCurrency( $orderId ) {
		$currency = utilsWcu::getOrderMeta($orderId, 'wcu_order_currency');
		//$currency = get_post_meta($orderId, 'wcu_order_currency', true);
		if (empty($currency)) {
			$currency = utilsWcu::getOrderMeta($orderId, '_order_currency');
			//$currency = get_post_meta($orderId, '_order_currency', true);
		}
		
		return $currency;
	}
	public function getDefaultOptions() {

		$optionsProModule = frameWcu::_()->getModule('options_pro');
		if ($optionsProModule) {
			$optionsPro = $optionsProModule->getProModuleDefaultOptions();
		}
		$options = array(
			'currencies' => array(),
			'options' => array(
				'converter_type' => 'cryptocompare',
				'aur_freq' => 'disabled',
				'aur_email_notice' => 'disabled',
				'convert_checkout' => '',
				'save_order_rate' => '',
				'free_converter_apikey' => '',
				'currencyapi_apikey' => '',
				'fixer_converter_apikey' => '',
				'ecb_converter_apikey' => '',
				'flag_enabled' => '0',
				'disable_uris' => '',
			),
			'currency_switcher' => frameWcu::_()->getModule('currency_switcher')->getDefaultOptions(),
		);

		if (!empty($optionsPro) && $optionsPro) {
			$options = array_merge($options, $optionsPro);
		}

		return $options;
	}
	public function getOptionsParams() {
		// row_classes - specific classes for option row.
		// 1 - set row_parent param for child option
		// 2 - set custom class for child option (for example, 'row_classes' => 'wcuSwEnable')
		// 3 - add data attribute to element params of parent option (for example, 'data-target-toggle' => '.wcuSwEnable')
		// 4 - when you will toggle the parent option all its child options will be shown / hidden depending on value of parent option

		// options_attrs - show / hide some dropdown options of child option depending on parent option value (for dropdowns only)
		// 1 - set row_parent param for child option
		// 2 - put in options_attrs param the next array array('dropdown_value' => 'data-type="parent_opt_value_to_show_this_dropdown_value"')
		// 3 - write the custom js function for this dependence of params (could not be automatic for now) @see /modules/currency/js/admin.currency.js
		// 4 - when you will toggle the parent option some dropdown options of child option will be shown / hidden depending on parent option value

		// row_parent - parent option, visibility of current option is dependent on its value
		// row_show - the value of parent option, when child option will be visible
		// row_hide - the value of parent option, when child option will be hidden
		// row_hide_with_all - hide sub-option when currency switcher is disabled (it is useful for sub options of base switcher's options)

		$optionsProModule = frameWcu::_()->getModule('options_pro');
		if ($optionsProModule) {
			$optionsPro = $optionsProModule->getProModuleOptionsParams();
			if (method_exists($optionsProModule, 'getProCurrencyAgregator')) {
				$currencyAgregator = $optionsProModule->getProCurrencyAgregator();
			}
		}

		$updateRates = frameWcu::_()->getModule('update_rates');

		$options = array(
			'currencies' => array(),
			'options' => array(
				'converter_type' => array(
					'html' => 'selectbox',
					'row_classes' => '',
					'row_show' => 'all',
					'tooltip' => __('Select your preferred currency aggregator to get the exchange rate. If you use cryptocurrency for trading, it is recommended to use the "Cryptocompare" aggregator.', WCU_LANG_CODE),
					'label' => __('Currency Aggregator', WCU_LANG_CODE),
					'params' => array(
						'options' => !empty($currencyAgregator) ? $currencyAgregator : array(
							'free_converter' => 'Free Converter',
							'cryptocompare' => 'Cryptocompare',
							'currencyapi' => 'Currency Conversion API',
							'ratesapipro' => 'RatesAPI (PRO)',
							'ecbpro' => 'European Central Bank (PRO)',
							//'xe' => 'Xe',
						),
						'attrs' => '',
						'data-target-toggle' => '.wcuSwEnableDesign',
					),
				),
				'free_converter_apikey' => array(
					'html' => 'input',
					'row_classes' => 'wcuOptionsFreeConverterApiKey wcuOptionsConverterRow wcuSwEnableDesign',
					'row_parent' => 'converter_type',
					'row_show' => 'free_converter',
					'row_hide' => '',
					'tooltip' => sprintf(__('Insert the free API key of the converter in this field. Read <a target="blank" href="%s"> instructions </a> on how to get a free API key for the converter. If the field is empty, Free Converter will use the default API key - this may create an error when getting the exchange rate.', WCU_LANG_CODE), 'https://free.currencyconverterapi.com/free-api-key'),
					'label' => __('Free Converter API key', WCU_LANG_CODE),
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel" ',
						'attrs' => 'class="wcuSwitcherInput" style="margin:0px; width:400px;"',
					),
				),
				'currencyapi_apikey' => array(
					'html' => 'input',
					'row_classes' => 'wcuOptionsCurrencyConversionApiKey wcuOptionsConverterRow wcuSwEnableDesign',
					'row_parent' => 'converter_type',
					'row_show' => 'currencyapi',
					'row_hide' => '',
					'tooltip' => sprintf(__('Insert the Currency Conversion API key in this field. Get <a target="blank" href="%s"> API key </a> for the Currency Conversion API. If the field is empty, Currency Conversion API will use the default API key - this may create an error when getting the exchange rate.', WCU_LANG_CODE), 'https://currencyapi.com/'),
					'label' => __('Currency Conversion API key', WCU_LANG_CODE),
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel" ',
						'attrs' => 'style="margin:0px; width:400px;"',
					),
				),
				'ecb_converter_apikey' => array(
					'html' => 'input',
					'row_classes' => 'wcuOptionsEcbApiKey wcuOptionsConverterRow wcuSwEnableDesign',
					'row_parent' => 'converter_type',
					'row_show' => 'ecb',
					'row_hide' => '',
					'tooltip' => sprintf(__('Insert the European Central Bank API key of the converter in this field. Read <a target="blank" href="%s"> instructions </a> on how to get a European Central Bank API key for the converter. If the field is empty, European Central Bank will use the default API key - this may create an error when getting the exchange rate.', WCU_LANG_CODE), 'https://exchangeratesapi.io/faq/'),
					'label' => __('European Central Bank API key', WCU_LANG_CODE),
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel" ',
						'attrs' => 'class="wcuSwitcherInput" style="margin:0px; width:400px;"',
					),
				),
				'fixer_converter_apikey' => array(
					'html' => 'input',
					'row_classes' => 'wcuOptionsFixerApiKey wcuOptionsConverterRow wcuSwEnableDesign',
					'row_parent' => 'converter_type',
					'row_show' => 'fixer',
					'row_hide' => '',
					'tooltip' => sprintf(__('Insert the Fixer API key of the converter in this field. Get <a target="blank" href="%s"> API key </a> for the Fixer converter. If the field is empty, Fixer will use the default API key - this may create an error when getting the exchange rate.', WCU_LANG_CODE), 'https://fixer.io/product'),
					'label' => __('Fixer API key', WCU_LANG_CODE),
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel" ',
						'attrs' => 'class="wcuSwitcherInput" style="margin:0px; width:400px;"',
					),
				),
				'currencylayer_converter_apikey' => array(
					'html' => 'input',
					'row_classes' => 'wcuOptionsCurrencylayerApiKey wcuOptionsConverterRow wcuSwEnableDesign',
					'row_parent' => 'converter_type',
					'row_show' => 'currencylayer',
					'row_hide' => '',
					'tooltip' => sprintf(__('Insert the currencylayer API key of the converter in this field. Get <a target="blank" href="%s"> API key </a> for the currencylayer converter. If the field is empty, currencylayer will use the default API key - this may create an error when getting the exchange rate.', WCU_LANG_CODE), 'https://currencylayer.com/product'),
					'label' => __('Currencylayer API key', WCU_LANG_CODE),
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel" ',
						'attrs' => 'class="wcuSwitcherInput" style="margin:0px; width:400px;"',
					),
				),
				'oer_converter_apikey' => array(
					'html' => 'input',
					'row_classes' => 'wcuOptionsOerApiKey wcuOptionsConverterRow wcuSwEnableDesign',
					'row_parent' => 'converter_type',
					'row_show' => 'oer',
					'row_hide' => '',
					'tooltip' => sprintf(__('Insert the Open Exchange Rates API key of the converter in this field. Get <a target="blank" href="%s"> API key </a> for the Open Exchange Rates converter. If the field is empty, Open Exchange Rates will use the default API key - this may create an error when getting the exchange rate.', WCU_LANG_CODE), 'https://openexchangerates.org/signup'),
					'label' => __('Open Exchange Rates API key', WCU_LANG_CODE),
					'params' => array(
						'label_attrs' => 'class="wcuSwitcherInputLabel" ',
						'attrs' => 'class="wcuSwitcherInput" style="margin:0px; width:400px;"',
					),
				),
				'convert_checkout' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => '',
					'row_show' => 'all',
					'row_hide' => '',
					'tooltip' => __('You can allow change currency at checkout. Please note, that some payment systems (like PayPal) could use only fixed currencies.', WCU_LANG_CODE),
					'label' => __('Change currency at checkout', WCU_LANG_CODE),
					'params' => array(
                        'value'=>'1',
						'data-target-toggle' => '.wcuSwEnableCheckbox',
                    ),
				),
				'save_order_rate' => array(
					'html' => 'checkboxHiddenVal',
					'row_classes' => 'wcuSwEnableCheckbox',
					'row_parent' => 'convert_checkout',
					'row_show' => '1',
					'row_hide' => '',
					'tooltip' => __('The order will always display the price that was calculated at the exchange rate at the time of purchase', WCU_LANG_CODE),
					'label' => __('Save the current rate in the order', WCU_LANG_CODE),
					'params' => array(
                        'value'=>'1',
                    ),
				),
				'aur_freq' => array(
					'html' => 'selectbox',
					'row_classes' => '',
					'row_show' => 'all',
					'tooltip' => __('Automatic update of exchange rates for the selected schedule.', WCU_LANG_CODE),
					'label' => ($updateRates) ? __('Automatic exchange rates updates', WCU_LANG_CODE) : __('Automatic exchange rates updates <sup class="pro-label">PRO</sup>', WCU_LANG_CODE),
					'params' => array(
						'options' => array(
							'disabled' => __('Enter Rates Manually', WCU_LANG_CODE),
							'one_min' => __('Update Every Minute', WCU_LANG_CODE),
							'one_hour' => __('Update Hourly', WCU_LANG_CODE),
							'half_day' => __('Update Twice Daily', WCU_LANG_CODE),
							'daily' => __('Update Daily', WCU_LANG_CODE),
							'weekly' => __('Update Weekly', WCU_LANG_CODE),
						),
						'attrs' => ($updateRates) ? '' : 'disabled',
					),
				),
				'aur_email_notice' => array(
					'html' => 'selectbox',
					'row_classes' => '',
					'row_show' => 'all',
					'tooltip' => __('Sends to admin email notice with result values of automatic exchange rates update.', WCU_LANG_CODE),
					'label' => ($updateRates) ? __('Automatic exchange rates updates (notice admin by email)', WCU_LANG_CODE) : __('Automatic exchange rates updates (notice admin by email) <sup class="pro-label">PRO</sup>', WCU_LANG_CODE),
					'params' => array(
						'options' => array(
							'disabled' => __('Disabled', WCU_LANG_CODE),
							'enabled' => __('Enabled', WCU_LANG_CODE),
						),
						'attrs' => ($updateRates) ? '' : 'disabled',
					),
				),
				'disable_uris' => array(
                    'html' => 'textarea',
                    'row_classes' => '',
                    'row_parent' => '',
                    'row_show' => '',
                    'row_hide' => '',
					'tooltip' => __('Type relative links, each from a new line, to disable the display of the currency converter on these pages.', WCU_LANG_CODE),
                    'label' => __('Disable URIs', WCU_LANG_CODE),
                ),
			),
			'currency_switcher' => frameWcu::_()->getModule('currency_switcher')->getOptionsParams(),
		);

		if (!empty($optionsPro) && $optionsPro) {
			$options = array_merge($options, $optionsPro);
		}

		return $options;

	}
	public function getCryptoCurrencyList() {
		return array (
			'BTC' => '&#3647;',
			'ETC' => 'ETC',
			'LTC' => '&#321;',
			'ETH' => 'ETH',
			'ZEC' => 'ZEC',
			'DASH' => 'DASH',
			'XRP' => 'XRP',
			'XMR' => 'XMR',
			'BCH' => 'BCH',
			'NEO' => 'NEO',
			'ADA' => 'ADA',
			'EOS' => 'EOS',
		);
	}
	public function getCurrencySymbolsList($isMerge = true) {
		$currencySymbols = array(
			'USD' => '&#36;',
			'EUR' => '&euro;',
			'GBP' => '&pound;',
			'JPY' => '&yen;',
			'INR' => '&#8377;',
			'UAH' => 'грн.',
			'RUB' => '₽',
			'AUD' => 'AU&#36;',
			'ARS' => 'ARS$',
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BOB' => 'Bs.',
			'BTN' => 'Nu.',
			'BRL' => 'R$',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'CAD' => 'C$',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CNY' => '&yen;',
			'CRC' => '&#x20a1;',
			'CZK' => '&#75;&#269;',
			'CFP' => '₣',
			'CLP' => 'CLP',
			'COP' => '&#36',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'FKP' => '&pound;',
			'FJD' => 'FJ$',
			'GEL' => '&#x10da;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'HNL' => 'L',
			'HRK' => 'Kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'HKD' => 'HK&#36;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KZT' => 'KZT',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRO' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'MXN' => 'MXN',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => 'NZ&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/.',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#x434;&#x438;&#x43d;.',
			'RWF' => 'Fr',
			'SGD' => 'S$',
			'SAR' => '&#x631;.&#x633;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SSP' => '&pound;',
			'STD' => 'Db',
			'SVC' => '&#36',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'TTD' => 'TT$',
			'UGX' => 'UGX',
			'UZS' => 'UZS',
			'UYU' => 'UYU',
			'VES' => 'Bs F',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
		$cryptoCurrencyList = $this->getCryptoCurrencyList();
		$currencySymbols = array_merge( $currencySymbols, $cryptoCurrencyList);
		$customSymbols = frameWcu::_()->getModule('custom_symbols');
		if ( $customSymbols && $isMerge && $currencyUserSymbols = $customSymbols->getModel()->getCurrencyUserSymbols() ) {
					if (!empty($currencyUserSymbols) && $currencyUserSymbols) {
						$currencySymbols = array_merge($currencyUserSymbols, $currencySymbols);
					}
		}
		$customCurrenciesList = isset($this->optionsPro['custom_currency']) ? $this->optionsPro['custom_currency'] : array();
		if ( $customCurrenciesList ) {
			$currencySymbols = array_merge($customCurrenciesList, $currencySymbols);
		}

		return $currencySymbols;
	}
	public function getCurrencyDecimalsList() {
		return array(
			'2' => 'show cents',
			'1' => 'round cents',
			'0' => 'hide cents',
		);
	}

	public function getFontFamilyList() {
		return array (
		  'Georgia' => 'Georgia',
		  'Palatino Linotype' => 'Palatino Linotype',
		  'Times New Roman' => 'Times New Roman',
		  'Arial' => 'Arial',
		  'Helvetica' => 'Helvetica',
		  'Arial Black' => 'Arial Black',
		  'Gadget' => 'Gadget',
		  'Comic Sans MS' => 'Comic Sans MS',
		  'Impact' => 'Impact',
		  'Charcoal' => 'Charcoal',
		  'Lucida Sans Unicode' => 'Lucida Sans Unicode',
		  'Lucida Grande' => 'Lucida Grande',
		  'Tahoma' => 'Tahoma',
		  'Geneva' => 'Geneva',
		  'Trebuchet MS' => 'Trebuchet MS',
		  'Verdana' => 'Verdana',
		  'Courier New' => 'Courier New',
		  'Courier' => 'Courier',
		  'Lucida Console' => 'Lucida Console',
		  'Monaco' => 'Monaco',
		);
	}
	public function wcuTranslit($str) {
		$rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
		return str_replace($rus, $lat, $str);
	}

	public function overrideWcWidgetLayeredNavFilters() {
		if (class_exists('WC_Widget_Layered_Nav_Filters')) {
			unregister_widget('WC_Widget_Layered_Nav_Filters');
			include(WCU_MODULES_DIR . 'currency/includes/class-wcu_widget_layered_nav_filters.php');
			register_widget( 'Wcu_Widget_Layered_Nav_Filters' );
		}
	}

	public function loadProductsFilter( $q ) {
		$countryCode  = frameWcu::_()->getModule( 'geoip_rules' )->getUserCountryCode();
		$currencyCode = frameWcu::_()->getModule( 'geoip_rules' )->getCurrencyCodeByCountry( $countryCode );
		if ( '' !== $currencyCode ) {
			$metaQuery[] = array( 'key' => 'wcu_currency_unavailable_' . $currencyCode, 'compare' => 'NOT EXISTS' );
			if ( $q instanceof WP_Query ) {
				$metaQuery = array_merge( $q->get( 'meta_query' ), $metaQuery );
				$q->set( 'meta_query', $metaQuery );
			} else {
				$q['meta_query'] = ( isset( $q['meta_query'] ) ) ? array_merge( $q['meta_query'], $metaQuery ) : $metaQuery;
			}
		}

		return $q;
	}

	public function checkAvailabilityForCountry( $preempt, $wp_query ) {
		if (is_single()) {
			$countryCode  = frameWcu::_()->getModule( 'geoip_rules' )->getUserCountryCode();
			$currencyCode = frameWcu::_()->getModule( 'geoip_rules' )->getCurrencyCodeByCountry( $countryCode );
			if ( '' !== $currencyCode ) {
				$isUnavailable = get_post_meta( url_to_postid( $_SERVER['REQUEST_URI'] ), 'wcu_currency_unavailable_' . $currencyCode );
				if ( $isUnavailable ) {
					$wp_query->set_404();
					status_header( 404 );
					nocache_headers();
					//wp_die( 'Page not found', '', 404 );
				}
			}
		}
		return false;
	}
}
