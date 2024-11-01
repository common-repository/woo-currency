<?php

class currencyModelWcu extends modelWcu {

	public static $savedCurrencies = null;
	
	public function saveCurrencies($data) {
		$currencies = array();

		if(!empty($data['name'])) {
			foreach($data['name'] as $key => $name) {
				$currencies[$name] = array('index' => $key,);
			}

			foreach($data as $key => $item) {
				foreach($currencies as $index => $cur) {
					$currencies[$index][$key] = isset($item[$currencies[$index]['index']]) ? $item[$currencies[$index]['index']] : '';
				}
			}

			foreach($currencies as $key => $c) {
				$currencies[$key]['title'] = utilsWcu::escape($currencies[$key]['title']);
				if($currencies[$key]['rate'] == 1) {
					update_option('woocommerce_currency', $currencies[$key]['name']);
				}
				if (isset($currencies[$key]['symbol'])) {
					$currencies[$key]['symbol'] = apply_filters('wcu_get_change_custom_symbol', $currencies[$key]['symbol'], $key);
				}
			}
		}
		return update_option($this->getModule()->currencyDbOpt, $currencies);
	}

	public function getCurrencies() {
		if (!is_null(self::$savedCurrencies)) {
			return self::$savedCurrencies;
		}
		$currencies = get_option($this->getModule()->currencyDbOpt, array());
		if(empty($currencies) || !is_array($currencies)) {
			$currencies = $this->getDefaultCurrency();
		}
		foreach ($currencies as $key => $value) {
			$currencies[$key]['rate'] = !empty($currencies[$key]['rate_custom']) ? $currencies[$key]['rate_custom'] : $currencies[$key]['rate'];
		}
		
		self::$savedCurrencies = apply_filters('wcu_get_currencies_data', $currencies);

		return self::$savedCurrencies;
	}



