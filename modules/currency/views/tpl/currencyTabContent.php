<?php $wcuShowPro = !empty($this->optionsProModule) ? '' : 'wcuShowPro'; ?>
<div class="wcuCurrenciesShell">
    <h3 style="margin-bottom: 1px;"><?php printf(__('WooCurrency %s', WCU_LANG_CODE), WCU_VERSION) ?></h3>
    <div class="nav-tab-wrapper">
        <a href="#" data-target="wcuCurrenciesTab" class="nav-tab nav-tab-active" ><?php _e('Currencies', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuOptionsTab" class="nav-tab"><?php _e('Options', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuSwitcherTab" class="nav-tab"><?php _e('Frontend switcher', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuTooltipTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Tooltip', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuConverterTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Currency converter', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuRatesTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Currency rates', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuCustomCssTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Custom CSS', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuGeoIpRulesTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Geo IP Rules', WCU_LANG_CODE)?></a>
		<a href="#" data-target="wcuFlagsTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Custom Flags', WCU_LANG_CODE)?></a>
		<a href="#" data-target="wcuCustomCurrencyTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Custom Currencies', WCU_LANG_CODE)?></a>
        <a href="#" data-target="wcuManualPricesTab" class="nav-tab <?php echo $wcuShowPro ?>"><?php _e('Manual Prices', WCU_LANG_CODE)?></a>
    </div>
    <div class="wcuTabContent" data-tab="wcuCurrenciesTab" >
        <?php echo htmlWcu::button(array('attrs' => 'class="button wcuAddCurrency page-title-action"', 'value' => __('Add currency', WCU_LANG_CODE)))?>

        <?php dispatcherWcu::doAction('getChildrenOneTab', array('currencies')) ?>

        <div class="wcuCurrenciesList">
            <div class="wcuCurrencyHeader row">
                <div class="col-md-1" align="center"></div>
                <div class="col-md-1"><?php _e('Currency code', WCU_LANG_CODE) ?></div>
                <?php if (!empty($this->flagsModule)) {?>
                <div class="col-md-1" style="width:60px;"><?php _e('Flag', WCU_LANG_CODE) ?></div>
                <?php }?>
                <div class="col-md-1"><?php _e('Title', WCU_LANG_CODE) ?></div>
                <div class="col-md-1"><?php echo __('Symbol', WCU_LANG_CODE) . ' / ' . __('Position', WCU_LANG_CODE); ?></div>
                <div class="col-md-1"><?php _e('Separators', WCU_LANG_CODE) ?> <i class="fa fa-question woobewoo-tooltip tooltipstered" title="<?php _e('You can set thousand and/or decimal separators for each currency separately. Leave the fields blank if you want the wooCommerce settings to be used.', WCU_LANG_CODE) ?>"></i></div>
				<div class="col-md-1"><?php _e('Cents', WCU_LANG_CODE) ?></div>
                <div class="col-md-1" style="min-width:140px;"><?php _e('Converter Rates ', WCU_LANG_CODE) ?><i class="fa fa-question woobewoo-tooltip tooltipstered" title="<?php _e('This rates is updated automatically with a selected Currency Aggregator.', WCU_LANG_CODE) ?>"></i></div>
                <div class="col-md-1" style="min-width:130px;"><?php _e('Manual Rates ', WCU_LANG_CODE) ?><i class="fa fa-question woobewoo-tooltip tooltipstered" title="<?php _e('This value overwrites the automatic rate from field Converter Rates. Input value in follow format example: 12.5035', WCU_LANG_CODE) ?>"></i></div>
	            <?php if ( !empty($this->optionsProModule) ) {?>
					<div class="col-md-1"><?php _e('Exchange Fee', WCU_LANG_CODE) ?><i class="fa fa-question woobewoo-tooltip tooltipstered" title="<?php _e('The exchange fee only works with the automatic rate.', WCU_LANG_CODE) ?>"></i></div>
	            <?php }?>
				<div style="clear: both;"></div>
            </div>
            <?php foreach($this->currencies as $params) {?>
            <?php
                $example = empty($params) ? 'wcuCurrencyItemExample' : '';
                $display = empty($params) ? 'display: none;' : '';
                $disabled = empty($params) ? 'disabled="disabled" ' : '';
                $etalon = !empty($params['etalon']) ? 'wcuCurrencyEtalonSelected' : '';
                ?>
            <?php $currName = !empty($params['name']) ? $params['name'] : $this->defCur;?>
            <div class="<?php echo $example ?> wcuCurrencyItem row" <?php echo $example ? 'style="display: none;"' : '' ?>>
                <div class="col-md-1">
                    <div class="col-xs-1" style="padding:0px;">
                        <i class="fa fa-arrows-v woobewoo-tooltip tooltipstered" title="<?php echo sprintf(__('Hold the cursor on the row to change the position of the currency.', WCU_LANG_CODE))?>" style="font-size: 20px; margin-top:5px;"></i>
                    </div>
                    <div class="col-xs-10" style="padding:0px;">
                        <?php echo htmlWcu::button(array(
                            'value' => !empty($params['etalon']) ? __('Main Currency', WCU_LANG_CODE) : __('Set as Main', WCU_LANG_CODE),
                            'attrs' => $disabled.' class="button wcuCurrencyEtalon '.$etalon.'" data-main="'.__('Main Currency', WCU_LANG_CODE).'" data-def="'.__('Set as Main', WCU_LANG_CODE).'"',
                            ))?>
                        <?php echo htmlWcu::hidden("{$this->dbPrefix}[etalon][]", array(
                            'value' => !empty($params['etalon']) ? $params['etalon'] : 0,
                            'attrs' => $disabled . 'class="wcuIsEtalon"'
                            ))?>
                    </div>
                </div>
                <div class="col-md-1">
                    <?php echo htmlWcu::selectbox("{$this->dbPrefix}[name][]", array(
                        'value' => !empty($params['name']) ? $params['name'] : $this->defCur,
                        'options' => $this->getModule()->getCurrencyNames(),
                        'attrs' => $disabled . '',
                        'data-def' => $this->defCur,
                        ))?>
                </div>
                <?php if (!empty($this->flagsModule)) {?>
                    <div class="col-md-1 wcuFlagsSelectBoxWrapper">
                        <?php dispatcherWcu::doAction('flagsList', $params, $example) ?>
                    </div>
                <?php } ?>
                <div class="col-md-1">
                    <?php echo htmlWcu::input("{$this->dbPrefix}[title][]", array(
                        'type' => 'text',
                        'value' => !empty($params['title']) ? $params['title'] : $this->defCur,
                        'attrs' => $disabled . '',
                        ))?>
                </div>
                <div class="col-md-1 wcuCurrencyCol">
                    <?php echo htmlWcu::selectbox("{$this->dbPrefix}[symbol][]", array(
                        'value' => !empty($params['symbol']) ? $params['symbol'] : $this->getModel()->getCurrencySymbol($this->defCur),
                        'options' => $this->getModule()->getCurrencySymbolsList(true),
                        'attrs' => $disabled . 'class="wcuCurrencySymbol"',
                        ))?>
                    <?php if (!$example) {?>
                        <?php dispatcherWcu::doAction('customSymbols', $currName) ?>
                        <i class="wcuCurrencySymbolEdit dashicons dashicons-edit woobewoo-tooltip tooltipstered"
                            <?php if ( !$this->customSymbolsModule ) { ?> title="<?php echo esc_html(sprintf(__('Type your symbol for this currency. Only in PRO version.', WCU_LANG_CODE), $this->pluginLink, $this->pluginLink))?>"<?php } ?>>
                        </i>
                    <?php }?>
					<?php echo htmlWcu::selectbox("{$this->dbPrefix}[position][]", array(
                        'value' => !empty($params['position']) ? $params['position'] : $this->defPos,
                        'options' => $this->getModule()->getCurrencyPositions(),
                        'attrs' => $disabled . '',
                        'data-def' => $this->defPos,
                        ))?>
                </div>
                <div class="col-md-1">
					<?php echo htmlWcu::input("{$this->dbPrefix}[tho_separator][]", array(
						'type' => 'text',
                        'value' => !empty($params['tho_separator']) ? $params['tho_separator'] : '',
						'attrs' => $disabled . '',
                        ))?> 
					<?php echo htmlWcu::input("{$this->dbPrefix}[dec_separator][]", array(
						'type' => 'text',
                        'value' => !empty($params['dec_separator']) ? $params['dec_separator'] : '',
						'attrs' => $disabled . '',
                        ))?> 
                </div>
                <div class="col-md-1">
                    <?php echo htmlWcu::selectbox("{$this->dbPrefix}[decimals][]", array(
                        'value' => isset($params['decimals']) ? $params['decimals'] : $this->defCur,
                        'options' => $this->getModule()->getCurrencyDecimalsList(),
                        'attrs' => $disabled . '',
                        'data-def' => $this->defCur,
                        ))?>
	                <?php if ( !empty($this->optionsProModule) ) {?>
	                    <?php dispatcherWcu::doAction('afterPointOption', $params) ?>
					<?php }?>
                </div>
                <div class="col-md-1" style="min-width:140px;">
                    <?php echo htmlWcu::input("{$this->dbPrefix}[rate][]", array(
                        'type' => 'text',
                        'value' => !empty($params['rate']) ? $params['rate'] : 1,
                        'attrs' => $disabled . 'class="wcuRate" readonly',
                        ))?>
                </div>
                <div class="col-md-1" style="min-width:130px;">
                    <?php echo htmlWcu::input("{$this->dbPrefix}[rate_custom][]", array(
                        'type' => 'text',
                        'value' => !empty($params['rate_custom']) ? $params['rate_custom'] : '',
                        'attrs' => $disabled . 'class="wcuRateCustom wcuOnlyNumbers"',
                        ))?>
                </div>
	            <?php if ( !empty($this->optionsProModule) ) {?>
					<div class="col-md-1 wcuExchangeFeeWrapper" style="min-width:130px;">
						<?php dispatcherWcu::doAction('exchangeFeeOption', $params) ?>
					</div>
	            <?php }?>
                <div class="col-md-2">
                    <?php echo htmlWcu::button(array(
                        'value' => __('Get rate', WCU_LANG_CODE),
                        'attrs' => $disabled . 'class="button wcuCurrencyConvert"',
                        ))?>
                    <?php echo htmlWcu::button(array(
                        'value' => __('Remove', WCU_LANG_CODE),
                        'attrs' => $disabled . 'class="button wcuCurrencyRemove"',
                        ))?>
                </div>
                <div style="clear: both;"></div>
            </div>
            <?php }?>
        </div>
    </div>
    <div class="wcuTabContent" data-tab="wcuOptionsTab" >
        <?php dispatcherWcu::doAction('getChildrenOneTab', array('options')) ?>
    </div>
    <div class="wcuTabContent" data-tab="wcuSwitcherTab">
        <div class="nav-tab-wrapper-child">
            <a href="#" data-target-child="wcuSwitcherDesignTab" class="nav-tab-child" ><?php _e('Design', WCU_LANG_CODE)?></a>
            <a href="#" data-target-child="wcuSwitcherDisplayRulesTab" class="nav-tab-child"><?php _e('Display rules', WCU_LANG_CODE)?></a>
        </div>
        <div class="wcuTabContentChild"  data-tab-child="wcuSwitcherDesignTab">
            <div class="wcuTabContentInner">
                <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_switcher','design_tab',false,true)) ?>
            </div>
        </div>
        <div class="wcuTabContentChild wcuDisplayRulesTab"  data-tab-child="wcuSwitcherDisplayRulesTab">
            <div class="wcuTabContentInner">
                <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_switcher','display_rules_tab',false,false)) ?>
            </div>
        </div>
    </div>
    <div class="wcuTabContent" data-tab="wcuTooltipTab" >
        <div class="nav-tab-wrapper-child">
            <a href="#" data-target-child="wcuTooltipDesignTab" class="nav-tab-child" ><?php _e('Design', WCU_LANG_CODE)?></a>
            <a href="#" data-target-child="wcuTooltipDisplayRulesTab" class="nav-tab-child"><?php _e('Display rules', WCU_LANG_CODE)?></a>
        </div>
        <div class="wcuTabContentChild"  data-tab-child="wcuTooltipDesignTab">
            <div class="wcuTabContentInner">
                <?php if ( !empty($this->optionsProModule) ) {?>
                    <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_tooltip','design_tab',true,false)) ?>
                <?php } else { ?>
                    <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=currency_tooltip_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-tooltip-design.png"></a>
                <?php }?>
            </div>
        </div>
        <div class="wcuTabContentChild wcuDisplayRulesTab"  data-tab-child="wcuTooltipDisplayRulesTab">
            <div class="wcuTabContentInner">
                <?php if ( !empty($this->optionsProModule) ) {?>
                    <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_tooltip','display_rules_tab',true,false)) ?>
                <?php } else { ?>
                    <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=currency_tooltip_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-rules.png"></a>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="wcuTabContent" data-tab="wcuConverterTab" >
        <div class="nav-tab-wrapper-child">
            <a href="#" data-target-child="wcuConverterDesignTab" class="nav-tab-child" ><?php _e('Design', WCU_LANG_CODE)?></a>
            <a href="#" data-target-child="wcuConverterDisplayRulesTab" class="nav-tab-child"><?php _e('Display rules', WCU_LANG_CODE)?></a>
        </div>
        <div class="wcuTabContentChild"  data-tab-child="wcuConverterDesignTab">
            <div class="wcuTabContentInner">
                <?php if ( !empty($this->optionsProModule) ) {?>
                    <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_converter','design_tab',true,true)) ?>
                <?php } else { ?>
                    <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=currency_converter_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-converter-design.png"></a>
                <?php }?>
            </div>
        </div>
        <div class="wcuTabContentChild wcuDisplayRulesTab"  data-tab-child="wcuConverterDisplayRulesTab">
            <div class="wcuTabContentInner">
                <?php if ( !empty($this->optionsProModule) ) {?>
                    <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_converter','display_rules_tab',true,false)) ?>
                <?php } else { ?>
                    <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=currency_converter_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-rules.png"></a>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="wcuTabContent" data-tab="wcuRatesTab" >
        <div class="nav-tab-wrapper-child">
            <a href="#" data-target-child="wcuRatesDesignTab" class="nav-tab-child" ><?php _e('Design', WCU_LANG_CODE)?></a>
            <a href="#" data-target-child="wcuRatesDisplayRulesTab" class="nav-tab-child"><?php _e('Display rules', WCU_LANG_CODE)?></a>
        </div>
        <div class="wcuTabContentChild"  data-tab-child="wcuRatesDesignTab">
            <div class="wcuTabContentInner">
                <?php if ( !empty($this->optionsProModule) ) {?>
                    <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_rates','design_tab',true,true)) ?>
                <?php } else { ?>
                    <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=currency_rates_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-rates-design.png"></a>
                <?php }?>
            </div>
        </div>
        <div class="wcuTabContentChild wcuDisplayRulesTab"  data-tab-child="wcuRatesDisplayRulesTab">
            <div class="wcuTabContentInner">
                <?php if ( !empty($this->optionsProModule) ) {?>
                    <?php dispatcherWcu::doAction('getChildrenMultipleTab', array('currency_rates','display_rules_tab',true,false)) ?>
                <?php } else { ?>
                    <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=currency_rates_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-rules.png"></a>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="wcuTabContent" data-tab="wcuCustomCssTab" >
        <div class="wcuTabContentInner">
            <?php if ( !empty($this->optionsProModule) ) {?>
                <?php dispatcherWcu::doAction('getChildrenOneTab', array('custom_css',true)) ?>
            <?php } else { ?>
                <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=custom_css_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-css.png"></a>
            <?php }?>
        </div>
    </div>
    <div class="wcuTabContent wcuDisplayRulesTab" data-tab="wcuGeoIpRulesTab" >
        <div class="wcuTabContentInner">
            <?php if ( !empty($this->optionsProModule) ) {?>
                <?php dispatcherWcu::doAction('getChildrenOneTab', array('geoip_rules',true)) ?>
                <?php dispatcherWcu::doAction('geoIpRules') ?>
            <?php } else { ?>
                <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=geoip_rules_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-geoiprules.png"></a>
            <?php }?>
        </div>
    </div>
	<div class="wcuTabContent wcuDisplayRulesTab" data-tab="wcuFlagsTab" >
        <div class="wcuTabContentInner">
            <?php if ( !empty($this->optionsProModule) ) {?>
                <?php dispatcherWcu::doAction('getChildrenOneTab', array('flags',true)) ?>
                <?php dispatcherWcu::doAction('wcuFlagsSetting') ?>
            <?php } else { ?>
                <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=geoip_rules_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-custom-flags.png"></a>
            <?php }?>
        </div>
    </div>
	<div class="wcuTabContent wcuDisplayRulesTab" data-tab="wcuCustomCurrencyTab" >
        <div class="wcuTabContentInner">
            <?php if ( !empty($this->optionsProModule) ) {?>
                <?php dispatcherWcu::doAction('getChildrenOneTab', array('custom_currency',true)) ?>
				<?php dispatcherWcu::doAction('wcuCustomCurrency') ?>
            <?php } else { ?>
                <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=geoip_rules_tab&utm_campaign=woocurrency" target="_blank"><img class="wcuPreviewProOptions" src="<?php echo $this->modulePath?>img/module-custom-currency.png"></a>
            <?php }?>
        </div>
    </div>
    <div class="wcuTabContent wcuDisplayRulesTab" data-tab="wcuManualPricesTab" >
        <div class="wcuTabContentInner">
            <?php if ( !empty($this->optionsProModule) ) {?>
                <?php dispatcherWcu::doAction('getChildrenOneTab', array('manual_prices',true)) ?>
            <?php } else { ?>
                <a class="wcuProPreviewLink" href="<?php echo $this->pluginLink ?>?utm_source=plugin&utm_medium=manual_prices_tab&utm_campaign=woocurrency" target="_blank">PRO option</a>
            <?php }?>
        </div>
    </div>
    <div class="wcuTabContentCurrencyError">
        <div class="wcuTabContentCurrencyErrorItem wcuTabContentCurrencyErrorSetMain alert alert-danger-background" style="display:none">
            <?php echo __('Please set a main currency before getting the exchange rate.', WCU_LANG_CODE);?>
        </div>
        <div class="wcuTabContentCurrencyErrorItem wcuTabContentCurrencyErrorSetMainSave alert alert-danger-background" style="display:none">
            <?php echo __('Please set a main currency before saving.', WCU_LANG_CODE);?>
        </div>
        <div class="wcuTabContentCurrencyErrorItem wcuTabContentCurrencyErrorRemoveDuplicate alert alert-danger-background" style="display:none">
            <?php echo __('Please remove duplicate currencies before saving.', WCU_LANG_CODE);?>
        </div>
        <div class="wcuTabContentCurrencyErrorItem wcuTabContentCurrencyErrorSetRateFormat alert alert-danger-background" style="display:none">
            <?php echo __('Please set current rate format before saving. Example: 1.584', WCU_LANG_CODE);?>
        </div>
		<div class="wcuTabContentCurrencyErrorItem wcuTabContentCurrencyErrorSetCustomFormat alert alert-danger-background" style="display:none">
            <?php echo __('Please input value in string format. Example: ASD', WCU_LANG_CODE);?>
        </div>
        <div class="wcuTabContentCurrencyEtalonNotice" style="display:none">
            <?php echo __('Prices in this currency are shown on frontend when user first time visits store.', WCU_LANG_CODE);?>
        </div>
    </div>

    <?php echo htmlWcu::hidden('mod', array('value' => 'currency'))?>
    <?php echo htmlWcu::hidden('action', array('value' => 'saveCurrencyTab'))?>
    <?php echo htmlWcu::hidden('pl', array('value' => WCU_CODE))?>
</div>
