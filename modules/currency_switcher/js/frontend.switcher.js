jQuery(document).ready(function () {

	if (WCU_DATA && WCU_DATA.isPreview) {
		var isPreview = true;
	} else {
		var isPreview = false;
	}

    var switcherSimpleClassic = jQuery('#wcuCurrencySwitcherSimpleClassic');
    var switcherSimpleDropdown = jQuery('.wcuCurrencySwitcherSimpleDropdown');
    var switcherRotating = jQuery('#wcuCurrencySwitcherRotating');
    var switcherFloating = jQuery('#wcuCurrencySwitcherFloating');

	jQuery('body').find('.ddChild').css('height', 'auto');

    if (switcherSimpleClassic.length) {
        if (jQuery('#wpadminbar').length && switcherSimpleClassic.hasClass('top')) {
            switcherSimpleClassic.css('top', '32px')
        }
        switcherSimpleClassic.find('li').on('click', function () {
            if (!isPreview && !jQuery(this).hasClass("wcuCurrent") && !jQuery(this).hasClass("wcuCurrencySwitcherSimpleDropdownClose")) {
                wcuUpdateUrlParam('currency', jQuery(this).data('currency'));
            }
        });
        switcherSimpleClassic.show();
    }

    if (switcherSimpleDropdown.length) {
        if (jQuery('#wpadminbar').length && switcherSimpleDropdown.hasClass('top')) {
            switcherSimpleDropdown.css('top', '32px')
        }
        switcherSimpleDropdown.find('li').on('click', function () {
            if (!isPreview && !jQuery(this).hasClass("wcuCurrent") && !jQuery(this).hasClass("wcuCurrencySwitcherSimpleDropdownClose")) {
                wcuUpdateUrlParam('currency', jQuery(this).data('currency'));
            }
        });
        switcherSimpleDropdown.show();
        jQuery(".wcuCurrencySwitcherSimpleDropdown .wcuCurrent").click(function (e) {
            e.preventDefault();
            var wrapper = jQuery(this).closest('div');
            wrapper.addClass("wcuCsdToggleSwitcherClick");
            return false;
        });
        jQuery(".wcuCurrencySwitcherSimpleDropdown .wcuCurrencySwitcherSimpleDropdownClose").click(function (e) {
            e.preventDefault();
            var wrapper = jQuery(this).closest('div');
            wrapper.removeClass("wcuCsdToggleSwitcherClick");
            return false;
        });
    }

    if (switcherRotating.length) {
        if (jQuery('#wpadminbar').length && switcherRotating.hasClass('top')) {
            switcherRotating.css('top', '32px')
        }
        switcherRotating.find('li').on('click', function () {
            if (!isPreview && !jQuery(this).hasClass("wcuCurrent") && !switcherRotating.hasClass("wcuRotToggleSwitcher_on_click")) {
                wcuUpdateUrlParam('currency', jQuery(this).data('currency'));
            }
            if (!isPreview && jQuery(this).hasClass("wcuRotToggleSwitcherClick")) {
                wcuUpdateUrlParam('currency', jQuery(this).data('currency'));
            }
            if (switcherRotating.hasClass("wcuRotToggleSwitcher_on_click")) {
                switcherRotating.find('li').removeClass("wcuRotToggleSwitcherClick");
                jQuery(this).addClass("wcuRotToggleSwitcherClick");
            }
        });
        switcherRotating.show();
        jQuery(document).mouseup(function (e) {
            var container = jQuery(".wcuRotToggleSwitcherClick");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.removeClass("wcuRotToggleSwitcherClick");
            }
        });
    }

    if (switcherFloating.length) {
        if (jQuery('#wpadminbar').length && switcherFloating.hasClass('top')) {
            switcherFloating.css('top', '32px')
        }
        switcherFloating.find('.wcuCurrencySwitcherFloatingTableCurrencies tr').on('click', function () {
            if (!isPreview && !jQuery(this).hasClass("wcuCurrent") && !jQuery(this).hasClass("wcuCurrencySwitcherSimpleDropdownClose")) {
                wcuUpdateUrlParam('currency', jQuery(this).data('currency'));
            }
        });
        switcherFloating.show();

        jQuery("#wcuCurrencySwitcherFloating .wcuCurrencySwitcherButton").click(function (e) {
            e.preventDefault();
            jQuery(".wcuCurrencySwitcher").addClass("wcuFloatToggleSwitcherClick");
            return false;
        });

        jQuery("#wcuCurrencySwitcherFloating .wcuCurrencySwitcherButtonClose").click(function (e) {
            e.preventDefault();
            jQuery(".wcuCurrencySwitcher").removeClass("wcuFloatToggleSwitcherClick");
            return false;
        });

        jQuery(document).mouseup(function (e) {
            var container = jQuery(".wcuCurrencySwitcher");
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.removeClass("wcuFloatToggleSwitcherClick");
            }
        });

    }

	jQuery('.dd').on('click', function(e){
		jQuery(this).find('.ddChild').css('height', 'auto');
	});
	
	jQuery('.wcuCurrencySwitcherPopupClose').on('click', function(e){
	    e.preventDefault();
		jQuery(this).parent().remove();
		
		setCookie('wcuClosePopup', 1, 1);
		return false;
	});
	
	var closed = getCookie("wcuClosePopup");
	if (closed == '') {
	   jQuery('.wcuCurrencySwitcherPopup').show();
    }
});

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
