(function () {
    var _getText = function (html) {
        return html.replace(/<[^>]+>/g, '', html);
    };

    //链接点击统计
    $('body').delegate('a', 'mousedown', function () {
        var title = this.title;
        var html = $.trim(this.innerHTML);
        var txt = title || _getText(html);
        if (txt) {
            THS.sendPV('index', 'link_click/' + txt);
        }
    });

    $(function () {
        $('.ui-slider').slider({offset:['-45px', '0']});

        $('.list-item .pic img').each(function () {
            var me = this, img = new Image();

            img.onload = function () {
                var w = img.width, h = img.height;
                $(me).css({
                        top: '50%',
                        left: '50%',
                        margin: '-' + (h / 2) + 'px 0 0 -' + (w / 2) + 'px' }
                ).fadeIn(100);
            }

            img.src = me.src;
        });
    });
})();