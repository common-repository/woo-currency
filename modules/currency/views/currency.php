<?php

class currencyViewWcu extends viewWcu {

	public function getCurrencyTabContent() {

		$module = $this->getModule();
		$model = $this->getModel();
		$options = $model->getOptions();
		$defOptions = $module->getDefaultOptions();
		$currencies = $model->getCurrencies();

		$customSymbolsModule = frameWcu::_()->getModule('custom_symbols');
		if($customSymbolsModule){
			$currenciesSymbols = $customSymbolsModule->getModel()->getCurrencyUserSymbols();
		}

		$flagsModule = frameWcu::_()->getModule('flags');
		if($flagsModule){
			$this->assign('flagsModule', $flagsModule);
		}

		$optionsProModule = frameWcu::_()->getModule('options_pro');
		if($optionsProModule){
			$this->assign('optionsProModule', $optionsProModule);
		}
		
		$currencies = array_merge(array(array()), $currencies);
		frameWcu::_()->getModule('templates')->loadBootstrapSimple();
		frameWcu::_()->getModule('templates')->loadFontAwesome();
		frameWcu::_()->addStyle('admin.currency', $module->getModPath() . 'css/admin.currency.css');
		frameWcu::_()->addScript('wp.tabs', WCU_JS_PATH. 'wp.tabs.js');
		frameWcu::_()->addScript('admin.currency', $module->getModPath() . 'js/admin.currency.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'));

		frameWcu::_()->addScript('cookie.handler', WCU_JS_PATH. 'CookieHandler.js');
		frameWcu::_()->addScript('currency.flag_handler', WCU_JS_PATH. 'currency.flag_handler.js', array('cookie.handler'));
		frameWcu::_()->addScript('admin.currency', $module->getModPath() . 'js/admin.currency.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'cookie.handler', 'currency.flag_handler'));
		frameWcu::_()->addJSVar('admin.currency', 'wcuCssPath', WCU_CSS_PATH);

		$this->assign('curDbOpt', $module->currencyDbOpt);
		$this->assign('optDbOpt', $module->optionsDbOpt);
		$this->assign('currencies', $currencies);
		$this->assign('optionsParams', $this->_prepareOptionsParams($options, $defOptions));
		$this->assign('dbPrefix', $this->getModule()->currencyDbOpt);
		$this->assign('defCur', 'USD');
		$this->assign('defPos', 'left');
		$this->assign('customSymbolsModule', $customSymbolsModule);
		$this->assign('modulePath', $module->getModPath() );
		$this->assign('pluginLink', frameWcu::_()->getModule('promo')->getMainLink() );
		
		parent::display('currencyTabContent');
	}

	public function getChildrenOneTab($array) {
		$module = $this->getModule();
		$model = $this->getModel();
		$moduleIsPro = !empty($array[1]) ? $array[1] : false;
		if (!empty($moduleIsPro) && $moduleIsPro && frameWcu::_()->getModule('options_pro')) {
			$options = frameWcu::_()->getModule('options_pro')->getModel()->getOptionsPro();
		} else {
			$options = $model->getOptions();
		}
		$defOptions = $module->getDefaultOptions();
		$this->assign('moduleName', $array[0]);
		$this->assign('moduleIsPro', $moduleIsPro);
		$this->assign('optionsParams', $this->_prepareOptionsParams($options, $defOptions));
		parent::display('childrenOneTab');
	}

	public function getChildrenMultipleTab($array) {
		$module = $this->getModule();
		$model = $this->getModel();
		$moduleIsPro = !empty($array[2]) ? $array[2] : false;
		$showPreviewAjax = !empty($array[3]) ? $array[3] : false;
		if ($moduleIsPro && frameWcu::_()->getModule('options_pro')) {
			$options = frameWcu::_()->getModule('options_pro')->getModel()->getOptionsPro();
		} else {
			$options = $model->getOptions();
		}
		$defOptions = $module->getDefaultOptions();
		$this->assign('moduleName', $array[0]);
		$this->assign('moduleTab', $array[1]);
		$this->assign('moduleIsPro', $moduleIsPro);
		$this->assign('showPreviewAjax', $showPreviewAjax);
		$this->assign('optionsParams', $this->_prepareOptionsParams($options, $defOptions));
		parent::display('childrenMultipleTab');
	}

	public function _prepareOptionsParams($options, $defOptions) {
		$optionsParams = $this->getModule()->getOptionsParams();

		$indexTabWithChildrenOptionsArr = array (
			'currency_switcher', 'currency_tooltip', 'currency_rates', 'currency_converter',
		);

		foreach($optionsParams as $indexTab => &$optBlock) {
			if (in_array($indexTab, $indexTabWithChildrenOptionsArr)) {
				foreach($optBlock as $indexTabSubOpt => &$optTab) {
					foreach ($optTab as $key => &$opt) {

						if ( $opt['html'] === 'selectlistsortable' ) {
							$opt['params']['options'] =  isset($options[$indexTab][$indexTabSubOpt][$key]) && is_array($options[$indexTab][$indexTabSubOpt][$key]) ? array_replace(array_flip($options[$indexTab][$indexTabSubOpt][$key]), $opt['params']['options']) : $opt['params']['options'] ;
						}

						$opt['params'] = isset($opt['params']) ? $opt['params'] : array();
						$opt['params']['value'] = isset($options[$indexTab][$indexTabSubOpt][$key]) ? $options[$indexTab][$indexTabSubOpt][$key] : $defOptions[$indexTab][$indexTabSubOpt][$key];
					}
				}
			} else {
				foreach($optBlock as $key => &$opt) {
					$opt['params'] = isset($opt['params']) ? $opt['params'] : array();
					$defOptions[$indexTab][$key] = isset($defOptions[$indexTab][$key]) ? $defOptions[$indexTab][$key] : '';
					$opt['params']['value'] = isset($options[$indexTab][$key]) ? $options[$indexTab][$key] : $defOptions[$indexTab][$key];
				}
			}

		}

		return $optionsParams;
	}

	function isDisplayElem($value, $params) {
		$display = '';
		$showArr = array_filter(explode(',', str_replace('all', '', !empty($params['row_show']) ? $params['row_show'] : '')));
		$hideArr = array_filter(explode(',', !empty($params['row_hide']) ? $params['row_hide'] : '' ));

		if(!empty($params['row_parent'])) {
			if(!empty($showArr)) {
				$display = in_array($value, $showArr) || empty($showArr) ? '' : 'display: none;';
			} else if(!empty($hideArr)) {
				$display = empty($value) || $value == 'disable' || in_array($value, $hideArr) ? 'display: none;' : '';
			}
		}
		return $display;
	}

}
