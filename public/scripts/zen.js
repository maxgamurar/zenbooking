/* - ZEN mini-lib - */

(function () {

    var _zen = function (params) {
        return new _zenLibrary(params);
    };

    var _zenLibrary = function (params) {

        var selector, i = 0;

        this.version = '0.1';

        if (params === document) {
            selector = document;
            selector.length = 0;
            this[0] = selector;
        } else if (params instanceof HTMLElement) {
            selector = params;
            selector.length = 1;
            this[0] = selector;
        } else {
            selector = document.querySelectorAll(params);
        }

        this.length = selector.length;

        if (!this[0]) {
            for (; i < this.length; i++) {
                this[i] = selector[i];
            }
        }

        return this;
    };

    /* _zen Prototype Functions */

    _zen.fn = _zenLibrary.prototype =
            {
                ready: function (fn) {
                    this[0].addEventListener('DOMContentLoaded', fn, false);
                },
                get: function (selector) {
                    var els;
                    if (typeof selector === "string") {
                        els = document.querySelectorAll(selector);
                    } else if (selector.length) {
                        els = selector;
                    } else {
                        els = [selector];
                    }
                    return els;
                },
                hide: function () {
                    var len = this.length;
                    while (len--) {
                        this[len].style.display = 'none';
                    }
                    return this;
                },
                show: function () {
                    var len = this.length;
                    while (len--) {
                        this[len].style.display = 'inherit';
                    }
                    return this;
                },
                val: function (newval) {
                    var len = this.length;
                    while (len--) {
                        this[len].value = newval;
                    }
                    return this;
                },
                html: function (html_str) {
                    var len = this.length;
                    while (len--) {
                        this[len].innerHTML = html_str;
                    }
                    return this;
                },
                getval: function () {
                    var len = this.length;
                    while (len--) {
                        return this[len].value;
                    }
                    return null;
                },
                toggle: function () {

                    var len = this.length;
                    while (len--) {
                        if (this[len].style.display !== 'none') {
                            this[len].style.display = 'none';
                        } else {
                            this[len].style.display = '';
                        }
                    }
                    return this;
                },
                on: function (evt, fn) {

                    var len = this.length;

                    while (len--) {
                        if (document.addEventListener) {
                            this[len].addEventListener(evt, fn, false);
                        } else if (document.attachEvent) {
                            this[len].attachEvent("on" + evt, fn);
                        } else {
                            this[len]["on" + evt] = fn;
                        }
                    }

                    return this;
                },
                data: function (key) {
                    return this[0].getAttribute("data-" + key);
                }
            };

    if (!window._zen) {
        window._zen = _zen;
    }


    var ajax = {};
    ajax.x = function () {
        if (typeof XMLHttpRequest !== 'undefined') {
            return new XMLHttpRequest();
        }
        var versions = [
            "MSXML2.XmlHttp.6.0",
            "MSXML2.XmlHttp.5.0",
            "MSXML2.XmlHttp.4.0",
            "MSXML2.XmlHttp.3.0",
            "MSXML2.XmlHttp.2.0",
            "Microsoft.XmlHttp"
        ];

        var xhr;
        for (var i = 0; i < versions.length; i++) {
            try {
                xhr = new ActiveXObject(versions[i]);
                break;
            } catch (e) {
            }
        }
        return xhr;
    };

    ajax.send = function (url, callback, method, data, async) {
        if (async === undefined) {
            async = true;
        }
        var x = ajax.x();
        x.open(method, url, async);
        x.onreadystatechange = function () {
            if (x.readyState == 4) {
                callback(x.responseText)
            }
        };
        x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        if (method == 'POST') {
            x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        }
        x.send(data)
    };

    ajax.get = function (url, data, callback, async) {
        var query = [];
        for (var key in data) {
            query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
        ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
    };

    ajax.post = function (url, data, callback, async) {
        var query = [];
        for (var key in data) {
            query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
        ajax.send(url, callback, 'POST', query.join('&'), async)
    };

    if (!window.ajax) {
        window.ajax = ajax;
    }

})();