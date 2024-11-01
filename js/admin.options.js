var wcuAdminFormChanged = [];
var g_wcuChosenOptions = {
	width: '100%',
	disable_search_threshold: 10,
	search_contains: true,
	enable_split_word_search: true,
	placeholder_text_multiple: 'select options',
};
window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(wcuAdminFormChanged.length)
		return 'Some changes were not-saved. Are you sure you want to leave?';
};
jQuery(document).ready(function(){
	wcuInitMainPromoPopup();

	if (jQuery(".wcuDisplayRulesTab").length) {
		jQuery(".wcuDisplayRulesTab .wcuToShowSelectList select").chosen(g_wcuChosenOptions);
	}
	if (jQuery(".wcuGeoIpRulesCurrenciesList").length) {
		jQuery(".wcuGeoIpRulesCurrenciesList").chosen({
			width: '100%',
		});
	}
	if (jQuery(".wcuMultiSelect").length) {
		jQuery(".wcuMultiSelect select[multiple]").chosen({
			width: '100%',
		});
	}
	if(typeof(wcuActiveTab) != 'undefined' && wcuActiveTab != 'main_page' && jQuery('#toplevel_page_woo-currency').hasClass('wp-has-current-submenu')) {
		var subMenus = jQuery('#toplevel_page_woo-currency').find('.wp-submenu li');
		subMenus.removeClass('current').each(function(){
			if(jQuery(this).find('a[href$="&tab='+ wcuActiveTab+ '"]').length) {
				jQuery(this).addClass('current');
			}
		});
	}

	// Timeout - is to count only user changes, because some changes can be done auto when form is loaded
	setTimeout(function() {
		// If some changes was made in those forms and they were not saved - show message for confirnation before page reload
		var formsPreventLeave = [];
		if(formsPreventLeave && formsPreventLeave.length) {
			jQuery('#'+ formsPreventLeave.join(', #')).find('input,select').change(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormWcu(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).find('input[type=text],textarea').keyup(function(){
				var formId = jQuery(this).parents('form:first').attr('id');
				changeAdminFormWcu(formId);
			});
			jQuery('#'+ formsPreventLeave.join(', #')).submit(function(){
				adminFormSavedWcu( jQuery(this).attr('id') );
			});
		}
	}, 1000);

	if(jQuery('.wcuInputsWithDescrForm').length) {
		jQuery('.wcuInputsWithDescrForm').find('input[type=checkbox][data-optkey]').change(function(){
			var optKey = jQuery(this).data('optkey')
			,	descShell = jQuery('#wcuFormOptDetails_'+ optKey);
			if(descShell.length) {
				if(jQuery(this).attr('checked')) {
					descShell.slideDown( 300 );
				} else {
					descShell.slideUp( 300 );
				}
			}
		}).trigger('change');
	}
	wcuInitCustomCheckRadio();
	//wcuInitCustomSelect();

	jQuery('.wcuFieldsetToggled').each(function(){
		var self = this;
		jQuery(self).find('.wcuFieldsetContent').hide();
		jQuery(self).find('.wcuFieldsetToggleBtn').click(function(){
			var icon = jQuery(this).find('i')
			,	show = icon.hasClass('fa-plus');
			show ? icon.removeClass('fa-plus').addClass('fa-minus') : icon.removeClass('fa-minus').addClass('fa-plus');
			jQuery(self).find('.wcuFieldsetContent').slideToggle( 300, function(){
				if(show) {
					jQuery(this).find('textarea').each(function(i, el){
						if(typeof(this.CodeMirrorEditor) !== 'undefined') {
							this.CodeMirrorEditor.refresh();
						}
					});
				}
			} );
			return false;
		});
	});
	// Tooltipster initialization
	/*var tooltipsterSettings = {
		contentAsHTML: true
	,	interactive: true
	,	speed: 0
	,	delay: 0
	//,	animation: 'swing'
	,	maxWidth: 450
	};
	if(jQuery('.woobewoo-tooltip').length) {
		tooltipsterSettings.position = 'top-left';
		jQuery('.woobewoo-tooltip').tooltipster( tooltipsterSettings );
	}
	if(jQuery('.woobewoo-tooltip-bottom').length) {
		tooltipsterSettings.position = 'bottom-left';
		jQuery('.woobewoo-tooltip-bottom').tooltipster( tooltipsterSettings );
	}
	if(jQuery('.woobewoo-tooltip-left').length) {
		tooltipsterSettings.position = 'left';
		jQuery('.woobewoo-tooltip-left').tooltipster( tooltipsterSettings );
	}
	if(jQuery('.woobewoo-tooltip-right').length) {
		tooltipsterSettings.position = 'right';
		jQuery('.woobewoo-tooltip-right').tooltipster( tooltipsterSettings );
	}*/
	wcuInitTooltips();
	if(jQuery('.wcuCopyTextCode').length) {
		setTimeout(function(){	// Give it some time - wait until all other elements will be initialized
			var cloneWidthElement =  jQuery('<span class="sup-shortcode" />').appendTo('.woobewoo-plugin');
			jQuery('.wcuCopyTextCode').attr('readonly', 'readonly').click(function(){
				this.setSelectionRange(0, this.value.length);
			}).focus(function(){
				this.setSelectionRange(0, this.value.length);
			});
			jQuery('input.wcuCopyTextCode').each(function(){
				cloneWidthElement.html( str_replace(jQuery(this).val(), '<', 'P') );
				var parentSelector = jQuery(this).data('parent-selector')
				,	parentWidth = (parentSelector && parentSelector != ''
						? jQuery(this).parents(parentSelector+ ':first')
						: jQuery(this).parent()
					).width()
				,	txtWidth = cloneWidthElement.width();
				if(parentWidth <= 0 || parentWidth > txtWidth) {
					jQuery(this).width( cloneWidthElement.width() );
				}
			});
			cloneWidthElement.remove();
		}, 500);
	}
	// Check for showing review notice after a week usage
    wcuInitPlugNotices();
	jQuery(".woobewoo-plugin .tooltipstered").removeAttr("title");
});
function wcuInitTooltips( selector ) {
	var tooltipsterSettings = {
		contentAsHTML: true
	,	interactive: true
	,	speed: 0
	,	delay: 0
	//,	animation: 'swing'
	,	maxWidth: 450
	}
	,	findPos = {
		'.woobewoo-tooltip': 'top-left'
	,	'.woobewoo-tooltip-bottom': 'bottom-left'
	,	'.woobewoo-tooltip-left': 'left'
	,	'.woobewoo-tooltip-right': 'right'
	}
	,	$findIn = selector ? jQuery( selector ) : false;
	for(var k in findPos) {
		if(typeof(k) === 'string') {
			var $tips = $findIn ? $findIn.find( k ) : jQuery( k ).not('.sup-no-init');
			if($tips && $tips.length) {
				tooltipsterSettings.position = findPos[ k ];
				// Fallback for case if library was not loaded
				if(!$tips.tooltipster) continue;
				$tips.tooltipster( tooltipsterSettings );
			}
		}
	}
}
function changeAdminFormWcu(formId) {
	if(jQuery.inArray(formId, wcuAdminFormChanged) == -1)
		wcuAdminFormChanged.push(formId);
}
function adminFormSavedWcu(formId) {
	if(wcuAdminFormChanged.length) {
		for(var i in wcuAdminFormChanged) {
			if(wcuAdminFormChanged[i] == formId) {
				wcuAdminFormChanged.pop(i);
			}
		}
	}
}
function checkAdminFormSaved() {
	if(wcuAdminFormChanged.length) {
		if(!confirm(toeLangWcu('Some changes were not-saved. Are you sure you want to leave?'))) {
			return false;
		}
		wcuAdminFormChanged = [];	// Clear unsaved forms array - if user wanted to do this
	}
	return true;
}
function isAdminFormChanged(formId) {
	if(wcuAdminFormChanged.length) {
		for(var i in wcuAdminFormChanged) {
			if(wcuAdminFormChanged[i] == formId) {
				return true;
			}
		}
	}
	return false;
}
function wcuInitCustomCheckRadio(selector) {
	if(!jQuery.fn.iCheck) return;
	if(!selector)
		selector = document;
	jQuery(selector).find('input').iCheck('destroy').iCheck({
		checkboxClass: 'icheckbox_minimal'
	,	radioClass: 'iradio_minimal'
	}).on('ifChanged', function(e){
		// for checkboxHiddenVal type, see class htmlWcu
		jQuery(this).trigger('change');
		if(jQuery(this).hasClass('cbox')) {
			var parentRow = jQuery(this).parents('.jqgrow:first');
			if(parentRow && parentRow.length) {
				jQuery(this).parents('td:first').trigger('click');
			} else {
				var checkId = jQuery(this).attr('id');
				if(checkId && checkId != '' && strpos(checkId, 'cb_') === 0) {
					var parentTblId = str_replace(checkId, 'cb_', '');
					if(parentTblId && parentTblId != '' && jQuery('#'+ parentTblId).length) {
						jQuery('#'+ parentTblId).find('input[type=checkbox]').iCheck('update');
					}
				}
			}
		}
	}).on('ifClicked', function(e){
		jQuery(this).trigger('click');
	});
}
function wcuCheckDestroy(checkbox) {
	if(!jQuery.fn.iCheck) return;
	jQuery(checkbox).iCheck('destroy');
}
function wcuCheckDestroyArea(selector) {
	if(!jQuery.fn.iCheck) return;
	jQuery(selector).find('input[type=checkbox]').iCheck('destroy');
}
function wcuCheckUpdate(checkbox) {
	if(!jQuery.fn.iCheck) return;
	jQuery(checkbox).iCheck('update');
}
function wcuCheckUpdateArea(selector) {
	if(!jQuery.fn.iCheck) return;
	jQuery(selector).find('input[type=checkbox]').iCheck('update');
}
function wcuGetTxtEditorVal(id) {
	if(typeof(tinyMCE) !== 'undefined'
		&& tinyMCE.get( id )
		&& !jQuery('#'+ id).is(':visible')
		&& tinyMCE.get( id ).getDoc
		&& typeof(tinyMCE.get( id ).getDoc) == 'function'
		&& tinyMCE.get( id ).getDoc()
	)
		return tinyMCE.get( id ).getContent();
	else
		return jQuery('#'+ id).val();
}
function wcuSetTxtEditorVal(id, content) {
	if(typeof(tinyMCE) !== 'undefined'
		&& tinyMCE
		&& tinyMCE.get( id )
		&& !jQuery('#'+ id).is(':visible')
		&& tinyMCE.get( id ).getDoc
		&& typeof(tinyMCE.get( id ).getDoc) == 'function'
		&& tinyMCE.get( id ).getDoc()
	)
		tinyMCE.get( id ).setContent(content);
	else
		jQuery('#'+ id).val( content );
}
/**
 * Add data to jqGrid object post params search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function wcuGridSetListSearch(param, gridSelectorId) {
	jQuery('#'+ gridSelectorId).setGridParam({
		postData: {
			search: param
		}
	});
}
/**
 * Set data to jqGrid object post params search and trigger search
 * @param {object} param Search params to set
 * @param {string} gridSelectorId ID of grid table html element
 */
function wcuGridDoListSearch(param, gridSelectorId) {
	wcuGridSetListSearch(param, gridSelectorId);
	jQuery('#'+ gridSelectorId).trigger( 'reloadGrid' );
}
/**
 * Get row data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {object} Row data
 */
function wcuGetGridDataById(id, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getRowData', rowId);
	}
	return false;
}
/**
 * Get cell data from jqGrid
 * @param {number} id Item ID (from database for example)
 * @param {string} column Column name
 * @param {string} gridSelectorId ID of grid table html element
 * @return {string} Cell data
 */
