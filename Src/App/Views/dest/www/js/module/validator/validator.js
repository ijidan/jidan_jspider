/**
 * @copyright 2014,Taohai Inc. All rights reserved.
 * @update $Id: validator.js 4750 2014-08-11 09:01:08Z samgui $
 */

(function ($) {
    $.fn.validator = function (options) {

        $.fn.validator.defaults = {
            rowCls: '.ui-form-row',     // 表单行
            errorCls: 'row-error',      // 错误
            successCls: 'row-success',  // 成功
            fieldCls: '.ui-form-field', // 表单字段
            controlCls: '.form-control',// 表单控件
            event: 'keyup',             // 出发事件
            afterFn: null               // 验证通过后回调
        };

        var options = $.extend({}, $.fn.validator.defaults, options), valid, rules;

        rules = {
            isEmail: function (str) {
                return /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/.test(str);
            },
            isMobile: function (str) {
                return /^1\d{10}$/.test(str);
            }
        }

        function success(el) {
            $(el).parents(options.rowCls)
                .removeClass(options.errorCls).addClass(options.successCls)
                .find('.ui-tip').html('');

            return valid = true;
        }

        function error(el, message) {
            $(el).parents(options.rowCls)
                .removeClass(options.successCls).addClass(options.errorCls)
                .find('.ui-tip').html(message);

            return  valid = false;
        }

        function validate(el) {
            var $el = $(el),
                val = $.trim(el.value),
                message = $el.attr('placeholder')
                    || $el.parents(options.rowCls).find(options.fieldCls).html(),
                len = THS.getLength(val);

            if (val == '') {
                return error(el, '请填写' + message.replace('输入', ''));
            } else {
                if (el.id == 'account') {
                    var tip = '请输入正确的邮箱或手机号码，可用于登录和找回密码';
                    if (!isNaN(val)) {
                        if (!rules.isMobile(val)) {
                            return error(el, tip);
                        }
                    } else {
                        if (!rules.isEmail(val)) {
                            return error(el, tip);
                        }
                    }
                }

                if (el.id == 'password') {
                    if (!/^[\u4E00-\u9FA5\w_\-]+$/.test(val) || len < 6 || len > 16) {
                        return error(el, '6~16个字符，建议使用字母加数字或符号组合');
                    }
                }

                if (el.id == 'txt-captcha') {
                    if (len != 5) {
                        return error(el, '验证码必须为5个字符');
                    }
                }

                if (el.id == 'smscode') {
                    if (!/^\d{4}$/.test(val)) {
                        return error(el, '手机验证码必须为4个数字');
                    }
                }

                return success(el);
            }
        }

        this.each(function () {
            var form = this, els = form.elements;

            if (options.event) {
                $(options.controlCls).live(options.event, function () {
                    return validate(this);
                });
            }

            form.onsubmit = function () {
                for (var i = 0, len = els.length; i < len; i++) {
                    var el = els[i];
                    if (el.offsetWidth != 0 &&
                        el.type != 'button' &&
                        el.type != 'submit' && !validate(el)) {
                        valid = false;
                        el.focus();
                        return false;
                        break;
                    }
                }

                if (valid) {
                    if (options.afterFn) {
                        options.afterFn();
                        return false;
                    }
                }

                return valid;
            }
        });
    }
})(jQuery);