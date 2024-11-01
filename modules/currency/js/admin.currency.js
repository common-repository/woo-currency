jQuery(document).ready(function() {
    var currenciesShell = jQuery('.wcuCurrenciesShell'),
        targetToggle = jQuery('[data-target-toggle]'),
        chosenOptionsObj = jQuery.extend({}, g_wcuChosenOptions, {
            width: '100%'
        });

	jQuery("body").find("[name='wcu_options[options][converter_type]'] option[value=ratesapipro]").prop('disabled', true).attr('disabled', true);
	jQuery("body").find("[name='wcu_options[options][converter_type]'] option[value=ecbpro]").prop('disabled', true).prop('disabled', true).attr('disabled', true);

    jQuery('#wcuOptionsTab select, #wcuSwitcherTab select').chosen(g_wcuChosenOptions);
    if (currenciesShell.length && typeof jQuery.fn.wpTabs == 'function') {
        currenciesShell.wpTabs({
            change: function(selector) {
                window.location.hash = selector;
            }
        });
    }
    targetToggle.on('change ifChanged', function(e) {
        e.preventDefault();

        var self = jQuery(this),
            value = (self.attr('type') === 'checkbox') ? self.is(':checked') : self.val();

        jQuery(self.data('target-toggle')).each(function() {
            var $this = jQuery(this),
                showValue = String($this.data('target-show')),
                hideValue = String($this.data('target-hide'));

            if (showValue) {
                var showValueArr = showValue.split(',');

                showValueArr = showValueArr.length == 1 && showValueArr[0] == 'all' ? [] : showValueArr;

                if (toeInArray(value, showValueArr) != -1) {
                    $this.fadeIn();
                } else {
                    $this.fadeOut();
                }
            } else if (hideValue) {
                var hideValueArr = hideValue.split(',');

                if (toeInArray('disable', hideValueArr) != -1) {
                    if (!value || value == 'disable' || toeInArray(value, hideValueArr) != -1) {
                        $this.fadeOut();
                    } else {
                        $this.fadeIn();
                    }
                } else {
                    if (toeInArray(value, hideValueArr) != -1) {
                        $this.fadeOut();
                    } else {
                        $this.fadeIn();
                    }
                }
            } else {
                $this.fadeToggle();
            }
        });
    });

    // Currencies Sub Tab
    if (typeof jQuery.fn.sortable == 'function') {
        jQuery('.wcuCurrenciesList').sortable();
    }
    jQuery('.wcuAddCurrency').on('click', function() {
        var list = jQuery('.wcuCurrenciesList'),
            defItem = list.find('.wcuCurrencyItemExample:first'),
            newItem = defItem.clone(true);

        list.append(newItem);
        newItem.find('input, select, button').attr("disabled", false);
        newItem.show();

        return false;
    });
    jQuery('[name="wcu_currencies[name][]"]')
        .on('click', function() {
            var $name = jQuery(this);
            $name.data('value', $name.val());
        }).on('change', function() {
            var $name = jQuery(this),
                $parent = $name.parents('.wcuCurrencyItem:first'),
                $title = $parent.find('[name="wcu_currencies[title][]"]'),
                nameValue = $name.val();

            $parent.find('[name="wcu_currencies[symbol][]"]').val(nameValue);
            $parent.find('[name="wcu_currencies[flag][]"]').val(nameValue);

            if (jQuery.trim($title.val()) == $name.data('value')) {
                $title.val(nameValue);
            }
        });
    jQuery('.wcuCurrencyConvert').on('click', function() {
        var parent = jQuery(this).parents('.wcuCurrencyItem:first'),
            rateInput = parent.find('.wcuRate'),
            form = jQuery('form#mainform'),
            saveBtn = form.find('button[name="save"]');

        if (form.find('[name="wcu_currencies[etalon][]"][value="1"]').length === 1) {
            rateInput.val('loading ...');
            jQuery.sendFormWcu({
                data: {
                    mod: 'currency',
                    action: 'getCurrencyRate',
                    currency_name: parent.find('[name="wcu_currencies[name][]"]').val(),
                    default_currency: form.find('[name="wcu_currencies[etalon][]"][value="1"]')
                        .parents('.wcuCurrencyItem:first')
                        .find('[name="wcu_currencies[name][]"]')
                        .val()
                },
                btn: saveBtn,
                onSuccess: function(res) {
                    if (!res.error) {
                        rateInput.val(res.data.rate);
                    }
                }
            });
        } else {
            // If don't set main currency before getting rates show ERROR
            jQuery(".wcuTabContentCurrencyErrorSetMain").fadeIn();
            jQuery(".wcuCurrencyItem:eq(1)").find(".wcuCurrencyEtalon").addClass("wcuCurrencyEtalonWarning");
            setTimeout(function() {
                jQuery(".wcuTabContentCurrencyErrorSetMain").fadeOut();
                jQuery(".wcuCurrencyItem:eq(1)").find(".wcuCurrencyEtalon").removeClass("wcuCurrencyEtalonWarning");
            }, 3000)
        }
        return false;
    });
    jQuery('.wcuCurrencyRemove').on('click', function() {
        jQuery(this).parents('.wcuCurrencyItem:first').remove();

        return false;
    });
	
	jQuery('[name="wcu_currencies[decimals][]"]')
		.on('change', function() {
		var $decimals = jQuery(this),
			$parent = $decimals.parents('.wcuCurrencyItem:first'),
			decimalsValue = $decimals.val();
		
		if (decimalsValue != 2) {
		    $parent.find('.wcuAfterPoint').addClass('wcuHidden');
        } else {
			$parent.find('.wcuAfterPoint').removeClass('wcuHidden');
        }
	}).trigger('change');

    // Show tooltip for etalon currency button
    function showEtalonTooltip() {
        if (jQuery('.wcuCurrencyEtalonSelected').length>0) {
            etalon = jQuery('.wcuCurrencyEtalonSelected');
            etalon.on('mouseenter mouseleave');
            etalonText = jQuery(".wcuTabContentCurrencyEtalonNotice").html();
            etalon.attr('title',etalonText).addClass('woobewoo-tooltip').addClass('tooltipstered');
            wcuInitTooltips();
            etalon.removeAttr("title");
        }
    }
    showEtalonTooltip();
    jQuery('.wcuCurrencyEtalon').on('click', function() {
        var self = jQuery(this),
        row = self.parents('.wcuCurrencyItem:first'),
        shell = self.parents('.wcuCurrenciesList:first');
        jQuery('.wcuCurrencyEtalon').off('mouseenter mouseleave').removeAttr("title").removeClass("woobewoo-tooltip").removeClass("tooltipstered").removeClass("wcuCurrencyEtalonSelected");
        self.addClass('wcuCurrencyEtalonSelected');
        showEtalonTooltip();
        shell.find('.wcuIsEtalon').val(0);
        shell.find('.wcuCurrencyEtalon').html(self.data('def'));
        row.find('.wcuIsEtalon').val(1);
        self.html(self.data('main'));
        return false;
    });

    // Switcher Sub Tab
    var switcherType = jQuery('[name="wcu_options[currency_switcher][design_tab][type]"]');

    switcherType.on('change', function() {
        var value = jQuery(this).val(),
            switcherSide = jQuery('[name="wcu_options[currency_switcher][design_tab][side]"]'),
            switcherSideOpts = switcherSide.find('option');

        switcherSideOpts.filter('[data-type]').hide();
        switcherSideOpts.filter('[data-type="' + value + '"]').show();

        if (toeInArray(value, ['floating', 'rotating']) != -1 && toeInArray(switcherSide.val(), ['top', 'bottom']) != -1) {
            switcherSide.val('left');
        }
        if (!value) {
            jQuery('[data-hide-with-all="1"]').hide();
        } else {
            jQuery('[data-hide-with-all="1"]').show();
            jQuery('#wcuSwitcherTab [data-target-toggle]').not('[name="wcu_options[currency_switcher][design_tab][type]"]').trigger('change');
        }
    });
    switcherType.trigger('change');

    // Fix for correct closing of colorpickers
    jQuery(document).click(function(e) {
        if (!jQuery(e.target).is(".colour-picker, .iris-picker, .iris-picker-inner")) {
            jQuery('.iris-picker.iris-border').hide();
        }
    });
    jQuery('.colour-picker').click(function(event) {
        jQuery('.iris-picker.iris-border').hide();
        jQuery(this).iris('show');
        return false;
    });

    jQuery(document).keyup(function(e) {
        if (e.keyCode === 27) jQuery('.wcuTabContentCurrencySwitcherPreviewOverlay').fadeOut();
    });

    jQuery(".wcuCurrencySwitcherLookPreviewButton").click(function(e) {
        e.preventDefault();
        jQuery(".wcuTabContentCurrencySwitcherPreviewOverlay").fadeIn();
        wcuCurrencySwitcherLookPreviewAjax();
    });

    jQuery(".wcuTabContentCurrencySwitcherPreviewClose").click(function(e) {
        e.preventDefault();
        jQuery(".wcuTabContentCurrencySwitcherPreviewOverlay").fadeOut();
    });

    jQuery(document).mouseup(function(e) {
        var container = jQuery(".wcuTabContentCurrencySwitcherPreviewWrapper");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            jQuery(".wcuTabContentCurrencySwitcherPreviewOverlay").fadeOut();
        }
    });
    // LivePreview functions END

    // Hide Display Mode Selects functions START
    function hideWcuSelectDisplayMode(displayMode) {
        switch (displayMode) {
            case 'simple':
                //jQuery(".wcuTabContentChildActive tr").show();
                break;
            case 'floating':
                //jQuery(".wcuTabContentChildActive tr").show();
                break;
            case 'rotating':
                //jQuery(".wcuTabContentChildActive tr").show();
                break;
            default:
        }
    }


    jQuery(".wcuSwitcherRadioLabel").click(function () {
        var name = jQuery(this).closest("input:radio").attr("name");
        var currVal = jQuery(this).closest("input:radio").val();
        var oldVal;
        jQuery("[name='" + name + "']").each(function (index) {
            var input = jQuery("[name='" + name + "']:eq(" + index + ")"),
                attr = input.attr('checked');
            if (typeof attr !== typeof undefined && attr !== false) {
                oldVal = jQuery("[name='" + name + "']:eq(" + index + ")").val();
            }
            input.parent().removeClass('checked');
            input.removeAttr('checked');
        });
        jQuery(this).closest("input:radio").attr("checked", true);
        jQuery(this).addClass("checked");
        var currentTab = jQuery(this).closest(".wcuTabContent");
        jQuery(currentTab).find("tr").each(function (index) {

            if (jQuery(this).attr('data-target-hide') === oldVal) {
                jQuery(this).show();
            }
            if (jQuery(this).attr('data-target-hide') === currVal) {
                jQuery(this).hide();
            }
            if (jQuery(this).attr('data-target-show') === oldVal) {
                jQuery(this).hide();
            }
            if (jQuery(this).attr('data-target-show') === currVal) {
                jQuery(this).show();
            }

        });
    });

    var converterValue = jQuery("select[name='wcu_options[options][converter_type]']").val();
    var converterApiRows = jQuery('.wcuOptionsConverterRow');
	converterApiRows.fadeOut();
    switch (converterValue) {
        case 'free_converter':
			jQuery(".wcuOptionsFreeConverterApiKey").fadeIn();
		case 'currencyapi':
			jQuery(".wcuOptionsCurrencyConversionApiKey").fadeIn();
            break;
        case 'ecb':
			jQuery(".wcuOptionsEcbApiKey").fadeIn();
            break;
        case 'fixer':
			jQuery(".wcuOptionsFixerApiKey").fadeIn();
            break;
		case 'currencylayer':
			jQuery(".wcuOptionsCurrencylayerApiKey").fadeIn();
			break;
		case 'oer':
			jQuery(".wcuOptionsOerApiKey").fadeIn();
			break;
        default:
			converterApiRows.fadeOut();
            break;
    }

    var displayMode = jQuery("select[name='wcu_options[currency_switcher][design_tab][type]']").val();
    hideWcuSelectDisplayMode(displayMode);

    jQuery("select[name='wcu_options[currency_switcher][design_tab][type]']").change(function() {
        var displayMode = jQuery("select[name='wcu_options[currency_switcher][design_tab][type]']").val();
        hideWcuSelectDisplayMode(displayMode);
    })
    // Hide Display Mode Selects functions extend

    jQuery('.wcuOnlyNumbers').on('input', function(e) {
      this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });

    // Validate Currencies TAB before Save START
    jQuery("#mainform").submit(function(e) {
        jQuery(".wcuCurrencyItem").removeClass("wcuCurrencyItemWarning");
        var seen = {};
        var i = jQuery(".wcuCurrencyItem").length;
        var isMatch = false;
        while (i) {
            seen = {};
            jQuery(".wcuCurrencyItem").each(function(index) {
                if (index !== 0) {
                    var txt = jQuery(this).find('select[name="wcu_currencies[name][]"]').val();
                    if (seen[txt]) {
                        jQuery('.wcuCurrencyItem:eq(' + index + ')').addClass("wcuCurrencyItemWarning");
                        isMatch = true;
                    } else {
                        seen[txt] = true;
                    }
                }
            });
            i = i - 1;
        }
        // If find Matches Currencies show ERROR
        if (isMatch) {
            e.preventDefault();
            jQuery(".wcuTabContentCurrencyErrorRemoveDuplicate").fadeIn();
            setTimeout(function() {
                jQuery(".wcuTabContentCurrencyErrorRemoveDuplicate").fadeOut();
            }, 3000);
            return true;
        }

		jQuery(".wcuCustomCurrencyCode").each(function(index) {
			if (index !== 0) {
            	if ( !jQuery(this).val().match(/^[A-Z]+$/) ) {
					jQuery(this).addClass('wcuCustomCurrencyCodeWarning');
				}
			}
        });

		if (jQuery(".wcuCustomCurrencyCodeWarning").length>0) {
			e.preventDefault();
            jQuery(".wcuTabContentCurrencyErrorSetCustomFormat").fadeIn();
            setTimeout(function() {
                jQuery(".wcuCustomCurrencyCode").removeClass("wcuCustomCurrencyCodeWarning");
                jQuery(".wcuTabContentCurrencyErrorSetCustomFormat").fadeOut();
            }, 3000);
        }

        jQuery(".wcuCurrencyItem").each(function(index) {
            if (index !== 0) {
                rate = jQuery(this).find(".wcuRate");
                value = jQuery(this).find(".wcuRate").val();
				valueCustom = jQuery(this).find(".wcuRateCustom").val();
                if( !(/^([0-9]+(\.[0-9]+)?|Infinity)$/.test(value)) && !(/^([0-9]+(\.[0-9]+)?|Infinity)$/.test(valueCustom)) ) {
                    rate.addClass('wcuRateWarning');
                }
            }
        });

        if (jQuery(".wcuRateWarning").length>0) {
			e.preventDefault();
            jQuery(".wcuTabContentCurrencyErrorSetRateFormat").fadeIn();
            setTimeout(function() {
                jQuery(".wcuRate").removeClass("wcuRateWarning");
                jQuery(".wcuTabContentCurrencyErrorSetRateFormat").fadeOut();
            }, 3000);
        }

        // If don't set main currency show ERROR
        if (jQuery("#mainform").find('[name="wcu_currencies[etalon][]"][value="1"]').length === 0) {
            e.preventDefault();
            jQuery(".wcuTabContentCurrencyErrorSetMainSave").fadeIn();
            jQuery(".wcuCurrencyItem:eq(1)").find(".wcuCurrencyEtalon").addClass("wcuCurrencyEtalonWarning");
            setTimeout(function() {
                jQuery(".wcuTabContentCurrencyErrorSetMainSave").fadeOut();
                jQuery(".wcuCurrencyItem:eq(1)").find(".wcuCurrencyEtalon").removeClass("wcuCurrencyEtalonWarning");
            }, 3000);
            return true;
        }

		if ( typeof wcuRemoveCustomFlagArr != 'undefined' && wcuRemoveCustomFlagArr != null) {
			jQuery.sendFormWcu({
	            data: {
	                mod: 'flags',
	                action: 'removeCustomlag',
	                data: wcuRemoveCustomFlagArr,
	            },
	            onSuccess: function(res) {
	                if (!res.error) {
	                }
	            }
	        });
		}
		return true;
    });
    // Validate Currencies TAB before Save END

    if (jQuery(".wcuFlagsSelectBox").length>0) {
        jQuery(".wcuFlagsSelectBox").msDropDown();
    }
    jQuery(".wcuTabContentInner .wcuProPreviewLink").click(function(e){
        e.stopPropagation();
    });
});

function wcuLookPreviewAjax(moduleName) {
    var divClass = moduleName;
    switch (moduleName) {
      case 'currency_switcher':
        moduleName = 'drawCurrencySwitcherAjax';
        break;
      case 'currency_converter':
        moduleName = 'drawCurrencyConverterAjax';
        break;
      case 'currency_rates':
        moduleName = 'drawCurrencyRatesAjax';
        break;
      default:
        break;
    }
   
	jQuery('#mainform').sendFormWcu({
		data: jQuery('.form-table.moduleName'+divClass).serializeAnythingWcu()
	,	appendData: {mod: divClass, action: moduleName}
	,	onSuccess: function(res) {
			if(!res.error) {
				jQuery(".wcuTabContentPreviewInner"+divClass+"").html(res.html);
			}
		}
	});
};

