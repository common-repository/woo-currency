<?php
class currency_switcherViewWcu extends viewWcu
{
    /**
     * Retrive main module functionality to display
     *
     * @param array $previewOptions
     * @param bool $isShortcode
     *
     * @return string
     */
	public function getCurrencySwitcher( $previewOptions = array(), $isShortcode = false, $mode = '') {
	    $currencyModule = frameWcu::_()->getModule( 'currency' );
	    $currencyModel  = $currencyModule->getModel();
	    if ( ! $previewOptions || $isShortcode ) {
		    $options = $currencyModel->getOptions();
	    } else {
		    $options = $previewOptions;
	    }
	    $moduleName = 'currency_switcher';

	    if ( 'extended' === $mode ) {
		    $templateType       = 'simple';
		    $templateTypeDesign = 'Dropdown';
		    $templateTypeStyle  = '.dropdown';
	    } else {
		    $templateType       = strtolower( isset( $options[ $moduleName ]['design_tab']['type'] ) ? $options[ $moduleName ]['design_tab']['type'] : '' );
		    $templateTypeDesign = '';
		    $templateTypeStyle = '';
	    }

        switch ( $templateType ) {
            case 'simple':
	            if ( 'extended' === $mode ) {
		            $currencies = $this->getCurrenciesOpts( 'floating', array( 'name', 'symbol', 'flag' ) );
	            } else {
			            $templateTypeDesign = isset( $options[ $moduleName ]['design_tab']['design'] ) ? $options[ $moduleName ]['design_tab']['design'] : '';
			            $templateTypeStyle  = strtolower( isset( $options[ $moduleName ]['design_tab']['design'] ) ? '.' . $options[ $moduleName ]['design_tab']['design'] : '' );
		            if ( ! empty( $templateTypeDesign ) ) {
			            $currencies = $this->getCurrenciesOpts( $options[ $moduleName ]['design_tab']['show'], 'name' );
		            }
	            }
                break;
            case 'rotating':
                $currencies = $this->getCurrenciesOpts( $options[ $moduleName ][ 'design_tab' ][ 'type' ], 'name' );
                break;
            case 'floating':
				$showFloatingOrder = $options[ $moduleName ][ 'design_tab' ][ 'show_floating_order' ];
				//if ($previewOptions || !$isShortcode) {
                if ($previewOptions || count($showFloatingOrder) == 1) {
					$showFloatingOrder = explode(',', $showFloatingOrder[0]);
				}
                $currencies = $this->getCurrenciesOpts( $options[ $moduleName ][ 'design_tab' ][ 'type' ], $showFloatingOrder );
                $switcherOpeningButton = !empty( $options[ $moduleName ][ 'design_tab' ][ 'switcher_opening_button' ] ) ? $options[ $moduleName ][ 'design_tab' ][ 'switcher_opening_button' ] : 'currency_codes';
                $currenciesButton = $this->getCurrenciesOpts( $switcherOpeningButton );
                $this->assign( 'currenciesButton', $currenciesButton );
                break;
        }
        $defOptions = $currencyModule->getDefaultOptions();
		if (is_product()) $currencyModule->initCurrency();
        $currentCurrency = $currencyModule->getCurrentCurrency();
        $designTab = array_keys( $defOptions[ $moduleName ][ 'design_tab' ] );
        $displayRulesTab = array_keys( $defOptions[ $moduleName ][ 'display_rules_tab' ] );
        $prepareParams = $currencyModule->getView()->_prepareOptionsParams( $options, $defOptions );
        frameWcu::_()->getModule( 'templates' )->loadCoreJs();
	    frameWcu::_()->addStyle( 'frontend.switcher', $this->getModule()->getModPath() . 'css/frontend.switcher.css' );
        $proModule = frameWcu::_()->getModule( 'promo' )->getProOptions();
        $isProModule = frameWcu::_()->getModule( 'promo' )->getProOptions( true );
        if ( ( $templateType === 'simple' ) && ( $templateTypeDesign === 'Dropdown' ) ) {
            if ( !empty( $proModule ) ) {
                return $proModule->getSimpleDropdown( $currencies, $currentCurrency, $designTab, $displayRulesTab, $prepareParams, $isShortcode, $mode );
            } else {
                $templateTypeDesign = 'Classic';
                $templateTypeStyle  = '.classic';
            }
        } elseif ($templateType == 'floating'){
            frameWcu::_()->getModule( 'templates' )->loadSlimscroll();
        }
        frameWcu::_()->addStyle( 'switcher.' . $templateType . $templateTypeStyle, $this->getModule()->getModPath() . 'css/switcher.' . $templateType . $templateTypeStyle . '.css' );
        frameWcu::_()->addScript( 'frontend.switcher', $this->getModule()->getModPath() . 'js/frontend.switcher.js' );
        frameWcu::_()->getModule( 'templates' )->loadFontAwesome();
        $this->assign( 'currencies', $currencies );
        $this->assign( 'currentCurrency', $currentCurrency );
        $this->assign( 'designTab', $designTab );
        $this->assign( 'displayRulesTab', $displayRulesTab );
        $this->assign( 'optionsParams', $prepareParams );
        $this->assign( 'proModule', $isProModule );
        $this->assign( 'isShortcode', $isShortcode );

        return parent::getContent( 'switcher' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $templateType . $templateTypeDesign ) ) ) );
    }
    public function getCurrenciesOpts( $template, $options = '' )
    {
        $currencies = frameWcu::_()->getModule( 'currency' )->getModel()->getCurrencies();
        $currencySymbols = frameWcu::_()->getModule( 'currency' )->getCurrencySymbols();
        if ( !empty( frameWcu::_()->getModule( 'flags' ) ) ) {
            $flagsList = frameWcu::_()->getModule( 'flags' )->getFlagsList();
        }
        $currenciesOpts = array();
        foreach ( $currencies as $c ) {
            switch ( $template ) {
                case 'flags':
                    $name = ( !empty( $c[ 'name' ] ) ) ? $c[ 'name' ] : '';
                    $flag = ( !empty( $c[ 'flag' ] ) && !empty( $flagsList[ $c[ 'flag' ] ] ) ) ? $flagsList[ $c[ 'flag' ] ] : '';
                    $currenciesOpts[ $c[ 'name' ] ] = !empty( $flag ) ? '<img src="' . $flag . '" alt="' . esc_attr($name) . '">' : $name;
                    break;
                case 'currency_codes':
                    $name = ( !empty( $c[ 'name' ] ) ) ? $c[ 'name' ] : '';
                    $currenciesOpts[ $name ] = $name;
                    break;
                case 'currency_symbols':
                    $name = ( !empty( $c[ 'symbol' ] ) && !empty( $currencySymbols[ $c[ 'symbol' ] ] ) ) ? $currencySymbols[ $c[ 'symbol' ] ] : '';
                    $currenciesOpts[ $c[ 'name' ] ] = $name;
                    break;
                case 'rotating':
                    $name = ( !empty( $c[ 'name' ] ) ) ? $c[ 'name' ] : '';
                    $title = ( !empty( $c[ 'title' ] ) ) ? $c[ 'title' ] : '';
					$flag = ( !empty( $c[ 'flag' ] ) && !empty( $flagsList[ $c[ 'flag' ] ] ) ) ? '<img src="' .$flagsList[ $c[ 'flag' ] ]. '" alt="' . esc_attr($name) . '">' : $title;
                    $currenciesOpts[ $name ][ 'title' ] = $title;
					$currenciesOpts[ $name ][ 'flag' ] = $flag;
                    $currenciesOpts[ $name ][ 'symbol' ] = ( !empty( $c[ 'symbol' ] ) && !empty( $currencySymbols[ $c[ 'symbol' ] ] ) ) ? $currencySymbols[ $c[ 'symbol' ] ] : '';
                    break;
                case 'floating':
                    $name = ( !empty( $c[ 'name' ] ) ) ? $c[ 'name' ] : '';
                    $title = ( !empty( $c[ 'title' ] ) ) ? $c[ 'title' ] : '';
                    $symbol = ( !empty( $c[ 'symbol' ] ) && !empty( $currencySymbols[ $c[ 'symbol' ] ] ) ) ? $currencySymbols[ $c[ 'symbol' ] ] : '';
                    $rate = ( !empty( $currencies[ $name ][ 'rate' ] ) ) ? $currencies[ $name ][ 'rate' ] : '';
                    $flag = ( !empty( $c[ 'flag' ] ) && !empty( $flagsList[ $c[ 'flag' ] ] ) ) ? $flagsList[ $c[ 'flag' ] ] : '';
                    $flag = !empty( $flag ) ? '<img src="' . $flag . '" alt="' . esc_attr($name) . '">' : $name;
                    foreach ( $options as $option ) {
                        $currenciesOpts[ $name ][ $option ] = ${$option};
                    }
                    break;
                default:
                    break;
            }
        }
        return $currenciesOpts;
    }
}
