/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: pay.js 2284 2014-06-13 07:52:26Z samgui $
 */
var TH = TH || {}, bank = g_pay_info || ['', '', '', ''];
TH.Pay = {
    init: function () {
        var input = $('input[name="payment_type"]'),
            pay_way , index = 0, way = bank[1], pay_type;

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
                payway.find('.selected-bank').show().find('.number')[ payway.find('.paywrap').length == 1 ? 'show' : 'hide']();
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
            $('.payment-popup,.payment-popup .bank-area,#mask').show();
            $('#mask').css('height', height);
        });

        $('.payment-popup-footer .btn').click(function () {
            $('.paywrap .bind-banks').hide();
            $('.paywrap .selected-bank,.paywrap .more').show();
        });

        $('.payment-popup-close,.payment-popup-footer .btn').click(function () {
            $('.payment-popup,#mask').hide();
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
            TH.Pay.set_default_bank($wrap, 0);
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

        TH.Pay.set_default_bank(pay_way, index);
    },
    set_default_bank: function (wrap, index) {
        var selector = wrap.find('.bind-banks li');

        if (selector.length == 0) {
            selector = wrap.find('.banks-list ul').eq(index);
        }

        selector.find('input[value=' + bank[2] + ']').attr('checked', true).parents('li').click();
    }
};

$(function () {
    TH.Pay.init();
});