<?php
class wcuCurrencySwitcherWidget extends WP_Widget {
	public $widgetName = 'wcuCurrencySwitcherWidget';
	public $widgetTemplate = 'currencySwitcher';
	public $widgetOps = array();

	public function __construct() {
		$this->widgetOps = array(
			'classname' => $this->widgetName,
			'description' => __('Displays Currency Switcher for Woocommerce products', WCU_LANG_CODE)
		);
		parent::__construct( $this->widgetName, WCU_WP_PLUGIN_NAME . ' ' . __('Switcher', WCU_LANG_CODE), $this->widgetOps );
	}
	public function widget($args, $instance) {
		echo frameWcu::_()->getModule('currency_widget')->getView()->displayWidget($instance, $this->widgetTemplate);
	}
	public function form($instance) {
		frameWcu::_()->getModule('currency_widget')->getView()->displayForm($instance, $this, $this->widgetTemplate);
	}
	public function update($new_instance, $old_instance) {
		return $new_instance;
	}
}
