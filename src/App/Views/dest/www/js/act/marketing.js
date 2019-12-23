/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: marketing.js 5896 2014-09-15 06:00:09Z heaven $
 */
var FX = FX || {};

FX = {
	// Cookie操作
	Cookie: {
		set: function(key, val, h) {
			if (h) {
				var date = new Date();
				date.setTime(date.getTime() + (h * 60 * 60 * 1000));
				var expires = "; expires=" + date.toGMTString();
			} else {
				var expires = '';
			}
			document.cookie = key + "=" + val + expires + "; domain=.layhangtrungviet.com;path=/";
		},
		get: function(key) {
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
		remove: function(key) {
			this.set(key, '', -1);
		}
	},

	// 获取URL参数
	getQueryString: function(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
		var r = window.location.search.substr(1).match(reg);
		if (r !== null) return unescape(r[2]);
		return null;
	},

	// 图片等比例缩放
	imgResize: function(objImg, maxWidth, maxHeight) {
		var img = new Image(),
			hRatio, wRatio, Ratio = 1,
			w, h;
		$(img).load(function() {
			w = img.width;
			h = img.height;
			wRatio = maxWidth / w;
			hRatio = maxHeight / h;
			if (maxWidth == 0 && maxHeight == 0) {
				Ratio = 1;
			} else if (maxWidth == 0) { //
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
		});
		img.src = objImg.src;
	}
}

// 写入URL中htag参数到cookie
FX.Cookie.set('htag', FX.getQueryString('htag'));

$(function() {
	//图片等比例缩放
	$('.list-item .pic img').each(function(index, el) {
		var img = $(this),
			w = img.width(),
			h = img.height();
		FX.imgResize(this, w, h);
		img.removeAttr('width').removeAttr('height');
	});
});