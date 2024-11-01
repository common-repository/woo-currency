var _Cookie = function() {

        'use strict';

        this.destroy = function(name) {

            // remove the cookie by setting its expiration date in the past
            return this.write(name, '', -1);

        };

        this.read = function(name) {

            var
                // prepare the regular expression used to find the sought cookie in document.cookie
                expression = new RegExp('(^|; )' + encodeURIComponent(name) + '=(.*?)($|;)'),

                // search for the cookie and its value
                matches = document.cookie.match(expression);

            // return the cookie's value
            return matches ? decodeURIComponent(matches[2]) : null;

        };

        this.write = function(name, value, expire, path, domain, secure) {

            var date = new Date();

            // if "expire" is a number, set the expiration date to as many seconds from now as specified by "expire"
            if (expire && typeof expire === 'number') date.setTime(date.getTime() + expire * 1000);

            // if "expire" is not specified or is a bogus value, set it to "null"
            else expire = null;

            // set the cookie
            return (document.cookie =

                // set the name/value pair
                // and also make sure we escape some special characters in the process
                encodeURIComponent(name) + '=' + encodeURIComponent(value) +

                // if specified, set the expiry date
                (expire ? '; expires=' + date.toGMTString() : '') +

                // if specified, set the path on the server in which the cookie will be available on
                '; path=' + (path || '/') +

                // if specified, set the the domain that the cookie is available on
                (domain ? '; domain=' + domain : '') +

                // if required, set the cookie to be transmitted only over a secure HTTPS connection from the client
                (secure ? '; secure' : ''));
        };
    },

    Cookie = new _Cookie();