function wcuGetGridColDataById(id, column, gridSelectorId) {
	var rowId = getGridRowId(id, gridSelectorId);
	if(rowId) {
		return jQuery('#'+ gridSelectorId).jqGrid ('getCell', rowId, column);
	}
	return false;
}
/**
 * Get grid row ID (ID of table row) from item ID (from database ID for example)
 * @param {number} id Item ID (from database for example)
 * @param {string} gridSelectorId ID of grid table html element
 * @return {number} Table row ID
 */
function getGridRowId(id, gridSelectorId) {
	var rowId = parseInt(jQuery('#'+ gridSelectorId).find('[aria-describedby='+ gridSelectorId+ '_id][title='+ id+ ']').parent('tr:first').index());
	if(!rowId) {
		console.log('CAN NOT FIND ITEM WITH ID  '+ id);
		return false;
	}
	return rowId;
}
function prepareToPlotDate(data) {
	if(typeof(data) === 'string') {
		if(data) {
			data = str_replace(data, '/', '-');
			return (new Date(data)).getTime();
		}
	}
	return data;
}
function wcuInitPlugNotices() {
	var $notices = jQuery('.woobewoo-admin-notice');
	if($notices && $notices.length) {
		$notices.each(function(){
			jQuery(this).find('.notice-dismiss').click(function(){
				var $notice = jQuery(this).parents('.woobewoo-admin-notice');
				if(!$notice.data('stats-sent')) {
					// User closed this message - that is his choise, let's respect this and save it's saved status
					jQuery.sendFormWcu({
						data: {mod: 'promo', action: 'addNoticeAction', code: $notice.data('code'), choice: 'hide'}
					});
				}
			});
			jQuery(this).find('[data-statistic-code]').click(function(){
				var href = jQuery(this).attr('href')
				,	$notice = jQuery(this).parents('.woobewoo-admin-notice');
				jQuery.sendFormWcu({
					data: {mod: 'promo', action: 'addNoticeAction', code: $notice.data('code'), choice: jQuery(this).data('statistic-code')}
				});
				$notice.data('stats-sent', 1).find('.notice-dismiss').trigger('click');
				if(!href || href === '' || href === '#')
					return false;
			});
			var $enbStatsBtn = jQuery(this).find('.wcuEnbStatsAdBtn');
			if($enbStatsBtn && $enbStatsBtn.length) {
				$enbStatsBtn.click(function(){
					jQuery.sendFormWcu({
						data: {mod: 'promo', action: 'enbStatsOpt'}
					});
					return false;
				});
			}
		});
	}
}
/**
 * Main promo popup will show each time user will try to modify PRO option with free version only
 */
