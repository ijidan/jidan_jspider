/**
 * @Copyright 2014 Taohai, Inc.
 * @update $Id: address.js 5618 2014-09-09 03:39:53Z samgui $
 */

var TH = TH || {}, g_address, g_idcard, g_address_data;

TH.Helper = {
    //模版替换
    template: function (str, data) {
        var fn = new Function("data", "var p=[];p.push('" + str.replace(/[\r\t\n]/g, " ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g, "$1\r").replace(/\t=(.*?)%>/g, "',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'") + "');return p.join('');");
        return data ? fn(data) : '';
    },
    //获得中英文混合字符长度
    get_length: function (s) {
        return s.replace(/[^\x00-\xff]/g, "aa").length;
    },
    //身份证自动空格.
    format: function (input) {
        var idValue = $.trim(input);
        if (idValue != "") {
            var idLength = idValue.length;
            if (idLength <= 3) {
                idNumber.value = idValue;
            } else {
                if (idLength <= 6) {
                    idNumber.value = idValue.substring(0, 3) + " " + idValue.substring(3, idLength);
                } else {
                    if (idLength <= 10) {
                        idNumber.value = idValue.substring(0, 3) + " " + idValue.substring(3, 6) + " " + idValue.substring(6, idLength);
                    } else {
                        if (idLength <= 14) {
                            idNumber.value = idValue.substring(0, 3) + " " + idValue.substring(3, 6) + " " + idValue.substring(6, 10) + " " + idValue.substring(10, idLength);
                        } else {
                            idNumber.value = idValue.substring(0, 3) + " " + idValue.substring(3, 6) + " " + idValue.substring(6, 10) + " " + idValue.substring(10, 14) + " " + idValue.substring(14, idLength);
                        }
                    }
                }
            }
        }
    }
};

TH.Validator = {
    //验证邮政编码
    check_zipcode: function (input) {
        return /^\d{6}$/.test(input);
    },
    //验证手机号
    check_mobile: function (input) {
        return /^0{0,1}(13[0-9]|15[0-9]|18[0-9]|14[7])[0-9]{8}$/.test(input);
    },
    //验证固定电话
    check_telphone: function (input) {
        return /^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/.test(input);
    },
    //验证身份证
    check_idcard: function (input) {
        var idNum = input,
            errors = new Array(
                "验证通过",
                "身份证号码位数不对",
                "身份证含有非法字符",
                "身份证号码校验错误",
                "身份证地区非法"
            ),
            re, //身份号码位数及格式检验
            len = idNum.length,
            idcard_array = new Array();

        //身份证位数检验
        if (len != 18) {
            return errors[1];
        } else {
            re = new RegExp(/^(\d{6})()?(\d{4})(\d{2})(\d{2})(\d{3})([0-9xX])$/);
        }

        var area = { 11: "北京", 12: "天津", 13: "河北", 14: "山西",
            15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海",
            32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西",
            37: "山东", 41: "河南", 42: "湖北", 43: "湖南", 44: "广东",
            45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州",
            53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃", 63: "青海",
            64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门",
            91: "国外"
        }

        idcard_array = idNum.split("");
        //地区检验
        if (area[parseInt(idNum.substr(0, 2))] == null) {
            return errors[4];
        }
        //出生日期正确性检验
        var a = idNum.match(re);
        if (a != null) {

            var DD = new Date(a[3] + "/" + a[4] + "/" + a[5]);
            var flag = DD.getFullYear() == a[3] && (DD.getMonth() + 1) == a[4] && DD.getDate() == a[5];

            if (!flag) {
                return "身份证出生日期不对！";
            }

            //检验校验位

            S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7
                + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9
                + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10
                + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5
                + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8
                + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4
                + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2
                + parseInt(idcard_array[7]) * 1
                + parseInt(idcard_array[8]) * 6
                + parseInt(idcard_array[9]) * 3;
            Y = S % 11;
            M = "F";
            JYM = "10X98765432";
            M = JYM.substr(Y, 1); //判断校验位
            //检测ID的校验位
            if (M == idcard_array[17]) {
                return "1";
            }
            else {
                return errors[3];
            }
        } else {
            return errors[2];
        }
        return true;
    },
    check_image_upload: function (input) {
        return /\.(jpg|jpeg|png)$/.test(input.toLowerCase());
    }
};

TH.Address = {
    //初始化
    init: function () {
        TH.Address.load_address();
        TH.Address.load_idcard();

        //初始化地域联动
        TH.Address.area_select('province', 0);

        $(document).click(function (e) {
            var target = e.target;
            if (target.className != 'idcard-list' && target.id != 'number') {
                $('.idcard-list').hide();
            }
        });

        document.getElementById('form-submit')._callback = function (rsp) {
            var name = $('#accept_name').val(), id = $('.address .selected input[type="radio"]').val();

            setTimeout(function () {
                if (rsp.code == 0) {
                    TH.Address.success(null, '保存成功');
                    TH.Address.load_address({id: id, name: name});
                    setTimeout(function () {
                        $('.add_address').hide();
                    }, 1000);
                } else {
                    TH.Address.warning(null, rsp.message);
                }
                $('#btn-submit').attr('disabled', false);
                $('.label-console').removeClass('label-loading');
                return false;
            }, 0);
        };

        $('#checkout .address tr').live('click', function (e) {
            var me = $(this), rd = me.find('input[type="radio"]'), id;
            if (e.target.nodeName == 'TR') {
                $('.add_address').hide();
            }
            me.addClass('selected').siblings().removeClass('selected');
            rd.attr('checked', true);
            id = rd.val();
            $.each(g_address, function (i, d) {
                if (d.address_no == id) {
                    TH.Address.set_order_address(d);
                }
            });
        });

        $('.order-address .update').live('click', function () {
            $('.address .lnk-edit[data-id="' + $(this).data('id') + '"]').trigger('click');
        });

        $('.address .lnk-edit').live('click', function () {
            var id = $(this).data('id');
            $('.label-console').hide();
            TH.Address.edit_address(id);
            $('.add_address').show();
            $('.add_address .txt').removeClass('.f-error');
        });

        $('.address .lnk-delete').live('click', function () {
            var id = $(this).data('id');
            TH.Address.form_empty();
            if (confirm('确认要删除该条记录？')) {
                $.ajax({
                    url: '/address/del_address',
                    type: 'POST',
                    dataType: 'json',
                    data: {address_no: id, _: (+new Date())}
                }).done(function (result) {
                    if (result.code == 0) {
                        alert('删除成功');
                        TH.Address.form_empty();
                        TH.Address.load_address();
                    } else {
                        alert(result.message);
                    }
                    return false;
                });
                return false;
            }
        });

        $('.address .lnk-default').live('click', function () {
            var me = $(this), id = me.data('id'), tr = me.parents('tr');
            tr.addClass('default').find('.col-default').html('<span class="label-default">默认地址</span>');
            tr.siblings().removeClass('default').find('.col-default').html('<a class="lnk-default" href="javascript:;" data-id="' + id + '">设为默认</a>');

            $.ajax({
                url: '/address/set_default_address',
                type: 'POST',
                dataType: 'json',
                data: {address_no: id, _: (+new Date())}
            }).done(function (result) {
                if (result.code != 0) {
                    alert(result.message);
                }
                return false;
            });
            return false;
        });

        $('.address .icon-error').live('mouseover', function () {
            $('.reason-box').hide();
            $(this).next('.reason-box').show();
        });

        $('.reason-box').live('mouseover', function () {
            $(this).show();
        });

        $('.reason-box').live('mouseout',function () {
            $(this).hide();
        }).find('.btn').live('click', function () {
            $(this).parents('tr').find('.lnk-edit').trigger('click');
        });

        $('.reason-box').find('.btn,.close').live('click', function () {
            $('.reason-box').hide();
        });

        //设置默认地址
        $('.address tr').live('mouseover',function () {
            $(this).find('.lnk-default').show();
        }).live('mouseout', function () {
            $(this).find('.lnk-default').hide();
        });

        $('#btn-new').click(function () {
            TH.Address.form_empty();
            $('.label-console').hide();
            $('.add_address').show();
        });

        $('.add_address .txt').bind('blur', function () {
            return TH.Address.validate(this, true);
        });

        $('.add_address input[type="file"]').change(function () {
            return TH.Address.validate(this);
        });

        $('#number').focus(function () {
            var name = $.trim($('#accept_name').val());
            if (name == '') {
                $('.idcard-list').show();
            }
        });

        $('.idcard-list li').live('click', function () {
            var me = $(this), id = me.data('id'), idcard, readonly;

            $.each(g_idcard, function (k, v) {
                if (v.accept_name == id) {
                    readonly = v.check_status == 0 || v.check_status == 2;
                    $('#accept_name').val(v.accept_name);
                    $('#number').val(v.number);
                    $('[data-group="idcard"]')[(readonly ? 'add' : 'remove') + 'Class']('disabled').attr('readonly', readonly);
                    TH.Address.set_status(v);
                    $('.idcard-list').hide();
                }
            });

        });

        $('.add_address .select').change(function () {
            return TH.Address.validate(this);
        });

        $('#form-address').submit(function () {
            var valid = true;
            $('.add_address .txt,.add_address .select,.add_address input[type="file"]').each(function () {
                valid = TH.Address.validate(this);
                if (!valid) {
                    if (this.type != 'file') {
                        this.focus();
                    }
                    return false;
                }
            });
            if (valid) {
                $('.label-console').addClass('label-loading').html('<i class="icon-loading"></i>数据提交中...');
                $('#btn-submit').attr('disabled', true);
            }
            return valid;
        });

        $('#btn-cancel').click(function () {
            $('.add_address').hide();
        });

        $('#province,#city').change(function () {
            var id = this.id , val = $(this).val();
            id = id == 'province' ? 'city' : 'area';
            TH.Address.area_select(id, val);
        });
    },
    success: function (el, msg) {
        var label = $('.label-console');

        if (el) {
            el.removeClass('f-error');
            label.addClass('label-success');
        }

        if (msg) {
            label.css('display', 'inline-block').html(msg);
        } else {
            label.hide();
        }

        return true;
    },
    warning: function (el, msg) {
        if (el) {
            el.addClass('f-error').removeClass('f-success');
        }

        $('.label-console').removeClass('label-success').css('display', 'inline-block').html(msg);

        return false;
    },
    //表单验证
    validate: function (o, v) {
        var $el = $(o),
            field = $el.parents('td').prev('.col-field').html().replace(/\s/g, ''),
            val = $.trim($el.val()),
            len = TH.Helper.get_length(val),
            required = $el.data('required'),
            visible = $el.is(':visible'),
            warn = TH.Address.warning,
            success = TH.Address.success;

        if (val == '') {
            if (required && visible) {
                return warn($el, $el.is('select') ? '请选择' + field : '请填写' + field);
            } else {
                return success($el);
            }
        } else {
            if (o.id == 'accept_name' && len > 20) {
                return warn($el, '长度不能超过10个中文字符或20个英文字符');
            }
            if (v && (o.id == 'accept_name' || o.id == 'number')) {
                var readonly, exist = false;
                if (val.length > 0) {
                    $.each(g_idcard, function (k, card) {
                        if (card[o.id] == val) {
                            readonly = card.check_status == 0 || card.check_status == 2;
                            if (readonly) {
                                $('#accept_name').val(card.accept_name);
                                $('#number').val(card.number);
                                TH.Address.set_status(card);
                                exist = true;
                            }
                            $('[data-group="idcard"]')[(readonly ? 'add' : 'remove') + 'Class']('disabled').attr('readonly', readonly);
                        }
                    });
                }
                if (!exist) {
                    var number = $('#number').val(), accept_name = $('#accept_name').val();
                    $.each(g_idcard, function (k, card) {
                        if (card.number == number || card.accept_name == accept_name) {
                            if (card.check_status != 1 && card.check_status != 4) {
                                $('#' + (o.id == 'number' ? 'accept_name' : 'number')).val('');
                                TH.Address.set_status({
                                    front_pic: '',
                                    back_pic: '',
                                    check_status: ''
                                });
                            }
                        }
                    });

                    $('[data-group="idcard"]').removeClass('disabled').attr('readonly', false);
                }
            }
            if (o.id == 'tel') {
                //只保留一种联系方式
                if (TH.Validator.check_mobile(val)) {
                    $('#mobile').val(val);
                    $('#telphone').val('');
                } else {
                    if (TH.Validator.check_telphone(val)) {
                        $('#telphone').val(val);
                        $('#mobile').val('');
                    } else {
                        return warn($el, field + '格式不正确');
                    }
                }
            }
            if (o.id == 'zip' && !TH.Validator.check_zipcode(val)) {
                return warn($el, field + '格式不正确');
            }
            if (o.id == 'number') {
                var result = TH.Validator.check_idcard(val);
                if (result !== true && result != '1') {
                    return warn($el, result);
                }
            }
            if (o.id == 'idcard1' || o.id == 'idcard2') {
                var $number = $('#number'), number = $.trim($number.val());
                if (!TH.Validator.check_image_upload(val)) {
                    return warn($el, '请上传JPG、JPEG和PNG格式的图片');
                }

                if ($el.is(':visible') && number == '') {
                    return warn($number, '请填写身份证号');
                }
            }
            return success($el);
        }
    },
    //加载地址信息
    load_address: function (o) {
        $.ajax({
            url: '/address/my_address',
            type: 'GET',
            dataType: "json",
            data: {"page": 1, "page_size": 100, _: (+new Date())}
        }).done(function (result) {
            var $address = $('.address'), html = '', no = $('#address_no').val(), len;
            g_address = result.data;
            if (result.code == 0) {
                if (g_address.length > 0) {
                    $.each(result.data, function (i, d) {
                        var tpl = $('#tpl-address').html(), status = d.check_status,
                            statusCls , statusLabel, defaultLabel, tel;

                        defaultLabel = d['default'] == '1' ?
                            '<span class="label-default">默认地址</span>' :
                            '<a class="lnk-default" href="javascript:;" data-id="' + d.address_no + '">设为默认</a>';

                        status = TH.Address.get_status(status, d.reject_reason);
                        statusCls = status.statusCls;
                        statusLabel = status.statusLabel;
                        tel = d.mobile == '' ? d.telphone : d.mobile;

                        if (d['default'] == '1') {
                            $('#idcard_id').val(d.accept_name);
                            if ($('#checkout').length > 0) {
                                TH.Address.set_order_address(d);
                            }
                        }

                        //TH.Helper.template
                        html += tpl.replace(/{defaultCls}/gi, (d['default'] == '1' ? ' class="default selected"' : ''))
                            .replace(/{accept_name}/gi, d.accept_name)
                            .replace(/{address_no}/gi, d.address_no)
                            .replace(/{tel}/gi, tel)
                            .replace(/{province}/gi, d.province_name)
                            .replace(/{city}/gi, d.city_name)
                            .replace(/{area}/gi, d.area_name)
                            .replace(/{address}/gi, d.address)
                            .replace(/{statusCls}/gi, statusCls)
                            .replace(/{statusLabel}/gi, statusLabel)
                            .replace(/{defaultLabel}/gi, defaultLabel)
                            .replace(/{id}/gi, d.address_no);
                    });
                } else {
                    html += '<tr><td colspan="6" class="row-empty">还没添加收货地址</td></tr>';
                }
                $address.find('tbody').html(html);

                if (no == '' && o) {//选中新增的收货地址
                    $address.find('[data-name="' + o.name + '"] input[type="radio"]').click();
                } else {
                    //选中默认收货地址
                    $address.find('.default input[type="radio"]').click();

                    len = $address.find('input[type="radio"]:checked').length;

                    //没有设置默认收货地址则选择第一个
                    if (len == 0) {
                        $address.find('input[type="radio"]:first').click();
                    }
                }
            }
        });
    },
    set_order_address: function (d) {
        var order_tpl = $('#tpl-order-address').html(), tel = d.mobile == '' ? d.telphone : d.mobile;
        $('#idcard_id').val(d.accept_name);
        $('.order-address').html(order_tpl
            .replace(/{id}/gi, d.address_no)
            .replace(/{accept_name}/gi, d.accept_name)
            .replace(/{tel}/gi, tel)
            .replace(/{province}/gi, d.province_name)
            .replace(/{city}/gi, d.city_name)
            .replace(/{area}/gi, d.area_name)
            .replace(/{address}/gi, d.address)
        );

        $('#orderForm input[name="radio_address"]').val(d.radio_address);
        $('#orderForm input[name="accept_name"]').val(d.accept_name);
        $('#orderForm input[name="province"]').val(d.province);
        $('#orderForm input[name="city"]').val(d.city);
        $('#orderForm input[name="area"]').val(d.area);
        $('#orderForm input[name="address"]').val(d.address);
        $('#orderForm input[name="zip"]').val(d.zip);
        $('#orderForm input[name="telephone"]').val(d.telphone);
        $('#orderForm input[name="mobile"]').val(d.mobile);
        $('#orderForm input[name="addressdetail"]').val(d.province_name + ' ' + d.city_name + ' ' + d.area_name
            + ' ' + d.address + ' ' + d.accept_name + '，' + tel);
        $('#orderForm input[name="address_no"]').val(d.address_no);
    },
    set_area_select: function (el, id) {
        var name , code , len  , html = '', $el = $('#' + el);

        name = g_address_data['name' + id];
        len = name.length;

        if (len > 0) {
            name = g_address_data['name' + id].split(',');
            code = g_address_data['code' + id].split(',');
            len = name.length;

            html += '<option value="">请选择</option>';
            for (var i = 0; i < len; i++) {
                html += '<option value="' + code[i] + '">' + name[i] + '</option>';
            }
            $el.show().html(html);
        } else {
            $el.hide();
        }
    },
    //省市县联动
    area_select: function (el, id) {
        if (!g_address_data) {
            $.getScript('http://manager.layhangtrungviet.com/www/js/module/address/address.data.js').done(function () {
                g_address_data = address;
                TH.Address.set_area_select(el, id);
            });
        } else {
            TH.Address.set_area_select(el, id);
        }
    },
    //获取状态信息
    get_status: function (status, reject_reason) {
        var statusCls = '', statusLabel = '', txt_btn = '';
        if (status == 0) {
            statusCls = 'info';
            statusLabel = '审核中';
        } else if (status == 1 || status == 3 || status == 4) {
            statusCls = 'error';
            reject_reason = status == 3 ? '身份证已过期，请更换' : (status == 4 ? '身份证信息不完整' : reject_reason);
            txt_btn = status == 4 ? '补充' : '重新上传';
            statusLabel = status == 3 ? '已过期' : '未通过';
            statusLabel += '<i class="icon-error"></i>\
							<div class="reason-box">\
								<i class="arrow"></i>\
								<a class="close" href="javascript:;">&times;</a>\
								<p>' + reject_reason + '</p>\
								<a href="#add" class="btn btn-primary">' + txt_btn + '</a>\
							</div>';
        } else if (status == 2) {
            statusCls = 'success';
            statusLabel = '已通过';
        } else {
            statusCls = '';
            statusLabel = '未上传';
        }
        return {statusCls: statusCls, statusLabel: statusLabel};
    },
    //设置状态信息
    set_status: function (o) {
        var status = parseInt(o.check_status), $tip = $('.add_address .col-action .tip');

        if (status === 0) {
            $tip.css('margin-top', '-30px').html('身份证信息已上传，审核中');
        } else if (status == 1) {
            $tip.css('margin-top', '-38px').html('身份证信息审核不通过，请重新上传<p>原因：' + o.reject_reason + '</p>');
        } else if (status == 4) {
            $tip.css('margin-top', '-30px').html('审核不通过原因：身份证信息不完整');
        } else {
            $tip.html('');
        }

        $('.add_address input[type="file"]').each(function (k, v) {
            var me = $(this), pic,
                wrapper = me.parents('.ui-uploader'),
                label = wrapper.next('.success'), lnk = label.find('.lnk-upload');

            if (label.length == 0) {
                label = $('<span class="success"><i class="icon-selected"></i>已上传</span>');
                wrapper.after(label);
            }

            if (status == 1 || status == 4) {
                if (lnk.length == 0) {
                    lnk = $('<a href="javascript:;" class="lnk-upload">重新上传</a>');
                    label.append(lnk);
                }
                lnk.show();
            } else {
                lnk.hide();
            }

            pic = this.id == 'idcard1' ? o.front_pic : o.back_pic;

            if (pic) {
                wrapper.hide();
                label.show();
            } else {
                wrapper.show();
                label.hide();
            }

            $('.lnk-upload').click(function () {
                var self = $(this), span = self.parent('span');
                span.hide();
                span.prev('.ui-uploader').show();
            });
        });
    },
    //加载身份证信息
    load_idcard: function () {
        $.ajax({
            url: '/address/my_idcard',
            type: 'GET',
            data: { _: (+new Date())},
            dataType: "json"
        }).done(function (result) {
            var html = '', data;
            g_idcard = result.data;
            if (result.code == 0) {
                if (g_idcard.length > 0) {
                    $.each(g_idcard, function (i, d) {
                        var status = d.check_status,
                            number = d.number, len = number.length, number = number.substr(len - 4, len),
                            statusCls = '', statusLabel = '', id = d.accept_name, idcard_id = $('#idcard_id').val();

                        if (status == 0 || status == 2) {//审核中、已通过
                            status = TH.Address.get_status(d.check_status, '');
                            statusCls = status.statusCls;
                            statusLabel = status.statusLabel;
                            html += '<li ' + (id == idcard_id ? 'class="default"' : '') + ' data-id="' + id + '"><span class="name">' +
                                d.accept_name + '</span><span class="number">****' +
                                number + '</span><span class="status ' + statusCls + '">' +
                                statusLabel + '</span></li>';
                        }
                    });
                }
                $('.idcard-list').html(html);
            }
        });
    },
    //编辑地址信息
    edit_address: function (id) {
        var len = g_address.length, ga, tel, idcard, readonly, status;

        for (var i = 0; i < len; i++) {
            if (g_address[i].address_no == id) {
                ga = g_address[i];
            }
        }

        TH.Address.area_select('city', ga.province);
        TH.Address.area_select('area', ga.city);

        status = ga.check_status;
        readonly = status == 0 || status == 2;
        tel = ga.mobile == '' ? ga.telphone : ga.mobile;

        $('#accept_name').val(ga.accept_name);
        $('#address_no').val(ga.address_no);
        $('#province').val(ga.province);
        $('#city').val(ga.city);
        $('#area').val(ga.area);
        $('#address').val(ga.address);
        $('#tel').val(tel);
        $('#mobile').val(ga.mobile);
        $('#telphone').val(ga.telphone);
        $('#zip').val(ga.zip);
        $('#number').val(ga.number);
        TH.Address.set_status(ga);
        $('#accept_name,[data-group="idcard"]')[(readonly ? 'add' : 'remove') + 'Class']('disabled').attr('readonly', readonly);

    },
    //清空表单
    form_empty: function () {
        var form = $('#form-address');
        TH.Address.area_select('province', 0);
        form[0].reset();
        form.find('#address_no').val('');
        form.find('.txt').attr('readonly', false).removeClass('disabled f-error');
        form.find('select[name="city"],select[name="area"]').html('<option value="">请选择</option>');
        TH.Address.set_status({front_pic: '', back_pic: ''});
    }
}

$(function () {
    TH.Address.init();
});