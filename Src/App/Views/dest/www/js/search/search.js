/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: search.js 5929 2014-09-15 10:27:53Z samgui $
 */

$(function () {
    $('.item-more').click(function () {
        var me = $(this);
        me.parents('.row').toggleClass('row-unfold');
    });

    $('.col-main .mod-list .pic img').each(function () {
        THS.imgAutoResize(this);
    });
});