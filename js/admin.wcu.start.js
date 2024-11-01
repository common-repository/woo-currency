jQuery(document).ready(function(){
    var el = jQuery('a[href $= "page=woo-currency"]');
    if (el.length) {
        var href = el.attr('href');
        href = href.replace('page=woo-currency','page=wc-settings&tab=wcu_currency');
        el.attr('href',href);
    }
    var el = jQuery('a[href *= "page=woo-currency&tab=settings"]');
    if (el.length) {
        var href = el.attr('href');
        href = href.replace('page=woo-currency','page=wc-settings');
        href = href.replace('tab=settings','tab=wcu_currency');
        el.attr('href',href);
    }
});
