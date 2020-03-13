/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: footer.js 6169 2014-09-19 08:30:18Z samgui $
 */
var g_footer =
    '<div class="ht-footer">\
        <div class="container">\
        Copyright &copy; 2013-2014 深圳市淘海科技有限公司版权所有 <a href="http://www.miitbeian.gov.cn" target="_blank">粤ICP备13027679号</a>\
        </div>\
    </div>';

window.onload = function () {
    // 写入URL中htag、ptag、ptag_time到页面a标签中
    $('.floor a[href^="http://"]').each(function () {
        var me = $(this),
            url = me.attr('href'),
            search = location.search;

        if (search.length != '') {
            if (url.indexOf('?') == -1) {
                url = url + search;
            } else {
                var r1 = /htag=\d{1}.\d{1}.\d{1}&/,
                    r2 = /htag=\d{1}.\d{1}.\d{1}/;

                search = search.substr(1);

                if (r1.test(search)) {
                    search = search.replace(r1, '');
                } else if (r2.test(search)) {
                    search = search.replace(r2, '');
                }

                if (search.length != '') {
                    url = url + '&' + search;
                }
            }
        }

        me.attr('href', url);
    });
}

//baidu
var _hmt = _hmt || [];
(function () {
    var hm = document.createElement("script");
    hm.src = "//hm.baidu.com/hm.js?688c316cbbfb0a8c419ea3cf62178d1f";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();

//cnzz
var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cspan style='display:none' id='cnzz_stat_icon_5459139'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s19.cnzz.com/stat.php%3Fid%3D5459139' type='text/javascript'%3E%3C/script%3E"));
document.write('<script charset="utf-8" src="http://wpa.b.qq.com/cgi/wpa.php?key=XzgwMDA4ODAzOF8xNTkzNjRfODAwMDg4MDM4Xw"></script>')
document.write(g_footer);