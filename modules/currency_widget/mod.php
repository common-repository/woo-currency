<?php
class currency_widgetWcu extends moduleWcu {
	public function init() {
        parent::init();

		add_shortcode(WCU_SHORTCODE_SWITCHER, array($this, 'drawSwitcherWidget'));
		add_shortcode(WCU_SHORTCODE_CONVERTER, array($this, 'drawConverterWidget'));
		add_shortcode(WCU_SHORTCODE_RATES, array($this, 'drawRatesWidget'));

		add_action('widgets_init', array($this, 'registerWidget'));
		add_action('admin_enqueue_scripts', array($this, 'addColorPickerScripts'));
    }
	function addColorPickerScripts($hook){
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style('wp-color-picker');
	}
	public function registerWidget() {
		register_widget('wcuCurrencySwitcherWidget');
		register_widget('wcuCurrencyConverterWidget');
		register_widget('wcuCurrencyRatesWidget');
	}
	public function drawSwitcherWidget($atts) {
		if (is_array($atts) && array_key_exists('exclude', $atts) && (!is_array($atts['exclude']))) {
			$atts['exclude'] = explode(',', $atts['exclude']);
		}
		$params = shortcode_atts( array(
			'currency_display' => 'name',
			'show_as' => 'dropdown',
			'show_flag_dropdown' => false,
			'width' => '100%',
			'exclude' => array(),
			'show_on' => 'both',
			'show_on_widths' => 0,
			'show_on_screen_compare' => 'less',
			'show_on_widths_value' => '',
		), $atts );
		return frameWcu::_()->getModule('currency_widget')->getView()->displayWidget($params, 'currencySwitcher');
	}
	public function drawConverterWidget($atts) {
		if (is_array($atts) && array_key_exists('exclude', $atts) && (!is_array($atts['exclude']))) {
			$atts['exclude'] = explode(',', $atts['exclude']);
		}
		$params = shortcode_atts( array(
			'currency_display' => 'name',
			'show_flag_dropdown' => false,
			'layout' => 'vertical',
			'btn_txt_color' => 'white',
			'btn_bg_color' => '#333',
			'btn_bg_color_h' => '#e58004',
			'width' => '100%',
			'exclude' => array(),
			'show_on' => 'both',
			'show_on_widths' => 0,
			'show_on_screen_compare' => 'less',
			'show_on_widths_value' => '',
		), $atts );
		return frameWcu::_()->getModule('currency_widget')->getView()->displayWidget($params, 'currencyConverter');
	}
	public function drawRatesWidget($atts) {
		if (is_array($atts) && array_key_exists('exclude', $atts) && (!is_array($atts['exclude']))) {
			$atts['exclude'] = explode(',', $atts['exclude']);
		}
		$params = shortcode_atts( array(
			'currency_display' => 'name',
			'show_flag_dropdown' => false,
			'show_flag_currency_list' => false,
			'width' => '100%',
			'exclude' => array(),
			'show_on' => 'both',
			'show_on_widths' => 0,
			'show_on_screen_compare' => 'less',
			'show_on_widths_value' => '',
		), $atts );
		return frameWcu::_()->getModule('currency_widget')->getView()->displayWidget($params, 'currencyRates');
	}
}
/**
 * Load widget classes
 */
include_once 'classes/wcuCurrencySwitcherWidget.php';
include_once 'classes/wcuCurrencyConverterWidget.php';
include_once 'classes/wcuCurrencyRatesWidget.php';
