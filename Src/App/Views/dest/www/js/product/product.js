/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: product.js 6221 2014-09-20 10:12:24Z samgui $
 */
$(function () {
    $('.lnk-detail,.price-summary').hover(function () {
        $('.price-detail').addClass('hover');
    }, function () {
        $('.price-detail').removeClass('hover');
    });

    var slider = $('.gallery .ui-slider');
    if (slider.find('li').length > 7) {
        slider.jCarouselLite({
            btnNext: ".gallery .ui-slider-next",
            btnPrev: ".gallery .ui-slider-prev",
            visible: 7,
            scroll: 1,
            circular: false,
            speed: 200
        }).css('width', '100%');
    }

    $('.gallery img').each(function () {
        THS.imgAutoResize(this);
    });

    $('.gallery .tb-item img').on('mouseover', function () {
        $(this).parents('.tb-item').addClass('active').siblings().removeClass('active');
        $('.img-preview img').attr('src', this.src);
        return false;
    }).on('click', function () {
        return false;
    });

    $('.select-container').each(function () {
        var me = $(this),
            list = me.find('.select-dropdown'),
            item = me.find('.select-item'),
            len = item.length,
            width;

        if (len > 0) {
            width = list.outerWidth() + 20;
            me.width(width);
            list.width(width - 2);
        }
    });

    $('.select-container').hover(function () {
        $(this).addClass('hover');
    }, function () {
        $(this).removeClass('hover');
    })

    $('#amount-decrease,#amount-increase').click(function () {
        changeNumber(this.id == 'amount-increase');
    });

    $('#btn-buyitnow:not(.disabled)').click(function () {
        var sku_id = $('#sku_id').val(),
            spu_id = $('#spu_id').val(),
            num = $.trim($('#amount-input').val());

        if (ISLOGIN) {
            $.ajax({
                type: 'GET',
                url: '/simple/ajax_check_limit',
                dataType: 'json',
                data: {sku_id: sku_id, goods_num: num},
                success: function (data) {
                    if (data.code == 0) {
                        location.href = '/simple/cart2/sku_id/' + sku_id + '/num/' + num;
                    } else {
                        if (data.code == 4) { //商家订单下限
                            $('.buynow-dialog').show();
                        } else {
                            $('#addtocart-dialog').find('.message').html(data.message).end().show().fadeOut(5000);
                        }
                    }
                }
            });
        } else {
            THS.quicklogin();
        }
        return false;
    });

    $('#btn-addtocart:not(.disabled)').click(function () {
        var me = $(this), offset = me.offset(),
            top = offset.top,
            left = offset.left,
            sku_id = $('#sku_id').val(),
            spu_id = $('#spu_id').val(),
            num = $.trim($('#amount-input').val());

        if (ISLOGIN) {
            $.ajax({
                type: 'GET',
                url: '/simple/joinCart',
                dataType: 'json',
                data: {sku_id: sku_id, spu_id: spu_id, goods_num: num},
                success: function (data) {
                    if (data.code == 0) {
                        var thumb = $('#img-thumb'),
                            thumb = thumb.length == 1 ? thumb : $('<img style="position:absolute;border:2px solid #cc2e5b;z-index:99" width="40" height="40" id="img-thumb" src="' + $('.img-preview img').attr('src') + '" />'),
                            animateGap = -100,
                            offsetEnd = $("#ht-toolbar").offset();

                        thumb.appendTo('body').css({
                            top: top + 'px',
                            left: left + 'px'
                        }).show().animate({
                            top: top + animateGap + 'px'
                        }, 300).animate({
                                top: offsetEnd.top + 'px',
                                left: offsetEnd.left + 'px'
                            }, 700,
                            function () {
                                var cart = $('#ht-toolbar .toolbar-item-cart'), num = data.cart_num;
                                $('.ht-navbar .cart-count').html(num);
                                cart.find('.toolbar-item-text').html(num);

                                setTimeout(function () {
                                    cart.addClass('shake');
                                }, 1000);

                                setTimeout(function () {
                                    cart.removeClass('shake');
                                }, 2500);

                                thumb.hide().offset({
                                    top: 0,
                                    left: 0
                                })
                            });
                    } else {
                        $('#addtocart-dialog').find('.message').html(data.message).end().show().fadeOut(5000);
                    }
                }
            });
        } else {
            THS.quicklogin();
        }
        return false;
    });

    $('.buynow-dialog .btn-primary').click(function () {
        $('.buynow-dialog').hide();
        $('#btn-addtocart').click();
    });

    $('.tab-nav .item').click(function () {
        var me = $(this), anchor = me.attr('href').substr(1), top;
        top = $('.mod-' + anchor).offset().top - 55;
        $('html,body').animate({scrollTop: top}, 200);
        location.hash = '';
        return false;
    });

    $(window).scroll(function () {
        var st = $(document).scrollTop();
        $('.col-main .tab-pane .mod').each(function () {
            var me = $(this), top = me.offset().top - 55,
                cls = me[0].className.replace('mod mod-', '');

            if (st >= top) {
                $('.tab-nav .item[href="#' + cls + '"]').addClass('active').siblings().removeClass('active');
            }
        });
    });

    function changeNumber(increase) {
        var $amount = $('#amount-input'), amount = $.trim($amount.val());
        if (increase) {
            amount++;
        } else {
            amount--;
        }
        amount = amount < 1 ? 1 : amount;
        $('#amount-input').val(amount);
    }
});