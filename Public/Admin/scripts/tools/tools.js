$(function() {
    var $fullText = $('.admin-fullText');
    $('#admin-fullscreen').on('click', function() {
        $.AMUI.fullscreen.toggle();
    });

    $(document).on($.AMUI.fullscreen.raw.fullscreenchange, function() {
        $fullText.text($.AMUI.fullscreen.isFullscreen ? '退出全屏' : '开启全屏');
    });
});

function getCookie(key) {
    var cookie = document.cookie,
        cookies, result;
    if (cookie.length > 0) {
        cookies = cookie.split(/\s*;\s*/);
        cookies.forEach(function(cookie) {
            var arr = cookie.split('=');
            if (arr.length > 1) {
                if (arr[0] === key) {
                    result = arr[1];
                }
            }
        });
        return result;
    }
}

function setCookie(c_name, value, expiredays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + expiredays);
    document.cookie = c_name + "=" + escape(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString());　　
}
var easeInOut = function(t, b, c, d) {
    if ((t /= d / 2) < 1) return c / 2 * t * t * t + b;
    return c / 2 * ((t -= 2) * t * t + 2) + b;
};
var getElementTop = function(element) {
    var actualTop = element.offsetTop,
        current = element.offsetParent;

    while (current !== null) {
        actualTop += current.offsetTop;
        current = current.offsetParent;
    }

    return Number.parseInt(actualTop);
};
var getScrollTop = function(elem) {
    var elementScrollTop = null;
    if (elem) {
        elementScrollTop = elem.scrollTop;
    } else {
        if (document.compatMode == "BackCompat") {　　　　　　
            elementScrollTop = document.body.scrollTop;　　　　
        } else {　　　　　　
            elementScrollTop = document.documentElement.scrollTop;　　　　
        }
    }

    return Number.parseInt(elementScrollTop);
};
var setScrollTop = function(elem, val) {
    if (val == null) {
        val = elem;
        elem = null;
    }
    if (elem) {
        elem.scrollTop = val;
    } else {
        if (document.compatMode == "BackCompat") {
            document.body.scrollTop = val;
        } else {
            document.documentElement.scrollTop = val;
        }
    }
};

window.scrollToForm = function() {
    var start = 0,
        duration = 50,
        toSet = null,
        top = null,
        elem = document.getElementById('xAdmin-tab'),
        wrap = document.querySelector('.admin-content'),
        top = getScrollTop(wrap);
    var _run = function() {
        start += 1;
        toSet = easeInOut(start, top, Math.min(getElementTop(elem) - top - 100, wrap.scrollHeight - wrap.clientHeight), duration);
        setScrollTop(wrap, toSet);
        if (start < duration) requestAnimationFrame(_run);
    };
    _run();
};