function wcuGetMainPromoPopup() {
	if(jQuery('#wcuOptInProWnd').hasClass('ui-dialog-content')) {
		return jQuery('#wcuOptInProWnd');
	}
	return jQuery('#wcuOptInProWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 200
	,	open: function() {
			jQuery('#wcuOptWndTemplateTxt').hide();
			jQuery('#wcuOptWndOptionTxt').show();
		}
	});
}
function wcuInitMainPromoPopup() {
	if(!WCU_DATA.isPro) {
		var $proOptWnd = wcuGetMainPromoPopup();
		jQuery('.wcuProOpt').change(function(e){
			e.stopPropagation();
			var needShow = true
			,	isRadio = jQuery(this).attr('type') == 'radio'
			,	isCheck = jQuery(this).attr('type') == 'checkbox';
			if(isRadio && !jQuery(this).attr('checked')) {
				needShow = false;
			}
			if(!needShow) {
				return;
			}
			if(isRadio) {
				jQuery('input[name="'+ jQuery(this).attr('name')+ '"]:first').parents('label:first').click();
				if(jQuery(this).parents('.iradio_minimal:first').length) {
					var self = this;
					setTimeout(function(){
						jQuery(self).parents('.iradio_minimal:first').removeClass('checked');
					}, 10);
				}
			}
			var parent = null;
			parent = jQuery(this).parents('tr:first');
			if(!parent.length) return;
			var promoLink = parent.find('.wcuProOptMiniLabel a').attr('href');
			if(promoLink && promoLink != '') {
				jQuery('#wcuOptInProWnd a').attr('href', promoLink);
			}
			$proOptWnd.dialog('open');
			return false;
		});
	}
}
jQuery(".woobewoo-tooltip").hover(function(){
		var title = jQuery(this).attr("title");
		jQuery(this).attr("tmp_title", title);
		jQuery(this).attr("title","");
});
jQuery(window).load(function(){
	InitStickyItem();
});
jQuery(window).resize(function(){
	resizePreview();
})
jQuery('.nav-tab').on("click", function(){
	resizePreview();
});

