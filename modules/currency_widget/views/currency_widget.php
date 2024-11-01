<?php
class currency_widgetViewWcu extends viewWcu {
	public function displayForm($data, $widget, $template) {
		$this->addCommonValidation();
		$this->addCommonAdminAssets();
		$this->displayWidgetForm($data, $widget, $template . 'Form');
	}
	public function displayWidget($instance, $template) {
		if(class_exists('WooCommerce')) {
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
			frameWcu::_()->getModule('templates')->loadCoreJs();

			$show = false;
			$showOn = !empty($instance['show_on']) ? $instance['show_on'] : 'both';

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

			if ($show) {

				$labelType = isset($instance['currency_display']) ? $instance['currency_display'] : 'name';

				$showAs = isset($instance['show_as']) ? $instance['show_as'] : false;
				$showFlagDropdown = isset($instance['show_flag_dropdown']) ? $instance['show_flag_dropdown'] : false;
				$showFlagCurrencyList = isset($instance['show_flag_currency_list']) ? $instance['show_flag_currency_list'] : false;

				$showFlags = false;
				$moduleFlags = false;

				if (!empty(frameWcu::_()->getModule('flags'))) {
					if ( ($showAs === 'flags') || $showFlagDropdown || $showFlagCurrencyList ) {
						$showFlags = true;
					}
					$moduleFlags = true;
				}

				$currenciesOpts = $this->getCurrenciesOpts($template, $labelType, $showFlags);

				$exclude = !empty($instance['exclude']) ? $instance['exclude'] : array();

				$randId = mt_rand(1,99999);

				foreach($currenciesOpts as $key => $opt) {
					if(in_array($key, $exclude)) {
						unset($currenciesOpts[$key]);
					}
				}

				$this->assign('instance', $instance);
				$this->assign('currenciesOpts', $currenciesOpts);
				$this->assign('moduleFlags', $moduleFlags);
				$this->assign('showFlags', $showFlags);
				$this->assign('randId', $randId);
				$this->assign('currentCurrency', frameWcu::_()->getModule('currency')->getCurrentCurrency());
				frameWcu::_()->getModule('templates')->loadFontAwesome();
				$template == 'currencySwitcher' && frameWcu::_()->getModule('templates')->loadChosenSelects();
				frameWcu::_()->addStyle('widget.' . $template, $this->getModule()->getModPath(). 'css/widget.' . $template . '.css');

				return parent::getContent($template . 'Widget');

			}

		}
	}
	public function addCommonAdminAssets() {
		frameWcu::_()->addStyle('wcu.widgets', $this->getModule()->getModPath(). 'css/wcu.widgets.css');
	}
	public function addCommonValidation() {
		if(!class_exists('WooCommerce')) {
			echo "<div class='notice'>" . __('Warning: Woocommerce is not activated!', WCU_LANG_CODE) . "</div>";
		}
		return false;
	}
	public function getCurrenciesOpts($template, $labelType, $showFlags) {
		$currencies = frameWcu::_()->getModule('currency')->getModel()->getCurrencies();
		$currencySymbols = frameWcu::_()->getModule('currency')->getCurrencySymbols();
		$options = frameWcu::_()->getModule('currency')->getModel()->getOptions();
		if ( $showFlags ) {
			$flagsList = frameWcu::_()->getModule('flags')->getFlagsList();
		}
		$currenciesOpts = array();
		foreach($currencies as $c) {
			$title = isset($c[$labelType]) ? $c[$labelType] : '';
			if ($labelType == 'symbol') {
				$title = isset($currencySymbols[$c['name']]) ? $currencySymbols[$c['name']] : $title;
			}
			switch($template) {
				case 'currencySwitcher':
				case 'currencyConverter':
						$currenciesOpts[$c['name']]['name'] = $title;
					break;
				case 'currencyRates':
						$currenciesOpts[$c['name']]['name'] = '1 ' . $title;
					break;
				default:
					break;
			}
			if ( $showFlags ) {
				$currenciesOpts[$c['name']]['flag'] = (!empty($c['flag']) && !empty($flagsList[$c['flag']])) ? $flagsList[$c['flag']] : '';
			}
		}
		return $currenciesOpts;
	}
}
