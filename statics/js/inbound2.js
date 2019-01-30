var Inbound = {};
Inbound.Brief = {
    init: function() {
        this.__initWarehouseExpressSelect(),
        this.__initItemList()
    },
    __initWarehouseExpressSelect: function() {
        var e = $(".inbound_warehouse_select"),
        t = $(".express_select");
        e.find("dd").click(function() {
            var i = $(this);
            e.find("dd.current").removeClass("current"),
            i.addClass("current");
            var n = i.attr("warehouse_id"),
            a = i.attr("unit_price_label");
            $("#inbound_warehouse_id").val(n),
            $("#inbound_express_info").show(),
            $("#inbound_express_info_submit_assisted_declaration").show(),
            $("#inbound_items_info").show(),
            $("#inbound_info_submit").show(),
            $("#unit_price_label").text(a),
            "1083894446703281793" == n ? ($("#submit_assisted_declaration").hide(), $("#nz_alert").hide()) : "1157438631417009282" == n ? ($("#submit_assisted_declaration").show(), $("#nz_alert").show()) : ($("#submit_assisted_declaration").show(), $("#nz_alert").hide()),
            t.find("option").remove(),
            t.append($("<option>").text("\u8bf7\u9009\u62e9").val("").attr("selected", 0 == inbound_lu_express_id));
            for (var d = 0; d < expresses[n].length; d++) {
                var l = $("<option>").text(expresses[n][d].name).val(expresses[n][d].id);
                inbound_lu_express_id > 0 && expresses[n][d].id.toString() == inbound_lu_express_id.toString() && l.attr("selected", !0),
                t.append(l)
            }
        }),
        1 == e.find("dd.current").length ? e.find("dd.current").click() : 1 == e.find("dd").length && e.find("dd:first").click(),
        t.change(function() {
            var e = t.find("option:selected").val();
            "undefined" == typeof e && (e = ""),
            t.closest("td.item").find("input").val(e)
        })
    },
    __itemListIndex: 0,
    __initItemList: function() {
        this.__initItemListAddItem(),
        this.__initItemListRemoveItem(),
        0 == $("#inbound_item_list table tbody tr").length && $("#inbound_item_list_button .add_item_button").click()
    },
    __initItemListAddItem: function() {
        var e = this;
        $("#inbound_item_list_button .add_item_button").click(function() {
            for (var t = "inbound[inbound_items_attributes][" + (e.__itemListIndex++).toString() + "]", i = $('<tr class="added_item" item_name_prefix="' + t + '"><td><input class="textbox item_name" placeholder="\u4e2d\u6587\u540d\u79f0" name="' + t + '[name]" /></td><td><select class="select parent_category"><option value="" selected="selected">\u8bf7\u9009\u62e9</option></select></td><td><select class="select category" name="' + t + '[lu_category_id]"><option value="" selected="selected">\u8bf7\u9009\u62e9</option></select></td><td><input class="textbox item_brand" placeholder="\u82f1\u6587\u54c1\u724c" name="' + t + '[brand]" /></td><td><div class="item_model_select_area" style="display:none;"><select class="select item_model_select"><option value="" selected="selected">\u8bf7\u9009\u62e9</option></select></div><div class="item_model_textbox_area"><input class="textbox item_model" placeholder="\u578b\u53f7" /></div><input class="item_model_hidden" type="hidden" name="' + t + '[model]" /></td><td><input class="textbox item_unit_price" placeholder="\u5355\u4ef7" name="' + t + '[dollar]" /></td><td><input class="textbox item_amount" placeholder="\u6570\u91cf" name="' + t + '[num]" /></td><td>  <a class="remove_line" href="#this">\u5220\u9664</a></td></tr>'), n = i.find("select.parent_category"), a = i.find("select.category"), d = 0; d < categories.length; d++) n.append($("<option>").val(categories[d].id).text(categories[d].name));
            n.change(function() {
                a.html('<option value="" selected="selected">\u8bf7\u9009\u62e9</option>');
                for (var e = n.val(), t = 0; t < categories.length; t++) if (e == categories[t].id.toString()) {
                    for (var i = 0; i < categories[t].sub_categories.length; i++) {
                        var d = categories[t].sub_categories[i];
                        a.append($("<option>").val(d.id).text(d.name).attr({
                            force_insurance_amount: d.force_insurance_amount,
                            model_selection_string: d.model_selection_string
                        }))
                    }
                    break
                }
                a.change()
            });
            var l = i.find("select.item_model_select"),
            o = i.find("input.item_model"),
            s = i.find("input.item_model_hidden");
            return l.change(function() {
                var e = l.find("option:selected");
                "\u81ea\u5b9a\u4e49\u578b\u53f7" == e.text() ? (o.val(""), i.find(".item_model_textbox_area").css("paddingTop", "5px").show()) : (o.val(""), i.find(".item_model_textbox_area").hide()),
                s.val(e.attr("value"))
            }),
            o.change(function() {
                s.val(o.val())
            }),
            a.change(function() {
                a.closest("tr").find(".notice").remove();
                var t = a.find("option:selected"),
                n = t.attr("force_insurance_amount");
                "undefined" != typeof n && "0.0" != n && e.__itemListInlineAlert(a, "\u8be5\u7c7b\u5546\u54c1\u6536\u53d6" + n + "\u5143\u5f3a\u5236\u4fdd\u9669", "notice"),
                s.val(""),
                o.val("");
                var d = t.attr("model_selection_string");
                if ("undefined" != typeof d && "" != d) {
                    l.empty(),
                    l.append($("<option>").text("\u8bf7\u9009\u62e9"));
                    var _ = $("<optgroup>");
                    _.attr("label", "\u81ea\u5b9a\u4e49\u578b\u53f7"),
                    _.append($("<option>").attr("manual", "manual").text("\u81ea\u5b9a\u4e49\u578b\u53f7")),
                    l.append(_);
                    for (var r = d.split(";;"), m = 0; m < r.length; m++) {
                        r[m] = r[m].split("||"),
                        r[m][1] = r[m][1].split(",,");
                        var c = $("<optgroup>");
                        c.attr("label", r[m][0]);
                        for (var u = 0; u < r[m][1].length; u++) {
                            var p = $("<option>"),
                            f = r[m][1][u];
                            p.val(f),
                            p.text(f),
                            c.append(p)
                        }
                        l.append(c)
                    }
                    i.find(".item_model_select_area").show(),
                    i.find(".item_model_textbox_area").hide()
                } else i.find(".item_model_select_area").hide(),
                i.find(".item_model_textbox_area").css("paddingTop", "0px").show();
                "124" == t.val() && (e.__itemListInlineAlert(i.find(".item_model_textbox_area"), "\u8bf7\u586b\u5199\u6bcf\u7f50\u514b\u91cd", "notice"), e.__itemListInlineAlert(i.find(".item_amount"), "\u8bf7\u586b\u5199\u7f50\u6570", "notice")),
                "127" == t.val() && (e.__itemListInlineAlert(i.find(".item_name"), "\u54c1\u724c+\u7247\u6570\uff0c\u5982\u82b1\u738b50\u7247", "notice"), e.__itemListInlineAlert(i.find(".item_amount"), "\u8bf7\u586b\u5199\u5355\u5305\u6570\u91cf\uff0c\u4e0d\u8981\u5199\u7bb1\u6570", "notice")),
                ("166" == t.val() || "144" == t.val()) && e.__itemListInlineAlert(i.find(".item_name"), "\u8bf7\u586b\u5199\u5177\u4f53\u978b\u5b50\u540d\u79f0\uff0c\u5982\u7ae5\u978b\uff0c\u9ad8\u8ddf\u978b\uff0c\u9774\u5b50\uff0c\u8fd0\u52a8\u978b\u7b49", "notice")
            }),
            $("#inbound_item_list table tbody").append(i),
            Common.commonListAdjust(),
            !1
        })
    },
    __initItemListRemoveItem: function() {
        var e = this;
        $("#inbound_item_list").on("click", "a.remove_line",
        function() {
            var t = $(this),
            i = t.closest("tr");
            if ("undefined" != typeof i.attr("inbound_item_id")) {
                var n = "inbound[inbound_items_attributes][" + (e.__itemListIndex++).toString() + "]";
                i.closest("#inbound_item_list").append($("<input>").attr({
                    type: "hidden",
                    name: n + "[id]"
                }).val(i.attr("inbound_item_id"))),
                i.closest("#inbound_item_list").append($("<input>").attr({
                    type: "hidden",
                    name: n + "[_destroy]"
                }).val("1"))
            }
            return i.remove(),
            0 == $("#inbound_item_list tbody tr").length && $("#inbound_item_list_button .add_item_button").click(),
            Common.commonListAdjust(),
            !1
        })
    },
    __itemListNotify: function(e, t) {
        $("#list_notify div").remove(),
        $("#list_notify").append($("<div>").addClass(t).text(e)).show()
    },
    __itemListInlineAlert: function(e, t, i) {
        "alert" == i ? (e.closest("td").find(".inline_alert.alert").remove(), e.closest("td").find(".inline_alert.notice").hide()) : e.closest("td").find(".inline_alert").remove(),
        e.closest("td").append($("<div>").addClass("inline_alert " + i).text(t))
    },
    itemListValid: function() {
        var e = this,
        t = !0;
        return 0 == $("#inbound_item_list table tbody tr").length && (t = !1, e.__itemListNotify("\u8bf7\u6dfb\u52a0\u5feb\u9012\u5355\u4e2d\u5546\u54c1\u4fe1\u606f", "alert")),
        $("#inbound_item_list table tbody tr.added_item").each(function() {
            var i = $(this);
            i.find("td .alert").remove(),
            i.find("td .notice").show();
            var n = i.find("input.item_name");
            n.val($.trim(n.val())),
            /^[0-9\u4e00-\u9fa5_-]+$/.test(n.val()) || (t = !1, e.__itemListInlineAlert(n, "\u8bf7\u8f93\u5165\u4e2d\u6587\u540d\u79f0", "alert"));
            var a = i.find("select.category");
            /^[0-9]+$/.test(a.find("option:selected").val()) || (t = !1, e.__itemListInlineAlert(a, "\u8bf7\u9009\u62e9\u7c7b\u522b", "alert"));
            var d = a.find("option:selected").attr("require_brand") || !0;
            if ("undefined" != typeof d && "false" != d) {
                var l = i.find("input.item_brand");
                l.val($.trim(l.val())),
                /^.+$/.test(l.val()) ? /^[a-zA-Z0-9\'\"\-\s]+$/.test(l.val()) ? /[a-zA-Z]/.test(l.val()) || (t = !1, e.__itemListInlineAlert(l, "\u8bf7\u8f93\u5165\u82f1\u6587\u54c1\u724c", "alert")) : (t = !1, e.__itemListInlineAlert(l, "\u8bf7\u8f93\u5165\u82f1\u6587\u54c1\u724c", "alert")) : (t = !1, e.__itemListInlineAlert(l, "\u8bf7\u8f93\u5165\u54c1\u724c", "alert"))
            }
            var o = a.find("option:selected").attr("require_model") || !0;
            if ("undefined" != typeof o && "false" != o) {
                var s = i.find("input.item_model_hidden");
                /^.+$/.test(s.val()) || (t = !1, e.__itemListInlineAlert(s, "\u8bf7\u8f93\u5165\u578b\u53f7", "alert"))
            }
            var _ = i.find("input.item_unit_price");
            _.val($.trim(_.val())),
            0 != _.val().length && /^(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(_.val()) ? (isNaN(parseFloat(_.val())) || parseFloat(_.val()) <= 0) && (t = !1, e.__itemListInlineAlert(_, "\u5355\u4ef7\u5fc5\u987b\u5927\u4e8e0", "alert")) : (t = !1, e.__itemListInlineAlert(_, "\u8bf7\u8f93\u5165\u6b63\u786e\u7684\u5355\u4ef7", "alert"));
            var r = i.find("input.item_amount");
            r.val($.trim(r.val())),
            (!/^\d+$/.test(r.val()) || parseInt(r.val()) <= 0) && (t = !1, e.__itemListInlineAlert(r, "\u8bf7\u8f93\u5165\u6b63\u786e\u7684\u6570\u91cf", "alert"))
        }),
        t
    }
};