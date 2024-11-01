<?php
class currency_widgetControllerWcu extends controllerWcu {
	public function getCurrencyRate() {
		$res = new responseWcu();
		$amount = reqWcu::getVar('amount');
		$fromCurrency = reqWcu::getVar('currency_from');
		$toCurrency = reqWcu::getVar('currency_to');
		$currencyModel = frameWcu::_()->getModule('currency')->getModel();
		$currencies = $currencyModel->getCurrencies();
		$defaultCurrency = frameWcu::_()->getModule('currency')->getDefaultCurrency();

		if(!empty($currencies) && isset($currencies[$toCurrency]) && isset($currencies[$fromCurrency])) {
			$rateFrom = isset($currencies[$fromCurrency]['rate']) && is_numeric($currencies[$fromCurrency]['rate'])
				? $currencies[$fromCurrency]['rate']
				: $currencyModel->getCurrencyRate($defaultCurrency, $fromCurrency);
			$rateTo = isset($currencies[$toCurrency]['rate']) && is_numeric($currencies[$toCurrency]['rate'])
				? $currencies[$toCurrency]['rate']
				: $currencyModel->getCurrencyRate($defaultCurrency, $toCurrency);

			$res->addMessage(__('Done', WCU_LANG_CODE));
			$res->addData('result', round($amount * $rateTo / $rateFrom, 7));
		} else {
			$res->pushError(sprintf(__('no data for %s', WCU_LANG_CODE), $toCurrency));
		}


		return $res->ajaxExec();
	}
	public function getCurrencyRatesList() {
		$res = new responseWcu();
		$exclude = reqWcu::getVar('exclude');
		$exclude = !empty($exclude) ? array_filter(array_map('trim', explode(',', $exclude))) : array();
		$current = reqWcu::getVar('current');
		$currencyModel = frameWcu::_()->getModule('currency')->getModel();
		$currencies = $currencyModel->getCurrencies();
		$defaultCurrency = frameWcu::_()->getModule('currency')->getDefaultCurrency();
		$cryptoCurrencyList = frameWcu::_()->getModule('currency')->getCryptoCurrencyList();
		$rates = array();

		$proModule = frameWcu::_()->getModule('options_pro');
		$rounding = false;
		if($proModule){
			$options = $proModule->getModel()->getOptionsPro();
			if (isset($options['currency_rates']) && isset($options['currency_rates']['design_tab'])) {
				$ratesSettings = $options['currency_rates']['design_tab'];
        		if (isset($ratesSettings['cr_rounding']) && $ratesSettings['cr_rounding'] == '1') {
        			$decimal = isset($ratesSettings['cr_decimal']) ? (int) $ratesSettings['cr_decimal'] : 0;
       				$rounding = true;
       			}
       		}
		}

		if(!empty($currencies) && isset($currencies[$current])) {
			foreach($currencies as $c) {
				if(in_array($c['name'], $exclude)) continue;
				$currencyRate = isset($currencies[$c['name']]['rate']) && is_numeric($currencies[$c['name']]['rate'])
					? $currencies[$c['name']]['rate']
					: $currencyModel->getCurrencyRate($defaultCurrency, $c['name']);

				$currentCurrencyRate = isset($currencies[$current]['rate']) && is_numeric($currencies[$current]['rate'])
					? $currencies[$current]['rate']
					: $currencyModel->getCurrencyRate($defaultCurrency, $currencies[$current]['name']);

				$rate = $currencyRate / $currentCurrencyRate;
				// $pos = strrpos($rate, '.');
				// $rates[$c['name']] = substr($rate, 0, $pos + 1 + 4);
				$rates[ $c['name'] ] = $rounding ? round( $rate, $decimal ) : ( ctype_digit( (string) $rate ) ? number_format( $rate, 2 ) : $rate );
			}
		}
		$res->addMessage(__('Done', WCU_LANG_CODE));
		$res->addData('rates', $rates);

		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			WCU_USERLEVELS => array(
				WCU_ADMIN => array()
			),
		);
	}
}
