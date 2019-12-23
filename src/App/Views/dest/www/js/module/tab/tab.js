/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: tab.js 4280 2014-07-29 12:54:59Z samgui $
 */

(function ($) {
    $.fn.tab = function (options) {

        $.fn.tab.defaults = {
            trigger: '.ui-tab-trigger', //触发点容器
            container: '.ui-tab-container', //内容容器
            item: '.item',
            current: 'current', //当前触发点以及内容节点样式
            event: 'mouseover', //触发事件类型
            selectedIndex: 0, //默认被选中的索引
            auto: false //自动切换
        };

        var options = $.extend({}, $.fn.tab.defaults, options);

        this.each(function () {
            var $tab = $(this),
                $triggerItems = $tab.find(options.trigger + ' ' + options.item),
                $containerItems = $tab.find(options.container + '>' + options.item),
                currentClass = options.current,
                selectedIndex = options.selectedIndex;

            //设置默认选中
            $triggerItems.eq(selectedIndex).addClass(currentClass);
            $containerItems.eq(selectedIndex).addClass(currentClass);

            $triggerItems.each(function () {
                var $triggerItem = $(this), tabIndex = $triggerItems.index($triggerItem), $containerItem = $containerItems.eq(tabIndex);
                $triggerItem.live(options.event, function () {
                    $triggerItems.removeClass(currentClass);
                    $containerItems.removeClass(currentClass);
                    $triggerItem.addClass(currentClass);
                    $containerItem.addClass(currentClass);
                    selectedIndex = tabIndex;
                })
            });

            if (options.auto) {
                var itemCount = $triggerItems.length;
                setInterval(function () {
                    $triggerItems.eq(selectedIndex).trigger(options.event);
                    selectedIndex++;
                    if (selectedIndex == itemCount) {
                        selectedIndex = 0;
                    }
                }, options.auto);
            }
        })
    };
})(jQuery);