	public function getDefaultCurrency() {
		$wcCurrency = get_option('woocommerce_currency', 'USD');
		$wcCurrencyPos = get_option('woocommerce_currency_pos', 'left');
		$currencies = array();

		$currencies[$wcCurrency] = array(
			'name' => $wcCurrency,
			'title' => $wcCurrency,
			'symbol' => $this->getCurrencySymbol($wcCurrency),
			'show_cents' => $wcCurrency,
			'position' => $wcCurrencyPos,
			'etalon' => 1,
			'rate' => 1,
			'rate_custom' => '',
			'sort_order' => 1,
		);
		return $currencies;
	}
	public function getCurrencySymbol($currency) {
		$currencySymbols = $this->getModule()->getCurrencySymbols();

		return isset($currencySymbols[$currency]) ? $currencySymbols[$currency] : $currencySymbols['USD'];
	}
	public function getCurrencyPriceFormat($format) {
		$currencies = $this->getCurrencies();
		$currentCurrency = $this->getModule()->currentCurrency;

		if(isset($currencies[$currentCurrency])) {
			switch($currencies[$currentCurrency]['position']) {
				case 'left':
					$format = '%1$s%2$s';
					break;
				case 'right':
					$format = '%2$s%1$s';
					break;
				case 'left_space':
					$format = '%1$s&nbsp;%2$s';
					break;
				case 'right_space':
					$format = '%2$s&nbsp;%1$s';
					break;
				default:
					break;
			}
		}
		return $format;
	}
	public function getCurrencyPrice($price, $product = null) {
		$module = $this->getModule();
		$currencies = $this->getCurrencies();
		$defaultCurrency = $module->getDefaultCurrency();
		$currentCurrency = $module->getCurrentCurrency();
		$cryptoCurrencies = $module->getCryptoCurrencyList();
		$decimalSep = $module->decimalSep;
		$priceNumDecimals = $module->priceNumDecimals;
		$precision = $currentCurrency != $defaultCurrency
			? $this->_getPriceDecimalsCount($currentCurrency, $priceNumDecimals, $currencies)
			: $this->_getPriceDecimalsCount($defaultCurrency, $priceNumDecimals, $currencies);
		$exchangeFeeSign = isset($currencies[$currentCurrency]['exchange_fee_sign']) ? $currencies[$currentCurrency]['exchange_fee_sign'] : 0;
		$exchangeFee = isset($currencies[$currentCurrency]['exchange_fee']) ? $currencies[$currentCurrency]['exchange_fee'] : 0;

		if($currentCurrency != $defaultCurrency) {
			$newPrice = dispatcherWcu::applyFilters('getManualPrice', $price, $currentCurrency, $product);
			if ($newPrice !== false) {
				if ($newPrice === true) {
					return $price;
				}
				if ($newPrice != $price) {
					return $newPrice;
				}
			}
			//Rewrite manual rate
			if ( !empty($currencies[$currentCurrency]['rate_custom']) ) {
				$currencies[$currentCurrency]['rate'] = $currencies[$currentCurrency]['rate_custom'];
			} elseif ( $exchangeFee ) {
				$exchangeFee = $exchangeFeeSign ? $exchangeFee * (-1) : $exchangeFee;
				$currencies[$currentCurrency]['rate'] += $exchangeFee;
			}
		}
			//Edited this line to set default converting of currency
		if ( !array_key_exists( $currentCurrency, $cryptoCurrencies ) ) {
			if ($currentCurrency != $defaultCurrency) {
				$price = isset($currencies[$currentCurrency]) && $currencies[$currentCurrency] != null
					? floatval((float) $price * (float) $currencies[$currentCurrency]['rate'])
					: floatval((float) $price * (float) $currencies[$defaultCurrency]['rate']);
			} else {
				$price = (float) $price;
			}

			if (empty($currencies[$currentCurrency]['decimals'])) {
				$price = floor($price);
			} else if ($currencies[$currentCurrency]['decimals'] == 1) {
				$price = number_format(round($price), $precision, $decimalSep, '');
			} else {
				$price = number_format($price, $precision, $decimalSep, '');
			}
		/*$price = isset($currencies[$currentCurrency]) && $currencies[$currentCurrency] != null
			? number_format(floatval((float) $price * (float) $currencies[$currentCurrency]['rate']), $precision, $decimalSep, '')
			: number_format(floatval((float) $price * (float) $currencies[$defaultCurrency]['rate']), $precision, $decimalSep, '');*/
		} else {
			$price = isset($currencies[$currentCurrency]) && $currencies[$currentCurrency] != null
			? floatval((float) $price * (float) $currencies[$currentCurrency]['rate'])
			: floatval((float) $price * (float) $currencies[$currentCurrency]['rate']);
		}
		
		return $price;
		//some hints for price rounding
		//http://stackoverflow.com/questions/11692770/rounding-to-nearest-50-cents
		//$price = round($price * 2, 0) / 2;
		//return round ( $price , 0 ,PHP_ROUND_HALF_EVEN );
		//return number_format ($price, $priceNumDecimals, $decimalSep, $this->thousands_sep);
	}
	public function getCurrencyVariationPrices($pricesArr) {
		// lets sort arrays by values to avoid wrong price displaying on the front
		if(!empty($pricesArr) && is_array($pricesArr)) {
			foreach($pricesArr as $key => $arrvals) {
				asort($arrvals);
				$pricesArr[$key] = $arrvals;
			}
		}
		//another way displaying of price range is not correct
		if(empty($pricesArr['sale_price'])) {
			if(isset($pricesArr['regular_price'])) {
				$pricesArr['price'] = $pricesArr['regular_price'];
			}
		}
		return $pricesArr;
	}

