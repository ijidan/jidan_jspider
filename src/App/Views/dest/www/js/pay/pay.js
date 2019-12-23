/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: pay.js 2284 2014-06-13 07:52:26Z samgui $
 */
var TH = TH || {}, bank = g_pay_info || ['', '', '', ''];
TH.Pay = {
    init: function () {
        var input = $('input[name="payment_type"]'),
            pay_way, index = 0, way = bank[1], pay_type;

        //银行卡选择
        $('.banks-list li').click(function () {
            var me = $(this),
                payway = me.parents('.payway'),
                index = $('.payment .payway').index(payway),
                selected_bank = $('.selected-bank'),
                type = me.parent('ul'),
                is_credit_card = type.hasClass('credit-card'),
                input = me.find('input'),
                code = input.val(),
                logo = me.find('img'),
                src = logo.attr('src'),
                alt = logo.attr('alt'),
                pay_type;

            me.find('input').attr('checked', true);
            selected_bank.find("img").attr({'src': src, 'alt': alt});
            selected_bank.find('.bank').text(is_credit_card ? '信用卡' : '储蓄卡');
            if (me.parents('.payment-popup').length == 0) {
                payway.find('.selected-bank').show().find('.number')[payway.find('.paywrap').length == 1 ? 'show' : 'hide']();
                payway.find('.more').show();
                payway.find('.bank-area').hide();
            } else { //快捷支付弹层
                $('.paywrap .selected-bank .number').html('待开通');
                $('.paywrap .paytitle').html('开通快捷支付服务');
                index = 0;
            }
            pay_type = index == 0 ? (is_credit_card ? 3 : 2) : (is_credit_card ? 8 : 1);

            $('#bank_code').val(code);
            $('#pay_type').val(pay_type);
            $('#payment').val(10); //连连支付
            $('#no_agree').val('');
        });

        $('.bind-banks li').click(function () {
            var me = $(this),
                img = me.find('img'),
                $bank = me.find('.bank'),
                number = me.find('.number'),
                input = me.find('input'),
                way = me.parents('.payway'),
                wrap = me.parents('.paywrap'),
                selected_bank = wrap.find('.selected-bank');
            input.attr('checked', true);
            way.find('.bind-banks,.bank-area,.banks-list').hide();
            selected_bank.show().find('img').attr({'src': img.attr('src'), 'alt': img.attr('alt')});
            selected_bank.find('.bank').html($bank.html());
            selected_bank.find('.number').html(number.html());

            wrap.addClass('hide-banks').find('.lnk-add-bank').hide();
            wrap.find('.more').show().find('span').html('选择其他银行卡');

            $('#bank_code').val(me.data('code'));
            $('#pay_type').val(me.data('type'));
            $('#payment').val(me.data('payment'));
            $('#card_no').val(me.data('cardno'));
            $('#no_agree').val(me.data('agree'));
        });

        $('.paywrap .more').click(function () {
            var me = $(this), wrap = me.parents('.paywrap');
            wrap.toggleClass('hide-banks');
            if (wrap.hasClass('hide-banks')) {
                wrap.removeClass('hide-banks');
                me.find('span').html('选择其他银行卡');
                wrap.find('.bind-banks').show();
                wrap.find('.selected-bank').hide();
            } else {
                if (wrap.find('.selected-bank').is(':visible')) {
                    wrap.find('.bind-banks').show();
                    wrap.find('.selected-bank').hide();
                }
                me.find('span').html('选择已开通银行卡');
                wrap.find('.lnk-add-bank').show();
            }
            me.hide();
        });

        $('.paywrap .lnk-add-bank').click(function () {
            var height = $(document).height();
            $('.payment-popup,.payment-popup .bank-area,.ui-overlay').show();
            $('.ui-overlay').css('height', height);
        });

        $('.payment-popup-footer .btn').click(function () {
            $('.paywrap .bind-banks').hide();
            $('.paywrap .selected-bank,.paywrap .more').show();
        });

        $('.payment-popup-close,.payment-popup-footer .btn').click(function () {
            $('.payment-popup,.ui-overlay').hide();
        });

        $('.selected-bank .more').click(function () {
            var me = $(this), payway = me.parents('.payway');
            if (me.parents('.paywrap').length == 0) {
                payway.find('.selected-bank').hide();
                payway.find('.bank-area').show();
            }
        });

        $('.paymenttype').click(function () {
            var me = $(this),
                $select = me.nextAll('.selected-bank'),
                $wrap = me.next('.paywrap'),
                $title = me.next('.paytitle'),
                $area = me.nextAll('.bank-area,.paytitle,.banks-list'),
                $input = me.find('input'),
                type = $input.val();

            $input.attr('checked', true);
            $('.selected-bank,.paywrap,.paytitle,.bank-area').hide();

            if (type == 10) {
                $select.hide();
                $area.show();
            } else {
                $select.hide();
                $area.hide();
                $('#bank_code').val('');
                $('#pay_type').val('');
                $('#no_agree').val('');
            }

            if ($wrap.length > 0) {
                $area.hide();
                $wrap.show();
                $wrap.find('.paytitle').show();
            } else {
                $area.show();
                $title.show();
            }

            $wrap.find('.bind-banks input:first').click();
            TH.Pay.setDefaultBank($wrap, 0);
            $('#payment').val(type);
        });

        if (way == 1 || way == 2) {//储蓄卡
            index = 0;
        } else if (way == 3 || way == 8) {//信用卡
            index = 1;
        }

        way = 0;
        input.eq(way).parents('.paymenttype').click();
        pay_way = input.eq(way).parents('.payway');
        pay_type = pay_way.find('.card-type li').eq(index);
        pay_type.click();

        TH.Pay.setDefaultBank(pay_way, index);

        //优惠券
        if ($('input.priceTotal').val() >= 500) {
            $('input[name="payment_type",value="6"]').click();
        }

        $('.coupon-list li').live('click', function (e) {
            var me = $(this), input = me.find('input'), type = me.data('type'), elName = e.target.nodeName;

            //通用和商家优惠券不能同时用
            $('.coupon-list li[data-type="' + (type == 1 ? 2 : 1) + '"] input').attr('checked', false);

            if (elName != 'INPUT') {
                //单商家只能用一次
                input.attr('checked', (input.attr('type') == 'checkbox' ? !input.is(':checked') : true));
            } else {
                input.attr('checked', input.is(':checked'));
            }

            TH.Pay.setCoupon();
            $('.coupon-list li').removeClass('selected').find('input:checked').parent('li').addClass('selected');
        });

        $('#use-coupon').click(function () {
            return TH.Pay.useCoupon();
        });

        //关闭弹窗口
        $('.ordertips .HT-poptip-close').click(function () {
            $('.ordertips').hide();
        });

        $('#orderForm').submit(function () {
            return TH.Pay.orderSubmit();
        });

        $('#lnk-repay').live('click', function () {
            var pay_order_no = $('#pay_order_no').val();
            $.getJSON('/block/isHadPayed?pay_order_no=' + pay_order_no, function (result) {
                if (!result.payed) {
                    location.href = "/ucenter/myorderpay/pay_order_no/" + pay_order_no;
                } else {
                    $('#order-pay-dialog .ft').show();
                }
            });
        });
    },
    setDefaultBank: function (wrap, index) {
        var selector = wrap.find('.bind-banks li');

        if (selector.length == 0) {
            selector = wrap.find('.banks-list ul').eq(index);
        }

        selector.find('input[value=' + bank[2] + ']').attr('checked', true).parents('li').click();
    },
    showTip: function (msg, top, time, callback) {
        var time = time || 3000;
        $('.ordertips .HT-poptip-content').html('<div style="text-align: left;"><p>' + msg + '</p></div>');
        $('.ordertips').show();
        setTimeout(function () {
            if (top) {
                $(window).scrollTop(top)
            }
            $('.ordertips').hide();
            callback && callback();
        }, time);
    },
    useCoupon: function () {
        var $code = $("#coupon-code"),
            $tip = $code.nextAll('.code-tip'),
            code = $.trim($code.val()),
            exist = $('.coupon-list li[data-code="' + code + '"]').length > 0;

        if (code == '') {
            $tip.html('请输入兑换码');
            $code.focus();
            return false;
        }

        if (exist) {
            $tip.html('您已添加过该兑换码');
            $code.focus();
            return false;
        }
        $.ajax({
            url: '/discountuse/ajax_coupon_can_use',
            type: 'POST',
            dataType: 'json',
            data: {card_code: code, order_info: $('#order_info').val(), cartitem: $('#cartitem').val()}
        }).done(function (data) {
            var result, group, name,
                tpl = $('#tpl-coupon').html(), html = '',
                type, filter, list, ctrl_type = 'checkbox';

            if (data.succ) {
                result = data.result;
                group = result.group_info;
                $code.val('');
                $tip.html('');
                type = result.type;
                filter = type == 1 ? '' : '[data-imid="' + group.imid + '"]';
                list = $('.coupon-list li[data-type="' + result.type + '"]' + filter);

                if (list.length > 0) {
                    ctrl_type = 'radio';

                    list.each(function () {
                        var me = $(this), input = me.find('input[type="checkbox"]');

                        if (input.length > 0) {
                            input = input.remove();
                            me.prepend('<input name="' + input.attr('name') + '" type="' + ctrl_type + '" value="' + input.val() + '">');
                        }
                    });
                }
                name = g_coupon_info[result.type - 1];

                html = tpl.replace(/{id}/gi, result.id)
                    .replace(/{type}/gi, result.type)
                    .replace(/{ctl_type}/gi, ctrl_type)
                    .replace(/{ctl_name}/gi, result.type == 1 ? 'comm' : 'imid')
                    .replace(/{group_name}/gi, name)
                    .replace(/{imid}/gi, group.imid)
                    .replace(/{name}/gi, group.name)
                    .replace(/{price}/gi, group.value)
                    .replace(/{code}/gi, result.code)
                    .replace(/{limit}/gi, group.limit_money_type)
                    .replace(/{expired}/gi, result.expired);

                $('.coupon-empty').hide();
                $('.coupon-info,.coupon-tip').show();
                if (result.type == '1') {
                    $('.coupon-list li').removeClass('selected').find('input').attr('checked', false);
                }
                $('.coupon-list').append(html).find('li[data-code="' + result.code + '"]').addClass('selected').click();

                TH.Pay.setCoupon();
            }
            else {
                $tip.html(data.msg);
                $code.focus();
                return false;
            }
        });
    },
    setCoupon: function () {
        var coupon = [], code = [], price = 0,
            orderAmount = parseFloat($('#orderAmount').val()),
            goodsAmount = parseFloat($('#goodsAmount').val()),
            freightAmount = parseFloat($('#freightAmount').val()),
            checked = $('.coupon-list li input:checked'),
            selected = $('.coupon-selected'),
            len = $('.coupon-list li').length,
            count = checked.length, limitType;

        checked.each(function () {
            var me = $(this), item = me.parent('li'), _price = parseFloat(me.val());
            limitType = item.data('limit');

            if (item.data('code')) {
                code.push(item.data('code'));
            } else {
                coupon.push(item.data('id'));
            }

            if (limitType == 1) { // 订单总额
                price += _price > orderAmount ? orderAmount : _price;
            } else if (limitType == 2) { // 商品总额
                price += _price > goodsAmount ? goodsAmount : _price;
            } else if (limitType == 3) { // 运费总额
                price += _price > freightAmount ? freightAmount : _price;
            }
        });

        orderAmount -= parseFloat(price);
        orderAmount = orderAmount < 0 ? 0 : orderAmount;
        $('.youhui').html(price > 0 ? '-&yen;' + price : '&yen;' + 0);
        $('#OrderPrice').val(orderAmount);
        $('#totalOrderPrice').html('&yen;' + orderAmount);
        $('.coupon-count').html(len);

        selected[count > 0 ? 'show' : 'hide']();
        selected.find('.count').html(count);
        selected.find('.save-price').html(price);

        $('#coupon_id').val(coupon.join(','));
        $('#card_code').val(code.join(','));
    },
    orderSubmit: function () {
        var payment = $('#payment').val(),
            bank_code = $('#bank_code').val(),
            pay_type = $('#pay_type').val(),
            order_no = $('#orderForm input[name="address_no"]').val();

        // if (payment == '') {
        //     TH.Pay.showTip('请选择支付方式', 380);
        //     return false;
        // }
        //
        // if (payment == 10) {
        //     if (bank_code == '' || pay_type == '') {
        //         TH.Pay.showTip('请选择付款银行', 380);
        //         return false;
        //     }
        // }

        if (
            $('#check_hide_accept').val() == '' ||
            ($('#check_hide_tele').val() == '' && $('#check_hide_mobile').val() == '' ) ||
            ($('#check_hide_tele').val() != '' && !/^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/.test($('#check_hide_tele').val()) ) ||
            ($('#check_hide_mobile').val() != '' && !/^\d{11}$/.test($('#check_hide_mobile').val())) ||
            $('#check_hide_area').val() == '' ||
            $('#orderForm input[name="zip"]').val() == '' || !/^\d{6}$/.test($('#orderForm input[name="zip"]').val())
        ) {
            TH.Pay.showTip('收货信息不完整或格式错误', 10, 3000, function () {
                $('.address .lnk-edit[data-id="' + order_no + '"]').click();
            });
            return false;
        }

        $('.ui-overlay').height($(document).height());
        $('.ui-overlay,#order-pay-dialog').show();
    }
};

$(function () {
    TH.Pay.init();
});