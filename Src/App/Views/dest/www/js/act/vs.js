/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: vs.js 4144 2014-07-25 10:57:54Z samgui $
 */

window.onload = function () {
    var price1 = $$('j_left_price').innerHTML,
        price2 = $$('j_right_price').innerHTML,
        href = '', imgs, len;

    if ($$('btn')) {
        href = $$('btn').href;
        $$('title').href = href;
        $$('image-wrapper').href = href;
    }

    $$('j_price_1').innerHTML = price1;
    $$('j_price_2').innerHTML = price2;
    $$('j_price').innerHTML = parseFloat(price1) - parseFloat(price2);

    autoResizeImage(300, 300, $$('image'));

    imgs = $$('best-match').getElementsByTagName('img');
    len = imgs.length;
    for (var i = 0; i < len; i++) {
        autoResizeImage(140, 140, imgs[i]);
    }
}

function $$(id) {
    return typeof id == 'string' ? document.getElementById(id) : id;
}

function autoResizeImage(maxWidth, maxHeight, objImg) {
    var img = new Image(), hRatio, wRatio, Ratio = 1, w , h;
    img.src = objImg.src;
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

        objImg.height = h;
        objImg.width = w;
        objImg.style.top = '50%';
        objImg.style.left = '50%';
        objImg.style.marginTop = '-' + (h / 2) + 'px';
        objImg.style.marginLeft = '-' + (w / 2) + 'px';
    }
}