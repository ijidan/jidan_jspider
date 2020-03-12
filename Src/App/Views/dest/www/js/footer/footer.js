var HT = window.HT || {};
HT = {
    Cookie: {
        set: function (key, val, h) {
            if (h) {
                var date = new Date();
                date.setTime(date.getTime() + (h * 60 * 60 * 1000));
                var expires = "; expires=" + date.toGMTString();
            } else {
                var expires = '';
            }
            document.cookie = key + "=" + val + expires + "; domain=.layhangtrungviet.com;path=/";
        }
    },
    getQueryString: function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r !== null) return unescape(r[2]);
        return null;
    }
};

window.onload = function () {
    //写入URL中htag参数到cookie
    HT.Cookie.set('htag', HT.getQueryString('htag'));

    var _hmt = _hmt || [];
    (function () {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?688c316cbbfb0a8c419ea3cf62178d1f";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
};
