/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: header.js 6038 2014-09-17 09:46:44Z samgui $
 */
var FX = FX || {}, g_header;

FX = {
    // Cookie操作
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
        },
        get: function (key) {
            var n = key + '=';
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1, c.length);
                }
                if (c.indexOf(n) == 0) {
                    return c.substring(n.length, c.length);
                }
            }
            return null;
        },
        remove: function (key) {
            this.set(key, '', -1);
        }
    },

    // 获取URL参数
    getQueryString: function (name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r !== null) return unescape(r[2]);
        return null;
    },

    // 图片等比例缩放
    imgResize: function (objImg, maxWidth, maxHeight) {
        var img = new Image(), hRatio, wRatio, Ratio = 1, w , h;
        img.onload = function () {
            w = img.width;
            h = img.height;

            wRatio = maxWidth / w;
            hRatio = maxHeight / h;
            if (maxWidth == 0 && maxHeight == 0) {
                Ratio = 1;
            } else if (maxWidth == 0) {//
                if (hRatio < 1) Ratio = hRatio;
            } else if (maxHeight == 0) {
                if (wRatio < 1) Ratio = wRatio;
            } else if (wRatio < 1 || hRatio < 1) {
                Ratio = (wRatio <= hRatio ? wRatio : hRatio);
            }
            if (Ratio < 1) {
                w = w * Ratio;
                h = h * Ratio;
            }
            $(objImg).css({
                height: h + 'px',
                width: w + 'px',
                margin: '-' + (h / 2) + 'px 0 0 -' + (w / 2) + 'px'
            }).fadeIn(100);
        }
        img.src = objImg.src;
    },
    // 编译模版
    compile: (function () {
        var delimiter = ['<%', '%>'];
        return function (template, data) {
            var fn = new Function("data", "var p=[];p.push('" + template.replace(/[\r\t\n]/gm, " ")
                .split("<%")
                .join("\t")
                .replace(/((^|%>)[^\t]*)'/g, "$1\r")
                .replace(/\t=(.*?)%>/g, "',$1,'")
                .split("\t")
                .join("');")
                .split("%>")
                .join("p.push('")
                .split("\r")
                .join("\\'") + "');return p.join('');");

            return data ? fn(data) : '';
        }
    })(),

    // 渲染模版
    render: function (option) {
        var api = option.api || 'http://marketing.com/api/v1/marketing_active.get_active_by_id',
            params = option.params || {},
            containerId = option.containerId,
            templateId = option.templateId,
            beforeFn = option.beforeFn,
            afterFn = option.afterFn,
            autoResize = option.autoResize || '1';

        $.ajax({
            type: "GET",
            url: api,
            data: params,
            dataType: 'jsonp',
            jsonp: 'callback',
            success: function (result) {
                var html = '', data = result.data[0], container = document.getElementById(containerId);

                // 开始回调
                beforeFn && beforeFn(result);

                // 渲染商品列表
                if (result.code == 0) {
                    var template = document.getElementById(templateId).innerHTML,
                        groups = data.goods_items,
                        count = groups.length;

                    if (count > 0) {
                        html += FX.compile(template, groups);
                    } else {
                        html = result.message;
                    }
                } else {
                    html = result.message;
                }

                container.innerHTML = html;

                if (autoResize === '1') {
                    var imgs = container.getElementsByTagName('img');
                    for (var i = 0, len = imgs.length; i < len; i++) {
                        var img = imgs[i], w = $(img).attr('width'), h = $(img).attr('height');
                        FX.imgResize(img, w, h);
                        $(img).removeAttr('width').removeAttr('height');
                    }
                }

                var links = container.getElementsByTagName('a');
                for (var i = 0, len = links.length; i < len; i++) {
                    var link = links[i];
                    $(link).attr('href', $(link).attr('href') + '?htag=' + data.htag);
                }

                // 结束回调
                afterFn && afterFn(result);
            }
        });
    }
}

// 输出页头
g_header =
    '<div class="ht-toolbar">\
        <div class="container">\
            <a href="http://www.layhangtrungviet.com/" class="ht-logo">\
            layhangtrungviet.com 全球购物第一站\
            </a>\
            <span class="ht-slogan">海外直发 官网正品 闪电清关</span>\
        </div>\
    </div>';
document.write(g_header);