	public function getCurrencyRate($fromCurrency, $toCurrency) {

		$options = $this->getOptions();
		$mode = !empty($options['options']['converter_type']) ? $options['options']['converter_type'] : 'cryptocompare';
		$freeConverterApiKey = !empty($options['options']['free_converter_apikey']) ? $options['options']['free_converter_apikey'] : 'a4472cb452c8fb230db0';
		$fixerConverterApiKey = !empty($options['options']['fixer_converter_apikey']) ? $options['options']['fixer_converter_apikey'] : 'a2697855aaf0f03e3bf46d2215106ef0';
		$currencylayerConverterApiKey = !empty($options['options']['currencylayer_converter_apikey']) ? $options['options']['currencylayer_converter_apikey'] : '905875ebd37315ce29619a9663b071aa';
		$oerConverterApiKey = !empty($options['options']['oer_converter_apikey']) ? $options['options']['oer_converter_apikey'] : 'e9f616eaa0fb49448d9fe3bd3b9bcd47';
		$fromCurrency = urlencode($fromCurrency);
		$toCurrency = urlencode($toCurrency);
		if (!$fromCurrency) {
			$errorMsg = sprintf(__("set main currency", WCU_LANG_CODE), $fromCurrency);
		} else {
			$errorMsg = sprintf(__("no data for %s", WCU_LANG_CODE), $toCurrency);
		}
		$rate = '';

		switch ($mode) {
			case 'free_converter':
				//http://free.currencyconverterapi.com/api/v6/convert?apiKey=sample-api-key&q=EUR_USD&compact=y
				$queryStr = sprintf("%s_%s", $fromCurrency, $toCurrency);
				$url = "http://free.currencyconverterapi.com/api/v6/convert?apiKey={$freeConverterApiKey}&q={$queryStr}&compact=y";
				$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				$currencyData = json_decode($res, true);
				$rate = !empty($currencyData[$queryStr]['val']) ? $currencyData[$queryStr]['val'] : $errorMsg;
				break;
			case 'currencyapi':
				//https://api.currencyapi.com/v3/latest?apikey=APIKEY&base_currency=USD&currencies=EUR
				$accessKey = ! empty( $options['options']['currencyapi_apikey'] ) ? $options['options']['currencyapi_apikey'] : '1d47d4f973a69d43edb9f8b4bb7cd4c8';
				$queryStr  = sprintf( '?apikey=%s&base_currency=%s&currencies=%s', $accessKey, $fromCurrency, $toCurrency );
				$url = 'https://api.currencyapi.com/v3/latest' . $queryStr;
				$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				$currencyData = json_decode($res, true);
				$rate = !empty($currencyData['data']) && !empty($currencyData['data'][$toCurrency]) && !empty($currencyData['data'][$toCurrency]['value']) ? $currencyData['data'][$toCurrency]['value'] : $errorMsg;
				break;
			case 'ratesapi':
/*				$url = "https://api.ratesapi.io/api/latest?base={$fromCurrency}&symbols={$toCurrency}";
				$url_get = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				$url_json =  json_decode($url_get,true);
				$rate = $url_json['rates'][$toCurrency];
				$rate = !empty($rate) ? $rate : $errorMsg;
				break;*/
			case 'ecb':
				if ( $fromCurrency === $toCurrency ) {
					$rate = 1;
				} else {
					$accessKey = ! empty( $options['options']['ecb_converter_apikey'] ) ? $options['options']['ecb_converter_apikey'] : '1d47d4f973a69d43edb9f8b4bb7cd4c8';
					$queryStr  = sprintf( '?apikey=%s&symbols=%s&base=%s', $accessKey, $toCurrency, $fromCurrency );
					$url       = 'https://api.apilayer.com/exchangerates_data/latest' . $queryStr;
					$url_get   = function_exists( 'curl_init' ) ? $this->_fileGetContentsCurl( $url ) : file_get_contents( $url );
					$url_json  = json_decode( $url_get, true );
					$rate      = $url_json['rates'][ $toCurrency ];
					$rate      = ! empty( $rate ) ? $rate : ( ( isset( $url_json['error']['info'] ) ) ? $url_json['error']['info'] : $errorMsg );
				}
			   break;
			case 'cryptocompare':
				//https://min-api.cryptocompare.com/data/price?fsym=ETH&tsyms=BTC
				$queryStr = sprintf("?fsym=%s&tsyms=%s", $fromCurrency, $toCurrency);
				$url = "https://min-api.cryptocompare.com/data/price" . $queryStr;
				$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				$currencyData = json_decode($res, true);
				$rate = !empty($currencyData[$toCurrency]) ? $currencyData[$toCurrency] : $errorMsg;
				break;
			case 'xe':
				//http://www.xe.com/currencyconverter/convert/?Amount=1&From=ZWD&To=CUP
				//https://www.xe.com/currencyconverter/convert/?Amount=4&From=USD&To=EUR
				$queryStr = sprintf("?Amount=1&From=%s&To=%s", $fromCurrency, $toCurrency);
				$url = "https://www.xe.com/currencyconverter/convert/" . $queryStr;
				$html = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				preg_match_all('/<span class=\'converterresult-toAmount\'>(.*?)<\/span>/s', $html, $matches);
				$rate = isset($matches[1][0]) ? floatval(str_replace(",", "", $matches[1][0])) : $errorMsg;
				break;
			case 'finance_yahoo':
				$queryStr = sprintf("%s%s=X", $fromCurrency, $toCurrency);
				$rate = '';
				$url = "https://query1.finance.yahoo.com/v8/finance/chart/" . $queryStr;
				$response = wp_remote_request($url, array('headers' => array('Content-Type' => 'application/json')));
				$data = is_wp_error($response) ? '' : json_decode(wp_remote_retrieve_body($response), true);
				if (isset($data['chart']) && isset($data['chart']['result']) && isset($data['chart']['result'][0])) {
					$meta = isset($data['chart']['result'][0]['meta']) ? $data['chart']['result'][0]['meta'] : array();
					$rate = isset($meta['regularMarketPrice']) ? $meta['regularMarketPrice'] : '';
				}
				if (empty($rate)) {
					//https://finance.yahoo.com/quote/USDBTC=X
					$queryStr = sprintf("%s%s=X", $fromCurrency, $toCurrency);
					$url = "https://finance.yahoo.com/quote/" . $queryStr;
					//$html = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$response = wp_remote_request($url, array('headers' => array('Content-Type' => 'application/json')));
					$html = is_wp_error($response) ? '' : wp_remote_retrieve_body($response);
					preg_match_all('/quote-header-info.*?fin-streamer.*?>(.*?)<\/fin-streamer>/s', $html, $matches);
					$rate = isset($matches[1][0]) ? floatval(str_replace(",", "", $matches[1][0])) : $errorMsg;
				}
				break;
			case 'poloniex':
				if ($fromCurrency == $toCurrency) {
					$rate = 1;
				} else {
					$fromCurrency = $fromCurrency == 'USD' ? 'USDT' : $fromCurrency;
					$isUsd = false;
					if ($toCurrency == 'USD') {
						$isUsd = true;
						$toCurrency = $fromCurrency;
						$fromCurrency = 'USDT';
					}
					$queryStr = sprintf("%s_%s", $fromCurrency, $toCurrency);
					$url = "https://poloniex.com/public?command=returnTicker";
					$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$currencyData = json_decode($res, true);
					$rate = isset($currencyData[$queryStr]) && !empty($currencyData[$queryStr]) ?
						($isUsd ? $currencyData[$queryStr]['last'] : 1 / $currencyData[$queryStr]['last']) : $errorMsg;
				}
				break;
			case 'cbr':
				if ($fromCurrency != 'RUB') {
					$rate = sprintf(__("set %s as main", WCU_LANG_CODE), 'RUB');
				} elseif ($fromCurrency == $toCurrency) {
					$rate = 1;
				} else {
					$url = "https://www.cbr-xml-daily.ru/daily_json.js";
					$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$currencyData = json_decode($res, true);
					$currencyData = !empty($currencyData) && isset($currencyData['Valute']) ? $currencyData['Valute']
						: $currencyData;
					$rate = isset($currencyData[$toCurrency]) ? intval($currencyData[$toCurrency]['Nominal'])
						/ $currencyData[$toCurrency]['Value'] : $errorMsg;
				}
				break;
			case 'nbp':
				if ($fromCurrency != 'PLN') {
					$rate = sprintf(__("set %s as main", WCU_LANG_CODE), 'PLN');
				} elseif ($fromCurrency == $toCurrency) {
					$rate = 1;
				} else {
					$url = "https://api.nbp.pl/api/exchangerates/tables/A/last/?format=json";
					$url_get = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$url_json = json_decode($url_get, true);
					$rates = $url_json[0]['rates'];
					$rate = '';
					foreach ($rates as $item) {
						if ($item['code'] == $toCurrency) {
							$rate = number_format(1 / $item['mid'], 4, '.', '');
							break;
						}
					}
					if (empty($rate)) {
						$url = "https://api.nbp.pl/api/exchangerates/tables/B/today/?format=json";
						$url_get = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
						$url_json = json_decode($url_get, true);
						$rates = $url_json[0]['rates'];
						foreach ($rates as $item) {
							if ($item['code'] == $toCurrency) {
								$rate = number_format(1 / $item['mid'], 4, '.', '');
								break;
							}
						}
					}
					$rate = !empty($rate) ? $rate : $errorMsg;
				}
				break;
			case 'pb':
				if ($fromCurrency != 'UAH') {
					$rate = sprintf(__("set %s as main", WCU_LANG_CODE), 'UAH');
				} elseif ($fromCurrency == $toCurrency) {
					$rate = 1;
				} else {
					$url = "https://api.privatbank.ua/p24api/exchange_rates?json&date=".date('d.m.Y');
					$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$currencyData = json_decode($res, true);
					$rate = $errorMsg;
					if (!empty($currencyData) && isset($currencyData['exchangeRate'])) {
						foreach ($currencyData['exchangeRate'] as $item) {
							if (isset($item['currency']) && $item['currency'] == $toCurrency) {
								$rate = number_format(isset($item['saleRate']) ? 1 / $item['saleRate']
									: 1 / $item['saleRateNB'], 4, '.', '');
								break;
							}
						}
					}
				}
				break;
			case 'bnr':
				if ($fromCurrency != 'RON') {
					$rate = sprintf(__("set %s as main", WCU_LANG_CODE), 'RON');
				} elseif ($fromCurrency == $toCurrency) {
					$rate = 1;
				} else {
					$dom = new DOMDocument();
					$dom->load("https://www.bnr.ro/nbrfxrates.xml");
					$rates = $dom->getElementsByTagName('Rate');
					$rate = '';
					if ($rates->length) {
						foreach ($rates as $item) {
							if ($item->getAttribute('currency') == $toCurrency) {
								$multiplier = $item->hasAttribute('multiplier') ? $item->getAttribute('multiplier') : 1;
								$rate = number_format($multiplier / $item->nodeValue, 4, '.', '');
								break;
							}
						}
					}
					$rate = !empty($rate) ? $rate : $errorMsg;
				}
				break;
			case 'fixer':
				$queryStr = sprintf("?access_key=%s&from=%s&to=%s&amount=1", $fixerConverterApiKey, $fromCurrency, $toCurrency);
				$url = "https://data.fixer.io/api/convert" . $queryStr;
				$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				$currencyData = json_decode($res, true);
				if ($currencyData['success']) {
					$rate = !empty($currencyData['result']) ? $currencyData['result'] : $errorMsg;
				} elseif ($currencyData['error']['code'] == 105 && $fromCurrency != 'EUR') {
					$rate = sprintf(__("set %s as main", WCU_LANG_CODE), 'EUR');
				} elseif ($currencyData['error']['code'] == 105) {
					$queryStr = sprintf("?access_key=%s", $fixerConverterApiKey);
					$url = "http://data.fixer.io/api/latest" . $queryStr;
					$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$currencyData = json_decode($res, true);
					$rate = !empty($currencyData['rates'][$toCurrency]) ? $currencyData['rates'][$toCurrency] : $errorMsg;
				} else {
					$rate = wp_trim_words($currencyData['error']['info'], 2, '');
				}
				break;
			case 'currencylayer':
				$queryStr = sprintf("?access_key=%s&from=%s&to=%s&amount=1", $currencylayerConverterApiKey, $fromCurrency, $toCurrency);
				$url = "https://api.currencylayer.com/convert" . $queryStr;
				$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				$currencyData = json_decode($res, true);
				if ($currencyData['success']) {
					$rate = !empty($currencyData['result']) ? $currencyData['result'] : $errorMsg;
				} elseif ($currencyData['error']['code'] == 105 && $fromCurrency != 'USD') {
					$rate = sprintf(__("set %s as main", WCU_LANG_CODE), 'USD');
				} elseif ($currencyData['error']['code'] == 105) {
					$queryStr = sprintf("?access_key=%s&amp;currencies=%s,%s&amp;date=%s", $currencylayerConverterApiKey, $fromCurrency, $toCurrency, date('Y-m-d'));
					$url = "http://api.currencylayer.com/live" . $queryStr;
					$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$currencyData = json_decode($res, true);
					$rate = !empty($currencyData['quotes'][$fromCurrency.$toCurrency]) ? $currencyData['quotes'][$fromCurrency.$toCurrency] : $errorMsg;
				} else {
					$rate = wp_trim_words($currencyData['error']['info'], 2, '');
				}
				break;
			case 'oer':
				$queryStr = sprintf("?app_id=%s&base=%s&symbols=%s", $oerConverterApiKey, $fromCurrency, $toCurrency);
				$url = "https://openexchangerates.org/api/latest.json" . $queryStr;
				$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
				$currencyData = json_decode($res, true);
				if (!empty($currencyData) && isset($currencyData['rates'])) {
					$rate = !empty($currencyData['rates'][$toCurrency]) ? $currencyData['rates'][$toCurrency] : $errorMsg;
				} elseif ((empty($currencyData) || in_array($currencyData['status'], [403, 429])) && $fromCurrency != 'USD') {
					$rate = sprintf(__("set %s as main", WCU_LANG_CODE), 'USD');
				} elseif (empty($currencyData) || in_array($currencyData['status'], [403, 429])) {
					$queryStr = sprintf("?app_id=%s&symbols=%s,%s", $oerConverterApiKey, $fromCurrency, $toCurrency);
					$url = "https://openexchangerates.org/api/latest.json" . $queryStr;
					$res = function_exists('curl_init') ? $this->_fileGetContentsCurl($url) : file_get_contents($url);
					$currencyData = json_decode($res, true);
					$rate = isset($currencyData['rates']) && !empty($currencyData['rates'][$toCurrency])
						? $currencyData['rates'][$toCurrency] : $errorMsg;
				} else {
					$rate = wp_trim_words($currencyData['description'], 3, '');
				}
				break;
			default:
				break;
		}
		return $rate;
	}

