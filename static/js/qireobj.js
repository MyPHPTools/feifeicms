pp = null;
function checkAll(objname) {
    objEvent = getEvent();
    if (objEvent.srcElement) id = objEvent.srcElement;
    else id = objEvent.target;
    if (objname != '') {
        var code_Values = document.getElementsByName(objname);
        for (i = 0; i < code_Values.length; i++) {
            if (code_Values[i].type == "checkbox") {
                code_Values[i].checked = id.checked;
            }
        }
    } else {
        var code_Values = document.getElementsByTagName("input");
        for (i = 0; i < code_Values.length; i++) {
            if (code_Values[i].type == "checkbox") {
                code_Values[i].checked = id.checked;
            }
        }
    }
}
$.extend({
    refresh: function(url) {
        window.location.href = url;
    }
});
function getEvent() {
    if (document.all) return window.event;
    func = getEvent.caller;
    while (func != null) {
        var arg0 = func.arguments[0];
        if (arg0) {
            if ((arg0.constructor == Event || arg0.constructor == MouseEvent) || (typeof(arg0) == "object" && arg0.preventDefault && arg0.stopPropagation)) {
                return arg0;
            }
        }
        func = func.caller;
    }
    return null;
}
jQuery.showfloatdiv = function(ox) {
    var oxdefaults = {
        txt: '数据加载中,请稍后...',
        classname: 'progressBar',
        left: 410,
        top: 210,
        wantclose: 1,
        suredo: function(e) {
            return false;
        },
        succdo: function(r) {},
        completetxt: '操作成功!',
        autoclose: 1,
        ispost: 0,
        cssname: 'alert',
        isajax: 0,
        intvaltime: 1000,
        redirurl: '/'
    };
    ox = ox || {};
    $.extend(oxdefaults, ox);
    $("#qirebox_overlay").remove();
    $("#qirebox").remove();
    if (oxdefaults.wantclose == 1) {
        var floatdiv = $('<div class="qirebox-overlayBG" id="qirebox_overlay"></div><div id="qirebox" class="qirebox png-img"><iframe frameborder="0" class="ui-iframe"></iframe><table class="ui-dialog-box"><tr><td><div class="ui-dialog"><div class="ui-dialog-cnt" id="ui-dialog-cnt"><div class="ui-dialog-tip alert" id="ui-cnt"><span id="xtip">' + oxdefaults.txt + '</span></div></div><div class="ui-dialog-close"><span class="close">关闭</span></div></div></td></tr></table></div>');
        $("body").append(floatdiv);
        $("#qirebox_overlay").fadeIn(500);
        $("#qirebox").fadeIn(500);
        $("#ui-cnt").removeClass('succ error alert loading').addClass(oxdefaults.cssname);
        $(".ui-dialog-close").click(function() {
            $.closefloatdiv();
        });
        if (oxdefaults.isajax == 1) {
            objEvent = getEvent();
            if (objEvent.srcElement) id = objEvent.srcElement;
            else id = objEvent.target;
            var idval = (id.attributes["data"].nodeValue != null && id.attributes["data"].nodeValue != undefined) ? id.attributes["data"].nodeValue: id.data;
            $.ajax({
                url: idval,
                async: true,
                type: 'get',
                cache: true,
                dataType: 'json',
                success: function(data, textStatus) {
                    if (data.msg != null && data.msg != undefined) {
                        $("#xtip").html(data.msg);
                    } else {
                        $("#xtip").html(oxdefaults.completetxt);
                    }
                    oxdefaults.succdo(data);
                    if (data.wantclose != null && data.wantclose != undefined) {
                        $.hidediv(data);
                    } else if (oxdefaults.autoclose == 1) {
                        $.hidediv(data);
                    }
                    if (data.wantredir != undefined || data.wantredir != null) {
                        if (data.redir != undefined || data.redir != null) {
                            setTimeout("$.refresh('" + data.redir + "')", oxdefaults.intvaltime);
                        } else {
                            setTimeout("$.refresh('" + oxdefaults.redirurl + "')", oxdefaults.intvaltime);
                        }
                    }
                },
                error: function(e) {
                    $("#xtip").html('系统繁忙,请稍后再试...');
                }
            });
        }
    } else if (oxdefaults.wantclose == 2) {
        objEvent = getEvent();
        if (objEvent.srcElement) id = objEvent.srcElement;
        else id = objEvent.target;
        var idval = (id.attributes["data"].nodeValue != null && id.attributes["data"].nodeValue != undefined) ? id.attributes["data"].nodeValue: id.data;
        var floatdiv = $('<div class="qirebox-overlayBG" id="qirebox_overlay"></div><div id="qirebox" class="qirebox png-img"><iframe frameborder="0" class="ui-iframe"></iframe><table class="ui-dialog-box"><tr><td><div class="ui-dialog"><div class="ui-dialog-cnt" id="ui-dialog-cnt"><div class="ui-dialog-tip alert" id="ui-cnt"><span id="xtip">' + oxdefaults.txt + '</span></div></div><div class="ui-dialog-todo"><a class="ui-link ui-link-small" href="javascript:void(0);" id="surebt">确定</a><a class="ui-link ui-link-small cancelbt"  id="cancelbt">取消</a><input type="hidden" id="hideval" value=""/></div><div class="ui-dialog-close"><span class="close">关闭</span></div></div></td></tr></table></div>');
        $("body").append(floatdiv);
        $("#qirebox_overlay").fadeIn(500);
        $("#qirebox").fadeIn(500);
        $(".ui-dialog-close").click(function() {
            $.closefloatdiv();
        });
        $(".cancelbt").click(function() {
            $.closefloatdiv();
        });
        $("#surebt").click(function(e) {
            if (!oxdefaults.suredo(e)) {
                $(".ui-dialog-todo").remove();
                $("#ui-cnt").removeClass('succ error alert').addClass('loading');
                if (oxdefaults.ispost == 0) {
                    $.ajax({
                        url: idval,
                        async: true,
                        type: 'get',
                        cache: true,
                        dataType: 'json',
                        success: function(data, textStatus) {
                            if (data.msg != null && data.msg != undefined) {
                                $("#xtip").html(data.msg);
                            } else {
                                $("#xtip").html(oxdefaults.completetxt);
                            }
                            oxdefaults.succdo(data);
                            if (data.wantclose != null && data.wantclose != undefined) {
                                $.hidediv(data);
                            } else if (oxdefaults.autoclose == 1) {
                                $.hidediv(data);
                            }
                        },
                        error: function(e) {
                            $("#xtip").html('系统繁忙,请稍后再试...');
                        }
                    });
                } else {
                    $("#" + oxdefaults.formid).qiresub({
                        curobj: $("#surebt"),
                        txt: '数据提交中,请稍后...',
                        onsucc: function(result) {
                            oxdefaults.succdo(result);
                            $.hidediv(result);
                        }
                    }).post({
                        url: oxdefaults.url
                    });
                }
            } else {
                oxdefaults.succdo(e);
            }
        });
    } else {
        var floatdiv = $('<div class="qirebox_overlayBG" id="qirebox_overlay"></div><div id="qirebox" class="qirebox"><iframe frameborder="0" class="ui-iframe"></iframe><div class="ui-dialog"><div class="ui-dialog-cnt" id="ui-dialog-cnt"><div class="ui-dialog-box"<div class="ui-cnt" id="ui-cnt">' + oxdefaults.txt + '</div></div></div></div></div>');
        $("body").append(floatdiv);
        $("#qirebox_overlay").fadeIn(500);
        $("#qirebox").fadeIn(500);
    }
    $('#qirebox_overlay').bind('click', 
    function(e) {
        $.closefloatdiv(e);
        if (pp != null) {
            clearTimeout(pp);
        }
    });
};
jQuery.closefloatdiv = function(e) {
    $("#qirebox_overlay").remove();
    $("#qirebox").remove();
};
jQuery.hidediv = function(e) {
    var oxdefaults = {
        intvaltime: 1000
    };
    e = e || {};
    $.extend(oxdefaults, e);
    if (e.msg != null && e.msg != undefined) {
        $("#ui-cnt").html(e.msg);
    }
    if (parseInt(e.rcode) == 1) {
        $("#ui-cnt").removeClass('loading error alert').addClass('succ');
    } else if (parseInt(e.rcode) < 1) {
        $("#ui-cnt").removeClass('loading alert succ').addClass('error');
    }
    pp = setTimeout("$.closefloatdiv()", oxdefaults.intvaltime);
}; (function($) {
    $.fn.qiresub = function(options) {
        var defaults = {
            txt: '数据提交中,请稍后...',
            redirurl: window.location.href,
            dataType: 'json',
            onsucc: function(e) {},
            onerr: function() {
                $.hidediv({
                    msg: '系统繁忙'
                });
            },
            oncomplete: function() {},
            intvaltime: 1000
        };
        options.curobj.attr('disabled', true);
        var ox = options.curobj.offset();
        var options = $.extend(defaults, options);
        $.showfloatdiv({
            offset: ox,
            txt: defaults.txt
        });
        var obj = $(this);
        var id = obj.attr('id');
        return {
            post: function(e) {
                $("#ui-cnt").removeClass('succ error alert').addClass('loading');
                $.post(e.url, obj.serializeArray(), 
                function(result) {
                    options.curobj.attr('disabled', false);
                    defaults.onsucc(result);
                    if (result.closediv != undefined || result.closediv != null) {
                        $.closefloatdiv();
                    }
                    if (result.wantredir != undefined || result.wantredir != null) {
                        if (result.redir != undefined || result.redir != null) {
                            setTimeout("$.refresh('" + result.redir + "')", options.intvaltime);
                        } else {
                            setTimeout("$.refresh('" + options.redirurl + "')", options.intvaltime);
                        }
                    }
                },
                options.dataType).error(function() {
                    options.curobj.attr('disabled', false);
                    defaults.onerr();
                }).complete(function() {
                    defaults.oncomplete();
                    options.curobj.attr('disabled', false);
                });
            },
            implodeval: function(e) {
                val = $("#" + id + " :input").map(function() {
                    if ($(this).attr('name') != '' && $(this).attr('name') != undefined) {
                        return $(this).attr('name') + "-" + $(this).val();
                    }
                }).get().join("-");
                return val;
            },
            get: function(e) {
                $(".ui-dialog-todo").remove();
                $("#ui-cnt").removeClass('succ error alert').addClass('loading');
                var val = this.implodeval();
                $.get(e.url + "-" + val, '', 
                function(result) {
                    options.curobj.attr('disabled', false);
                    defaults.onsucc(result);
                    if (result.wantredir != undefined || result.wantredir != null) {
                        if (result.redir != undefined || result.redir != null) {
                            setTimeout("$.refresh(" + result.redir + ")", options.intvaltime);
                        } else {
                            setTimeout("$.refresh(" + options.redirurl + ")", options.intvaltime);
                        }
                    }
                },
                options.dataType).error(function() {
                    options.curobj.attr('disabled', false);
                    defaults.onerr();
                }).complete(function() {
                    defaults.oncomplete();
                    options.curobj.attr('disabled', false);
                });
            }
        };
    };
    $.fn.ajaxdel = function(options) {
        var defaults = {
            txt: '数据提交中,请稍后...',
            redirurl: window.location.href,
            dataType: 'json',
            onsucc: function(e) {},
            onerr: function() {},
            oncomplete: function() {},
            intvaltime: 3000
        };
        $(".ui-dialog-todo").remove();
        $("#ui-cnt").removeClass('succ error alert').addClass('loading');
        var options = $.extend(defaults, options);
        var ajurl = $(this).attr('url');
        $.ajax({
            url: ajurl,
            success: function(data) {
                options.onsucc(data);
            },
            error: function() {
                options.onerr();
            },
            complete: function() {
                options.oncomplete();
            },
            dataType: 'json'
        });
    };
})(jQuery);
var qruser;
var loginhtml;
function qireuser() {};
qireuser.prototype.urls = new Array(syndomain + '/user-Center-flushinfo', syndomain + '/user-Center-getremindnew');
if (window.location.href.indexOf('Center') > 0) {
    qireuser.prototype.parms = new Array('', 'intime-1');
} else {
    qireuser.prototype.parms = new Array('inhome-1', 'intime-1');
}
qireuser.prototype.gu = function(e) {
    $.get(this.urls[0] + '-' + this.parms[0], '', 
    function(result) {
        if (parseInt(result.rcode) == -7) {
            $.showfloatdiv({
                txt: result.msg,
                classname: 'error'
            });
            $.hidediv({
                rcode: -1,
                msg: result.msg
            });
            return false;
        }
        if (result[0] > 0) {
            qruser = result;
            if (window.location.href.indexOf('Center') > 0) {
                $("#" + e.ubox).html(result[1]);
                $("#" + e.h3).html(result[1]);
                $("#" + e.logo).attr('src', result[13]);
                $("#ubox").html(result[14]);
                $(".logoutbt").click(function(e) {
                    $.showfloatdiv({
                        txt: '正在退出...',
                        cssname: 'loading',
                        isajax: 1
                    });
                    return false;
                });
            } else {
                if (parseInt(result[9]) > 5) {
                    $("#morelog").html('<a target="_blank" href="/user-Center-playlog">进入我的奇热查看完整播放记录&gt;&gt;</a>');
                    $("#morelog").show();
                } else {
                    $("#morelog").html('');
                    $("#morelog").hide();
                }
                loginhtml = $("#loginbarx").html();
                $("#loginbarx").html(result[14]);
                $("#loginbar").hide();
                $(".logoutbt").unbind();
                $(".logoutbt").click(function(e) {
                    $.showfloatdiv({
                        txt: '正在退出...',
                        cssname: 'loading',
                        isajax: 1,
                        succdo: function(r) {
                            clearcookie();
                            try {
                                PlayHistoryObj.viewPlayHistory('playhistory');
                            } catch(e) {}
                            r.wantredir = null;
                            $("#loged").html('');
                            $("#innermsg").html('登录');
                            $("#loginbar").show();
                            $("#loginbarx").html(loginhtml);
                            $("#loginform #loginbt").click(function(e) {
                                try {
                                    userlogin();
                                } catch(e) {}
                            });
                        }
                    });
                    return false;
                });
            }
        }
        if (parseInt(result[11])) {
            $("#" + e.rbox).html('我的留言<strong>(' + result[11] + ')</strong>');
        }
        if (window.location.href.match(/s=(.*)/g) == 's=user-Center') {
            $("#commnum a").html(result[5]);
            $("#lovenum a").html(result[6]);
            $("#remindnum a").html(result[10]);
            $("#playlognum a").html(result[9]);
            $("#reviewnum a").html(result[7]);
            $("#gbnum a").html(result[11]);
        }
    },
    'jsonp');
};
qireuser.prototype.gr = function() {
    $.get(this.urls[1] + '-' + this.parms[1], '', 
    function(result) {
        this.r = result;
        if (parseInt(result[0])) {
            $("#" + e.rbox).html('订阅更新<strong>(' + result[0] + ')</strong>');
        }
    },
    'jsonp');
}
qireuser.prototype.ucenter = function() {}
var qr = new qireuser();
if (checkcookie()) {
    qr.gu({
        ubox: 'unm',
        rbox: 'innermsg',
        h3: 'h3',
        logo: 'userlogo'
    });
}
function gqq() {
    qr.ucenter();
}
Date.prototype.pattern = function(fmt) {
    var o = {
        "M+": this.getMonth() + 1,
        "d+": this.getDate(),
        "h+": this.getHours() == 0 ? 12: this.getHours(),
        "H+": this.getHours(),
        "m+": this.getMinutes(),
        "s+": this.getSeconds(),
        "q+": Math.floor((this.getMonth() + 3) / 3),
        "S": this.getMilliseconds()
    };
    var week = {
        "0": "\u65e5",
        "1": "\u4e00",
        "2": "\u4e8c",
        "3": "\u4e09",
        "4": "\u56db",
        "5": "\u4e94",
        "6": "\u516d"
    };
    if (/(y+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    if (/(E+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, ((RegExp.$1.length > 1) ? (RegExp.$1.length > 2 ? "\u661f\u671f": "\u5468") : "") + week[this.getDay() + ""]);
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        }
    }
    return fmt;
}
function timetodate(tim, dat) {
    return new Date(parseInt(tim) * 1000).pattern(dat);
}
function clearcookie() {
    document.cookie = "qr_u=; path=/;domain=qire123.com;expires=" + (new Date(1970, 1, 1)).toGMTString();
    document.cookie = "qr_uu=; path=/;domain=qire123.com;expires=" + (new Date(1970, 1, 1)).toGMTString();
}