//---------------------------------------dima-----------------------------------------------------------
(function($) {
    $.fn.chosenImage = function(options) {
        return this.each(function() {
            var $select = $(this);
            var imgMap  = {};

            $select.find('option').filter(function(){
                return $(this).text();
            }).each(function(i) {
                imgMap[i] = $(this).attr('data-img-src');
            });

            function cssObj(imgSrc) {
                if(wcuCssPath) {
                    var bgImg = (imgSrc) ? 'url(' + wcuCssPath + imgSrc + ')' : 'none';
                    return { 'background-image' : bgImg };
                }
            }
        });
    };
})(jQuery);



var wcuShowFlags =  jQuery('#wcuShowFlags');
// var selectorOptions = $('.wcuAllCurrenciesWithFlags option');
// var selectorSelect = $('.wcuAllCurrenciesWithFlags select');

if(Cookie.read('wcuFlagNeed')) {
    wcuShowFlags.prop('checked', true);
}

jQuery('.wcuAllCurrenciesWithFlags option').each(function () {
    var el = $(this);
    var optionText = el.text();

    var split = optionText.split(',');
    var dataFlag = split[1];

    if(dataFlag) {
        if(wcuShowFlags.is(':checked')) {
            el.attr('data-img-src', dataFlag);
        }
        else { el.attr('data-repeat', dataFlag); }
        el.text(split[0]);
    }
});

wcuShowFlags.on('change', function () {
    var checkbox = $(this);

    if(checkbox.is(':checked')){
        var period = 3600 * 24 * 7; // 1 week

        $('.wcuAllCurrenciesWithFlags option').each(function () {
            var el = $(this);
            var imgSrc = el.attr('data-repeat');
            el.attr('data-img-src', imgSrc);
            el.removeAttr('data-repeat');
            Cookie.write('wcuFlagNeed', true, period);
        });

        $('.wcuAllCurrenciesWithFlags select').chosen('destroy');
        $('.wcuAllCurrenciesWithFlags select').chosenImage();
    }
    else {
        $('.wcuAllCurrenciesWithFlags option').each(function () {
            var el = $(this);
            var imgSrc = el.attr('data-img-src');
            el.attr('data-repeat', imgSrc);
            el.removeAttr('data-img-src');
        });

        $('.wcuAllCurrenciesWithFlags select').chosen('destroy');
        $('.wcuAllCurrenciesWithFlags select').chosen();
        Cookie.destroy('wcuFlagNeed');
    }

    $.sendFormWcu({
        data: {
            mod: 'currency',
            action: 'changeSettingFlags',
            setting: checkbox.prop('checked')
        }
    });
    return false;
});