	public function saveOptions($options) {
		return update_option($this->getModule()->optionsDbOpt, escHtmlRecursively($options));
	}


	public function getOptions() {
		$options = get_option($this->getModule()->optionsDbOpt, array());
		if(empty($options) || !is_array($options)) {
			$options = $this->getModule()->getDefaultOptions();
		}
		return $options;
	}
	public function _getPriceDecimalsCount($currency, $val = 2, $currencies = array()) {
		if ($this->getModule()->nowCalcCartTotals || $this->getModule()->nowCalcOrderTotals) {
			$currency = $this->getModule()->defaultCurrency;	
		}
		return $this->_getRealPriceDecimalsCount($currency, $val, $currencies);
	}
	public function _getRealPriceDecimalsCount($currency, $val = 2, $currencies = array()) {
		$currencies = $this->getCurrencies();
		if( isset($currencies[$currency]['decimals']) && ($currencies[$currency]['decimals'] != 0) ) {
			$val = isset($currencies[$currency]['after_point'])
				? $currencies[$currency]['after_point']
				: $currencies[$currency]['decimals'];
			return intval($val);
		} else {
			if (isset($currencies[$currency]['decimals'])) {
				$val = 0;
			}
			return intval($val);
		}
	}
	private function _fileGetContentsCurl($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;
	}

	public function changeSettingFlags($setting) {
		$field = $this->getModule()->settingFlags;
		update_option($field, $setting);
	}

}
