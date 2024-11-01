var g_wcuCurrTour = null
,	g_wcuTourOpenedWithTab = false
,	g_wcuAdminTourDissmissed = false;
jQuery(document).ready(function(){
	setTimeout(function(){
		if(typeof(wcuAdminTourData) !== 'undefined' && wcuAdminTourData.tour) {
			jQuery('body').append( wcuAdminTourData.html );
			wcuAdminTourData._$ = jQuery('#woobewoo-admin-tour');
			for(var tourId in wcuAdminTourData.tour) {
				if(wcuAdminTourData.tour[ tourId ].points) {
					for(var pointId in wcuAdminTourData.tour[ tourId ].points) {
						_wcuOpenPointer(tourId, pointId);
						break;	// Open only first one
					}
				}
			}
			for(var tourId in wcuAdminTourData.tour) {
				if(wcuAdminTourData.tour[ tourId ].points) {
					for(var pointId in wcuAdminTourData.tour[ tourId ].points) {
						if(wcuAdminTourData.tour[ tourId ].points[ pointId ].sub_tab) {
							var subTab = wcuAdminTourData.tour[ tourId ].points[ pointId ].sub_tab;
							jQuery('a[href="'+ subTab+ '"]')
								.data('tourId', tourId)
								.data('pointId', pointId);
							var tabChangeEvt = str_replace(subTab, '#', '')+ '_tabSwitch';
							jQuery(document).bind(tabChangeEvt, function(event, selector) {
								if(!g_wcuTourOpenedWithTab && !g_wcuAdminTourDissmissed) {
									var $clickTab = jQuery('a[href="'+ selector+ '"]');
									_wcuOpenPointer($clickTab.data('tourId'), $clickTab.data('pointId'));
								}
							});
						}
					}
				}
			}
		}
	}, 500);
});

function _wcuOpenPointerAndPluginTab(tourId, pointId, tab) {
	g_wcuTourOpenedWithTab = true;
	_wcuOpenPointer(tourId, pointId);
	g_wcuTourOpenedWithTab = false;
}
function _wcuOpenPointer(tourId, pointId) {
	var pointer = wcuAdminTourData.tour[ tourId ].points[ pointId ];
	var $content = wcuAdminTourData._$.find('#woobewoo-'+ tourId+ '-'+ pointId);
	if(!jQuery(pointer.target) || !jQuery(pointer.target).length)
		return;
	if(g_wcuCurrTour) {
		_wcuTourSendNext(g_wcuCurrTour._tourId, g_wcuCurrTour._pointId);
		g_wcuCurrTour.element.pointer('close');
		g_wcuCurrTour = null;
	}
	var options = jQuery.extend( pointer.options, {
		content: $content.find('.woobewoo-tour-content').html()
	,	pointerClass: 'wp-pointer woobewoo-pointer'
	,	close: function() {
			//console.log('closed');
		}
	,	buttons: function(event, t) {
			g_wcuCurrTour = t;
			g_wcuCurrTour._tourId = tourId;
			g_wcuCurrTour._pointId = pointId;
			var $btnsShell = $content.find('.woobewoo-tour-btns')
			,	$closeBtn = $btnsShell.find('.close')
			,	$finishBtn = $btnsShell.find('.woobewoo-tour-finish-btn');

			if($finishBtn && $finishBtn.length) {
				$finishBtn.click(function(e){
					e.preventDefault();
					jQuery.sendFormWcu({
						msgElID: 'noMessages'
					,	data: {mod: 'promo', action: 'addTourFinish', tourId: tourId, pointId: pointId}
					});
					g_wcuCurrTour.element.pointer('close');
				});
			}
			if($closeBtn && $closeBtn.length) {
				$closeBtn.bind( 'click.pointer', function(e) {
					e.preventDefault();
					jQuery.sendFormWcu({
						msgElID: 'noMessages'
					,	data: {mod: 'promo', action: 'closeTour', tourId: tourId, pointId: pointId}
					});
					t.element.pointer('close');
					g_wcuAdminTourDissmissed = true;
				});
			}
			return $btnsShell;
		}
	});
	jQuery(pointer.target).pointer( options ).pointer('open');
	var minTop = 10
	,	pointerTop = parseInt(g_wcuCurrTour.pointer.css('top'));
	if(!isNaN(pointerTop) && pointerTop < minTop) {
		g_wcuCurrTour.pointer.css('top', minTop+ 'px');
	}
}
function _wcuTourSendNext(tourId, pointId) {
	jQuery.sendFormWcu({
		msgElID: 'noMessages'
	,	data: {mod: 'promo', action: 'addTourStep', tourId: tourId, pointId: pointId}
	});
}