function resizePreview() {
	var width;
	jQuery('.supsystic-sticky').each(function(){
		element = jQuery(this).parents('.wcuTabContentChildActive').find('.supsystic-sticky-wrapper');
		if (element.width() > 100) {
			width = element.width();
		}
	});
	jQuery('.supsystic-sticky').width(width);
}

/*Some items should be always on users screen*/
function InitStickyItem() {
	jQuery(window).scroll(function(){
		var stickiItemsSelectors = [/*'.ui-jqgrid-hdiv', */'.supsystic-sticky']
		,	elementsUsePaddingNext = [/*'.ui-jqgrid-hdiv', */'.supsystic-bar']	// For example - if we stick row - then all other should not offest to top after we will place element as fixed
		,	wpTollbarHeight = 32
		,	wndScrollTop = jQuery(window).scrollTop() + wpTollbarHeight
		,	footer = jQuery('.wcuAdminFooterShell')
		,	footerHeight = footer && footer.length ? footer.height() : 0
		,	docHeight = jQuery(document).height()
		,	wasSticking = false
		,	wasUnSticking = false;
		/*if(jQuery('#wpbody-content .update-nag').length) {	// Not used for now
			wpTollbarHeight += parseInt(jQuery('#wpbody-content .update-nag').outerHeight());
		}*/

		for(var i = 0; i < stickiItemsSelectors.length; i++) {
			jQuery(stickiItemsSelectors[ i ]).each(function(){
				var element = jQuery(this);
				if(element.attr('id') == 'wcuPreviewStickyBar') {
					// #gmpMapRightStickyBar - map preview container, let be here for normal map preview container scrolling
					if(jQuery(window).width() <= 991) {
						element.removeClass('supsystic-sticky-active');
						element.addClass('sticky-ignore');
					} else {
						element.removeClass('sticky-ignore')
					}
				}
				if(element && element.length && !element.hasClass('sticky-ignore')) {
					var scrollMinPos = element.offset().top
					,	prevScrollMinPos = parseInt(element.data('scrollMinPos'))
					,	useNextElementPadding = toeInArray(stickiItemsSelectors[ i ], elementsUsePaddingNext) !== -1 || element.hasClass('sticky-padd-next')
					,	currentScrollTop = wndScrollTop
					,	calcPrevHeight = element.data('prev-height')
					,	currentBorderHeight = wpTollbarHeight
					,	usePrevHeight = 0
					,	nextElement;
					if(calcPrevHeight) {
						usePrevHeight = jQuery(calcPrevHeight).outerHeight();
						currentBorderHeight += usePrevHeight;
					}
					width = element.width();
					element.css({
						'width' : width,
					});
					if(element.is(':visible') && currentScrollTop > scrollMinPos && !element.hasClass('supsystic-sticky-active')) {	// Start sticking
						element.addClass('supsystic-sticky-active').data('scrollMinPos', scrollMinPos).css({
							'top': currentBorderHeight
						});
						if(element.hasClass('sticky-save-width')) {
							element.addClass('sticky-full-width');
						}
						if(useNextElementPadding) {
							//element.addClass('supsystic-sticky-active-bordered');
							nextElement = element.next();
							if(nextElement && nextElement.length) {
								nextElement.data('prevPaddingTop', nextElement.css('padding-top'));
								var addToNextPadding = parseInt(element.data('next-padding-add'));
								addToNextPadding = addToNextPadding ? addToNextPadding : 0;
								nextElement.css({
									'padding-top': element.height() + usePrevHeight  + addToNextPadding
								});
							}
						}
						wasSticking = true;
						element.trigger('startSticky');
					} else if(!isNaN(prevScrollMinPos) && currentScrollTop <= prevScrollMinPos) {	// Stop sticking
						// because of this action some map tabs (shapes and heatmap) are jump up during scroll.
						element.removeClass('supsystic-sticky-active').data('scrollMinPos', 0).css({
							'top': 0
						});
						if(element.hasClass('sticky-save-width')) {
							element.removeClass('sticky-full-width');
						}
						if(useNextElementPadding) {
							//element.removeClass('supsystic-sticky-active-bordered');
							nextElement = element.next();
							if(nextElement && nextElement.length) {
								var nextPrevPaddingTop = parseInt(nextElement.data('prevPaddingTop'));
								if(isNaN(nextPrevPaddingTop))
									nextPrevPaddingTop = 0;
								nextElement.css({
									'padding-top': nextPrevPaddingTop
								});
							}
						}
						element.trigger('stopSticky');
						wasUnSticking = true;
					} else {	// Check new stick position
						if(element.hasClass('supsystic-sticky-active')) {
							if(footerHeight) {
								var elementHeight = element.height()
								,	heightCorrection = 32
								,	topDiff = docHeight - footerHeight - (currentScrollTop + elementHeight + heightCorrection);
								if(topDiff < 0) {
									element.css({
										'top': currentBorderHeight + topDiff
									});
								} else {
									element.css({
										'top': currentBorderHeight
									});
								}
							}
							// If at least on element is still sticking - count it as all is working
							wasSticking = wasUnSticking = false;
						}
					}
				}
			});
		}
		// if(wasSticking) {
		// 	if(jQuery('#wcuPreviewStickyBar').size())
		// 		jQuery('#wcuPreviewStickyBar').show();
		// } else if(wasUnSticking) {
		// 	if(jQuery('#wcuPreviewStickyBar').size())
		// 		jQuery('#wcuPreviewStickyBar').hide();
		// }
	});
}
			function wcuSelectMultipleSortableFunction(originalSelectId) {

			var pseudoSelectClass = originalSelectId+'Pseudo';
			var pseudoSelectDiv = pseudoSelectClass+'Div';

			pseudoSelectClass = pseudoSelectClass.toString();
			pseudoSelectDiv = pseudoSelectDiv.toString();

			var select = jQuery("#"+originalSelectId+"");
			var name = jQuery("#"+originalSelectId+"").attr("name");

			var pseudoSelect = jQuery("<div class='"+pseudoSelectClass+" wcuSelectMultipleSortableWrapper' data-name='"+name+"'></div>");

			jQuery("#"+originalSelectId+"").hide();
			pseudoSelect.insertBefore(select);

			select.find("option").each(function(e){
				var html = jQuery(this).text();
				var val = jQuery(this).val();
				if (jQuery.isNumeric(html)) {
					jQuery(this).remove();
					return true;
				}
				var selected = jQuery(this).prop("selected");
				if (selected) {selected = 'checked';}
				var option = jQuery("<div align='center' class='"+pseudoSelectDiv+" wcuSelectMultipleSortableDiv' data-value='"+val+"' data-text='"+html+"'><i class='fa fa-arrows-h' style='font-size: 20px; margin-top:5px;'></i><br><input class='"+pseudoSelectDiv+"Checkbox' "+ selected +" type='checkbox'>"+html+"</div>")

				pseudoSelect.append(option);
			});

			jQuery( "."+pseudoSelectClass+"" ).sortable({
			  update: function( event, ui ) {
				wcuSelectSortableCreateOptions(pseudoSelectClass, pseudoSelectDiv, select, name, pseudoSelect);
			  }
			});

			jQuery( "."+pseudoSelectClass+"" ).disableSelection();

			jQuery("."+pseudoSelectDiv+"").click(function(e){
				var checkbox = jQuery(this).find("."+pseudoSelectDiv+"Checkbox");
				checkbox.prop("checked", !checkbox.prop("checked"));
				 if (jQuery(this).find(".icheckbox_minimal").hasClass("checked")) {
					 jQuery(this).find(".icheckbox_minimal").removeClass("checked");
				 } else {
					 jQuery(this).find(".icheckbox_minimal").addClass("checked");
				 };

				wcuSelectSortableCreateOptions(pseudoSelectClass, pseudoSelectDiv, select, name, pseudoSelect);
			});

			jQuery("."+pseudoSelectDiv+"Checkbox").click(function(e){
				e.stopPropagation();
				wcuSelectSortableCreateOptions(pseudoSelectClass, pseudoSelectDiv, select, name, pseudoSelect);
			});

		}

		function wcuSelectSortableCreateOptions(pseudoSelectClass, pseudoSelectDiv, select, name, pseudoSelect) {
			select.find("option").remove();
			jQuery('body .'+pseudoSelectClass+'').find("."+pseudoSelectDiv+"").each(function(e){
				var html = jQuery(this).text();
				var val = jQuery(this).attr("data-value");
				var checked = jQuery(this).find('input').is(":checked");
				option = select.append("<option value='"+val+"' >"+html+"</option>");
				if (checked) {
					select.find('option').last().prop("selected", true);
				}
			});
		}

		// Hide random blocks by selected options
		jQuery("[name*='wcu_options[currency_switcher][design_tab][type]']").on("change", function(){
				toggleType = jQuery(this).val();
				toggleSwitcher = jQuery("[name*='wcu_options[currency_switcher][design_tab][toggle_switcher]']");

				toggleSwitcherFullSize = jQuery("[name*='wcu_options[currency_switcher][design_tab][toggle_switcher]'][value='full_size']");
				toggleSwitcherOnClick = jQuery("[name*='wcu_options[currency_switcher][design_tab][toggle_switcher]'][value='on_click']");
				toggleSwitcherOnHover = jQuery("[name*='wcu_options[currency_switcher][design_tab][toggle_switcher]'][value='on_hover']");

				switch (toggleType) {
				  case 'simple':
					 toggleSwitcherFullSize.parent().parent().show();
				    break;
				  case 'floating':
						if ( toggleSwitcherFullSize.parent().hasClass("checked") ) {
						  toggleSwitcher.parent().removeClass("checked");
						  toggleSwitcherOnHover.prop("checked",true);
						  toggleSwitcherOnHover.parent().addClass("checked");
						}
				  		toggleSwitcherFullSize.parent().parent().hide();
				    break;
				  case 'rotating':
					  if ( toggleSwitcherFullSize.parent().hasClass("checked") ) {
						toggleSwitcher.parent().removeClass("checked");
						toggleSwitcherOnHover.prop("checked",true);
						toggleSwitcherOnHover.parent().addClass("checked");
					  }
					  toggleSwitcherFullSize.parent().parent().hide();
				    break;
				}

		});

