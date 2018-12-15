var Address = {
    init: function() {
        this.myAddressPanelAdjust(),
        this.__initInboundWarehouseSelect()
    },
    myAddressPanelAdjust: function() {
        var e = $('<div class="copy"><a href="#this">\u590d\u5236</a></div>');
        e.on("click", "a",
        function() {
            var e = $(this);
            if ("undefined" != typeof window.clipboardData) {
                var n = e.closest("dd").find(".value").text();
                window.clipboardData.setData("text", n),
                e.text("\u5df2\u590d\u5236")
            } else alert("\u60a8\u7684\u6d4f\u89c8\u5668\u4e0d\u652f\u6301\u526a\u8d34\u677f\u64cd\u4f5c\uff0c\u8bf7\u81ea\u884c\u590d\u5236");
            return ! 1
        }),
        $("#content .my_address_panel").each(function() {
            var n = $(this),
            d = n.find("dd");
            d.mouseleave(function() {
                e.hide()
            }),
            d.mouseenter(function() {
                var n = $(this);
                n.hasClass("hint") || n.hasClass("inline_hint") || n.hasClass("placeholder") || (e.appendTo(n), e.find("a").text("\u590d\u5236"), e.show())
            })
        })
    },
    __initInboundWarehouseSelect: function() {
        var e = $(".inbound_warehouse_select");
        e.find("dd").click(function() {
            var n = $(this);
            e.find("dd.current").removeClass("current"),
            n.addClass("current");
            var d = n.attr("warehouse_code");
            $("#tore").attr("value",d);
        }),
        1 == e.find("dd.current").length ? e.find("dd.current").click() : e.find("dd").length >= 1 && e.find("dd:first").click()
    }
};