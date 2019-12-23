//console hack
if (!window.console) {
    window.console = {
        log: function () {
        },
        info: function () {
        },
        debug: function () {
        },
        error: function () {
        },
        warn: function () {
        }
    };
}

var _LOGIN_CALLBACK_STACK = [];

window.THS = window.THS || {};

THS = {
    guid: function () {
        var _guid = 1;
        return function () {
            return _guid++;
        };
    },
    cookie: {
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
    getLength: function (s) {
        return s.replace(/[^\x00-\xff]/g, "aa").length;
    },
    getQueryString: function (param, url) {
        var r = new RegExp("(\\?|#|&)" + param + "=([^&#]*)(&|#|$)");
        var m = (url || location.href).match(r);
        return (!m ? "" : m[2]);
    },
    imgAutoResize: function (objImg, maxWidth, maxHeight) {
        var me = $(objImg), img = new Image(),
            hRatio, wRatio, ratio = 1, w, h,
            maxWidth = maxWidth || me.width(),
            maxHeight = maxHeight || me.height();

        $(img).load(function () {
            w = img.width;
            h = img.height;

            wRatio = maxWidth / w;
            hRatio = maxHeight / h;

            if (maxWidth == 0 && maxHeight == 0) {
                ratio = 1;
            } else if (maxWidth == 0) {
                if (hRatio < 1) ratio = hRatio;
            } else if (maxHeight == 0) {
                if (wRatio < 1) ratio = wRatio;
            } else if (wRatio < 1 || hRatio < 1) {
                ratio = (wRatio <= hRatio ? wRatio : hRatio);
            }

            if (ratio < 1) {
                w = w * ratio;
                h = h * ratio;
            }

            me.css({
                height: h + 'px',
                width: w + 'px',
                margin: '-' + (h / 2) + 'px 0 0 -' + (w / 2) + 'px'
            }).removeAttr('width').removeAttr('height').fadeIn(100);
        });

        img.src = objImg.src;
    },
    sendPV: function (c, sc) {
        if (!c) {
            return false;
        }
        var img = new Image();
        img.src = 'http://www.layhangtrungviet.com/pv.php?c=' + c + '&sc=' + (sc || '');
        console.log('SEND PV:', c, sc);
    },
    log: function (param) {
        param = $.extend({
            app: null,
            platform: null,
            tag: '',
            level: null,
            line: null,
            file: null,
            content: ''
        }, param);
        if (!param.content || !param.tag) {
            return false;
        }
        var param_str = [];
        for (var i in param) {
            if (param[i] !== null) {
                param_str.push(i + "=" + encodeURIComponent(param[i]));
            }
        }
        var img = new Image();
        img.src = 'http://www.layhangtrungviet.com/log.php?' + param_str.join('&');
        console.log('SEND LOG:', param);
    },
    reloadCaptcha: function () {
        var captcha = $('#captcha');
        captcha.attr('src', captcha.data('src') + '?_=' + (+new Date()));
        $('input[name="captcha"]').val('');
    },
    logout: function () {
        $.cookies.del('user');
        $.get('/user/logout', {}, function (msg) {
            if (typeof msg.ret == 'undefined') {
                location.href = '/';
                return;
            }
            location.reload();
        }, 'json');
    },
    quicklogin: function () {
        return THS.showLogin();
    },
    showLogin: function (callback) {
        callback = callback || function (data) {
            if (data.sc83_dlg) {
                var url = location.href;
                url += '#sc83_dlg=1'
                location.href = url;
                location.reload();
            } else {
                location.reload();
            }
        };
        _LOGIN_CALLBACK_STACK.push(callback);
        $('.ui-overlay').height($(document).height());
        $('#login-dialog,.ui-overlay').show();
        $('#login-form input[name="login_info"]').focus();
        THS.sendPV('login_dialog');
    },
    reflow: function () {
        var st = $(document).scrollTop(), url = location.href, ot;
        setTimeout(function () {
            if ($('.ht-header').length == 1
                && $('.ht-search').length == 1
                && url.indexOf('my') == -1
                && url.indexOf('ucenter') == -1
                && url.indexOf('item') == -1) {
                ot = $('.ht-header-wrapper').offset().top;
                $('.ht-header')[(st > ot ? 'add' : 'remove') + 'Class']('ht-header-fixed');
            }

            var sideNav = $('#sidenav');
            if (sideNav.length == 1) {
                sideNav[(st > 95 ? 'add' : 'remove') + 'Class']('sidenav-fixed');
            }

            var tabNav = $('#product .tab-nav');
            if (tabNav.length == 1) {
                ot = $('#product').offset().top;
                tabNav[(st > ot ? 'add' : 'remove') + 'Class']('tab-nav-fixed');
            }

            var saleGuide = $('#sale-guide');
            if (saleGuide.length == 1) {
                ot = $('.sale-guide-wrapper').offset().top - 75;
                saleGuide[(st > ot ? 'add' : 'remove') + 'Class']('sale-guide-fixed');
            }
        }, 10);
    }
};

$(function () {
    $('.item-catalog,.mod-catalog').hover(function () {
        if ($('.page-index').length == 0) {
            $('.item-catalog').addClass('active');
            $('.mod-catalog').stop(true, true).show();
        }
    }, function () {
        if ($('.page-index').length == 0) {
            $('.item-catalog').removeClass('active');
            $('.mod-catalog').stop(true, true).slideUp(200);
        }
    });

    $('.mod-catalog .cat-item').hover(function () {
        $(this).find('.subcat').show();
    }, function () {
        $(this).find('.subcat').hide();
    });

    //自动登录
    $('a[rel=link-need-login]').click(function () {
        var forward = this.getAttribute('data-login-forward');
        var callback;
        if (forward) {
            callback = function () {
                location.href = forward
            };
        }
        ;
        THS.showLogin(callback);
        return false;
    });

    $('a[rel=reload-captcha]').click(function () {
        THS.reloadCaptcha();
    });

    $('.ui-dialog-close').click(function () {
        $(this).parents('.ui-dialog').hide();
        $('.ui-overlay').hide();
    });

    //优惠券活动83对话框


    //检查浏览器是否缩放
    (function () {
        var doc = document, ua = navigator.userAgent,
            zoom = $(".ht-broswer-zoom"),
            neverShowZoomTip = !!THS.cookie.get("noZoom");

        if (ua.indexOf("MSIE 6.0") > 0) {
            return;
        }

        $(window).on('resize', function () {
            onZoomChange($('#accessory_zoom'));
        });

        window.onZoomChange = function (o) {//flash执行缩放检测逻辑的回调函数
            if (o.scale == 1 || neverShowZoomTip) {
                zoom.hide();
                return;
            }
            $("#zoom-state").html(o.scale > 1 ? '放大' : '缩小');
            zoom.show();
        }

        $('#clear-state').click(function (e) {
            zoom.hide();
            THS.cookie.set("noZoom", 1, 525600);
        });

        $('.ht-broswer-zoom .close').click(function () {
            zoom.hide();
        });

        // Ctrl+0
        doc.onkeydown = function (e) {
            if (e.ctrlKey && (e.keyCode == 48 || e.keyCode == 96)) {
                zoom.hide();
            }
        };
    })();

    (function () {
        //HTAG & PTAG
        var htag = THS.getQueryString('htag');
        if (htag) {
            THS.cookie.set('htag', htag);
        }

        var ptag = THS.getQueryString('ptag');
        if (ptag) {
            THS.cookie.set('ptag', ptag);
            THS.cookie.set('ptag_time', (new Date()).getTime() / 1000);
        }

        //写入时区
        if (!THS.cookie.get('timezone')) {
            var visitortime = new Date();
            var visitortimezone = -visitortime.getTimezoneOffset() / 60;
            THS.cookie.set('timezone', visitortimezone, 12);
        }

        //写入分辨率
        if (!THS.cookie.get('screen_resolution')) {
            THS.cookie.set('screen_resolution', screen.height + 'x' + screen.width, 12);
        }
    })();

    //固定搜索框、返回顶部
    (function () {
        THS.reflow();
        $(window).bind('scroll resize', function () {
            THS.reflow();
        });

        $('.toolbar-item-backtop').click(function () {
            //var selector = $.browser.webkit ? 'body' : 'html';
            $('html,body').animate({scrollTop: 0}, 300);
        });
    })();

    //登录处理
    (function () {
        var LOGIN_FORM = $('#login-form');
        if (!LOGIN_FORM.size()) {
            window.console && console.log("没有找到登录表单");
            return;
        }

        var doLogin = function () {
            var $uname = $('input[name=login_info]'),
                login_info = $.trim($uname.val()),
                $upass = $('input[name=password]'),
                password = $upass.val(),
                $captcha = $('input[name=captcha]'),
                captcha = $.trim($captcha.val()),
                remember = 0,
                autologin = 0;

            if (!login_info) {
                $uname.focus();
                return showLoginErr('请输入' + $uname.attr('placeholder'));
            }

            if (!isNaN(login_info)) {
                if (!(/^1\d{10}$/.test(login_info))) {
                    $uname.focus();
                    return showLoginErr('请输入正确格式的手机号码');
                }
            } else {
                if (!(/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(login_info))) {
                    $uname.focus();
                    return showLoginErr('请输入正确格式的邮箱地址');
                }
            }
            if (!password) {
                $upass.focus();
                return showLoginErr('请输入登录密码');
            }
            $.ajax({
                url: "/index.php?controller=simple&action=ajax_login",
                dataType: 'json',
                type: 'post',
                async: false,
                data: {
                    login_info: login_info,
                    password: password,
                    captcha: captcha,
                    remember: remember,
                    autoLogin: autologin
                },
                success: function (data) {
                    if (data.status == 1) {
                        var ref = document.referrer,
                            len = _LOGIN_CALLBACK_STACK.length;

                        LOGIN_FORM.find('.row-forget .ui-tip').html('');
                        THS.sendPV('login_dialog_login_success');
                        if (len > 0) {
                            for (var i = 0; i < len; i++) {
                                _LOGIN_CALLBACK_STACK[i](data);
                            }
                            _LOGIN_CALLBACK_STACK = [];
                        } else {
                            var pathName=location.pathname;
                            if(pathName=="/site/get_password_success"){
                                location.href="/";
                            }else{
                                if (ref) {
                                    location.href = ref;
                                } else {
                                    location.reload();
                                }
                            }


                        }
                    } else {
                        THS.sendPV('login_dialog_login_fail');
                        if (data.message['captcha']) {
                            $captcha.focus()
                            showLoginErr(data.message['captcha']);
                        } else {
                            $uname.focus()
                            showLoginErr('帐号或密码错误');
                        }
                        THS.reloadCaptcha();
                    }
                }
            });
        };

        var showLoginErr = function (msg) {
            LOGIN_FORM.find('.row-forget .ui-tip').html(msg || '你尚未登录，登录后可购买');
            return false;
        };

        $('.form-control', LOGIN_FORM).change(function () {
            showLoginErr();
        });

        LOGIN_FORM.submit(function () {
            doLogin();
            return false;
        });
    })();

    //点击统计
    (function () {
        $(document.body).mousedown(function (event) {
            var tag = $(event.target).parents("[data-click-sc]")[0];
            if (tag) {
                var click_sc = tag.getAttribute('data-click-sc');
                if (click_sc) {
                    THS.sendPV(click_sc);
                }
            }
        });
        if (window.PAGE_PV_TAG) {
            THS.sendPV(PAGE_PV_TAG);
        }
    })();

    //搜索处理
    (function () {
        var LOADING_TIP_DELAY = 0;
        var LOADING_TIP_TIMEOUT = 3000;
        var URL_SEARCH_TIMEOUT = 600000;
        var SEARCH_FORM = $('#search-form');
        var SUBMIT_BTN = $('input[type=submit]', SEARCH_FORM);
        var SUBMIT_BTN_TEXT = SUBMIT_BTN.val();
        var SEARCH_INPUT = $('input[name="word"]', SEARCH_FORM);
        if (!SEARCH_FORM.size()) {
            window.console && console.log('没有找到搜索框');
            return;
        }

        var TIP_TIMER = null;
        var showSearchTip = function (msg, timeout) {
            clearTimeout(TIP_TIMER);
            $('.tip-search').show();
            $('.tip-content').html(msg);
            if (timeout) {
                TIP_TIMER = setTimeout(function () {
                    hideSearchTip();
                }, timeout);
            }
        };

        var hideSearchTip = function () {
            clearTimeout(TIP_TIMER);
            $('.tip-search').hide();
        };

        var getGoodsUrl = function (sku_id) {
            return 'http://www.layhangtrungviet.com/item-' + sku_id + '.html';
        }

        var isUrl = function (str) {
            if (/\.\w+\.com\//i.test(str) || /^http:/i.test(str) || /^https:/i.test(str)) {
                return true;
            }
            return false;
        };

        var LOADING_TIP_TM = null;
        var showLoading = function () {
            SUBMIT_BTN.val('取 消');
            $('#search-progressbar').show();
            var progress = $('.ui-progressbar-thumb', '#search-progressbar');
            progress.stop();
            progress.animate({
                width: '97%'
            }, 10000);
            LOADING_TIP_TM = setTimeout(function () {
                showSearchTip('正在努力为您加载数据...', LOADING_TIP_TIMEOUT);
            }, LOADING_TIP_DELAY);
        };

        var hideLoading = function () {
            clearTimeout(LOADING_TIP_TM);
            SUBMIT_BTN.val(SUBMIT_BTN_TEXT);
            SEARCH_INSTANCE && SEARCH_INSTANCE.abort();
            var progress = $('.ui-progressbar-thumb', '#search-progressbar');
            progress.stop();
            progress.width(0);
            $('#search-progressbar').hide();
        }

        var checkUrl = function (url) {
            url = $.trim(url);
            if (!url) {
                return false;
            }
            url = url.replace(/[\/]+/g, '/');
            var check_list = [
                /amazon\.com\/[^/]+\/dp\/[^/]+/i, /amazon\.com\/dp\/[^/]+/i, /amazon\.com\/gp\/product\/[^/]+/i, /6pm\.com\/[^/]+/i, /vitacost\.com\/[^/]+/i
                //, /levis\.com\/[^/]+/i
            ];
            for (var i = 0; i < check_list.length; i++) {
                if ((check_list[i].test(url))) {
                    return true;
                }
            }
            return false;
        };

        SEARCH_INPUT.keydown(function () {
            hideSearchTip();
        });

        SUBMIT_BTN.click(function () {
            if (this.value == '取 消') {
                THS.sendPV('search_cancel_click');
                hideSearchTip();
                hideLoading();
                return false;
            } else {
                THS.sendPV('search_submit_click');
            }
        });

        THS.doSearch = function () {
            var kw = SEARCH_INPUT.val();

            if (!kw) {
                THS.sendPV('search_empty_kw');
                showSearchTip('请输入查询关键字', 3000);
                SEARCH_INPUT.focus();
                return false;
            }

            if (isUrl(kw)) {
                kw = kw.replace(/\/\//g, '/').replace(/:\//g, '://'); // fix url
                THS.sendPV('search_by_url');
                if (!checkUrl(kw)) {
                    THS.sendPV('search_url_error');
                    showSearchTip('目前已支持美国亚马逊、6PM、Vitacoat等商家，请选择这些商家链接试一试吧。');
                    SEARCH_INPUT.focus();
                    return false;
                }

                var act = SEARCH_FORM.attr('data-url-action');

                showLoading();
                SEARCH_INSTANCE = $.ajax({
                    url: act,
                    timeout: URL_SEARCH_TIMEOUT,
                    type: 'get',
                    dataType: 'jsonp',
                    jsonp: 'callback',
                    jsonpCallback: 'callback',
                    data: {
                        url: kw
                    },
                    success: function (data) {
                        data = data || {};
                        //容错
                        var sku_id = parseInt(data.sku_id, 10);
                        var message = (data.sku_id && data.message) ? data.message : data.message;
                        var status = (data.sku_id && data.status) ? data.status : data.status;
                        if (status == 1 && sku_id) {
                            THS.sendPV('search_url_result_success');
                            location.href = getGoodsUrl(sku_id);
                            return;
                        } else if (status == 0) {
                            SEARCH_INPUT.focus();
                        }
                        else {
                            THS.sendPV('search_url_result_error');
                            THS.log({
                                tag: 'search_url_result_error',
                                content: '[KW]' + kw + '||' + (window.JSON ? JSON.stringify(data) : message),
                                level: 'd'
                            });
                        }
                        hideLoading();
                        showSearchTip(message, 5000);
                    },
                    complete: function (XMLHttpRequest, status) {
                        //异常
                        if (status != 'success') {
                            hideLoading();
                            showSearchTip('商品加载失败，请检查商品链接是否有效', 2000);
                            THS.sendPV('search_url_result_error');
                            THS.log({
                                tag: 'search_url_result_error',
                                content: '[KW]' + kw + '||' + 'browser error:' + status,
                                level: 'd'
                            });
                            SEARCH_INPUT.focus();
                            SEARCH_INSTANCE.abort();
                        }
                    }
                });
                return false;
            } else {
                THS.sendPV('search_by_keyword');
                return true;
            }
        };

        SEARCH_FORM.submit(function () {
            return THS.doSearch();
        });
    })();
});