// Toggle CurrencySwitcher open button options by Toggle Switcher checked
jQuery(document).ready(function(){
	jQuery("[name='wcu_options[currency_switcher][design_tab][toggle_switcher]']").on("click", function(){
		parentDiv = jQuery(this).closest('.wcuTabContentChildOptions');
		toggleSwitcher = jQuery(this).val();
		switch (toggleSwitcher) {
			case 'on_click':
				parentDiv.find(".hideIfFullSizeView").removeClass('wcuHideIfFullSizeViewChecked');
			break;
			case 'on_hover':
				parentDiv.find(".hideIfFullSizeView").removeClass('wcuHideIfFullSizeViewChecked');
			break;
			case 'full_size':
				parentDiv.find(".hideIfFullSizeView").addClass('wcuHideIfFullSizeViewChecked');
			break;
		}
	});
	jQuery("[name='wcu_options[currency_switcher][design_tab][toggle_switcher]']").each(function(){
		if ( jQuery(this).parent().hasClass('checked') ) {
			jQuery(this).click();
		}
	});
});

// Toggle CurrencyRates open button options by Toggle Panel checked
jQuery(document).ready(function(){
	jQuery("[name='wcu_options_pro[currency_rates][design_tab][cr_toggle]']").on("click", function(){
		parentDiv = jQuery(this).closest('.wcuTabContentChildOptions');
		toggleSwitcher = jQuery(this).val();

		switch (toggleSwitcher) {
			case 'on_click':
				parentDiv.find(".hideIfFullSizeView").removeClass('wcuHideIfFullSizeViewChecked');
			break;
			case 'on_hover':
				parentDiv.find(".hideIfFullSizeView").removeClass('wcuHideIfFullSizeViewChecked');
			break;
			case 'full_size':
				parentDiv.find(".hideIfFullSizeView").addClass('wcuHideIfFullSizeViewChecked');
			break;
		}
	});
	jQuery("[name='wcu_options_pro[currency_rates][design_tab][cr_toggle]']").each(function(){
		if ( jQuery(this).parent().hasClass('checked') ) {
			jQuery(this).click();
		}
	});
});

// Toggle CurrencyConverter open button options by Toggle Panel checked
jQuery(document).ready(function(){
	jQuery("[name='wcu_options_pro[currency_converter][design_tab][cc_toggle]']").on("click", function(){
		parentDiv = jQuery(this).closest('.wcuTabContentChildOptions');
		toggleSwitcher = jQuery(this).val();

		switch (toggleSwitcher) {
			case 'on_click':
				parentDiv.find(".hideIfFullSizeView").removeClass('wcuHideIfFullSizeViewChecked');
			break;
			case 'on_hover':
				parentDiv.find(".hideIfFullSizeView").removeClass('wcuHideIfFullSizeViewChecked');
			break;
			case 'full_size':
				parentDiv.find(".hideIfFullSizeView").addClass('wcuHideIfFullSizeViewChecked');
			break;
		}
	});
	jQuery("[name='wcu_options_pro[currency_converter][design_tab][cc_toggle]']").each(function(){
		if ( jQuery(this).parent().hasClass('checked') ) {
			jQuery(this).click();
		}
	});
});

// Fix double label in checkboxHiddenVal
jQuery(document).ready(function(){
	if (jQuery(".icheckbox_minimal").length) {
		jQuery(".icheckbox_minimal").parent().parent().find("label:nth-child(2)").hide();
	}
})
