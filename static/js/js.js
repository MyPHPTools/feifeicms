var FF = {
    'Home': {
        'Url': document.URL,
        'Tpl': 'defalut',
        'Channel': '',
        'GetChannel': function($sid) {
            if ($sid == '1') return 'vod';
            if ($sid == '2') return 'news';
            if ($sid == '3') return 'special';
        },
		'Js': function() {
			//获取频道名
			this.Channel = this.GetChannel(Sid);
			//搜索默认关键字
			if($("#wd").length>0){ 
				//改变action目标地址
				if(Sid == '2'){
					$key = '输入关键字';
					$('#ffsearch').attr('action', Root+'index.php?s=news-search');
				}else{
					$key = '输入影片名称或主演名称';
				}
				//默认搜索框关键字
				if($('#wd').val() == ''){
					$('#wd').val($key);
				}
				//搜索框获得焦点
				$('#wd').focus(function(){
					if($('#wd').val() == $key){
						$('#wd').val('');
					}
				});
				//搜索框失去焦点
				$('#wd').blur(function(){
					if($('#wd').val() == ''){
						$('#wd').val($key);
					}
				});
			}
            $("#fav").click(function() {
                var url = window.location.href;
                try {
                    window.external.addFavorite(url, document.title);
                } catch(err) {
                    try {
                        window.sidebar.addPanel(document.title, url, "");
                    } catch(err) {
                        alert("请使用Ctrl+D为您的浏览器添加书签！");
                    }
                }
            });
			$("img.lazy").error(function(){
				$(this).attr('src',Root +'Tpls/PPTV/nophoto.jpg');
			});
			$("img.bigimg").error(function(){
				$(this).attr('src',Root +'Tpls/PPTV/nophoto2.jpg');
			});
            $('.img_txt img,.pic_list_comm .pic_list img').hover(function() {
                $(this).css('border-color','#333333');
			},
            function() {
                $(this).css('border-color','#dddddd');
            });
        }
    },
    //
    'UpDown': {
        'Vod': function($ajaxurl) {
            if ($("#Up").length || $("#Down").length) {
                this.Ajax($ajaxurl, 'vod', '');
            }
            $('.Up').click(function() {
                FF.UpDown.Ajax($ajaxurl, 'vod', 'up');
            });
            $('.Down').click(function() {
                FF.UpDown.Ajax($ajaxurl, 'vod', 'down');
            });
        },
        'News': function($ajaxurl) {
            if ($("#Digup").length || $("#Digdown").length) {
                this.Ajax($ajaxurl, 'news', '');
            } else {
                FF.UpDown.Show($("#Digup_val").html() + ':' + $("#Digdown_val").html(), 'news');
            }
            $('.Digup').click(function() {
                FF.UpDown.Ajax($ajaxurl, 'news', 'up');
            });
            $('.Digdown').click(function() {
                FF.UpDown.Ajax($ajaxurl, 'news', 'down');
            });
        },
        'Ajax': function($ajaxurl, $model, $ajaxtype) {
            $.ajax({
                type: 'get',
                url: $ajaxurl + '-type-' + $ajaxtype,
                timeout: 5000,
                dataType: 'json',
                success: function($html) {
                    if (!$html.status) {
                        alert($html.info);
                    } else {
                        FF.UpDown.Show($html.data, $model);
                    }
                }
            });
        },
        'Show': function($html, $model) {
            if ($model == 'vod') {
                $(".Up>span").html($html.split(':')[0]);
                $(".Down>span").html($html.split(':')[1]);
            } else if ($model = 'news') {
                var Digs = $html.split(':');
                var sUp = parseInt(Digs[0]);
                var sDown = parseInt(Digs[1]);
                var sTotal = sUp + sDown;
                var spUp = (sUp / sTotal) * 100;
                spUp = Math.round(spUp * 10) / 10;
                var spDown = 100 - spUp;
                spDown = Math.round(spDown * 10) / 10;
                if (sTotal != 0) {
                    $('#Digup_val').html(sUp);
                    $('#Digdown_val').html(sDown);
                    $('#Digup_sp').html(spUp + '%');
                    $('#Digdown_sp').html(spDown + '%');
                    $('#Digup_img').width(parseInt((sUp / sTotal) * 55));
                    $('#Digdown_img').width(parseInt((sDown / sTotal) * 55));
                }
            }
        }
    },
	
    'Lazyload': {
        'Show': function() {
            $("img.lazy").lazyload();
        }
    },
    'Playlist': {
        'Show': function() {
            var $title = $("#playlistit a");
            var $content = $("#playlist .playlist");
            $title.mousemove(function() {
                var index = $title.index($(this));
                $(this).addClass("on").siblings().removeClass("on");
                $content.hide();
                $($content.get(index)).show();
                return false;
            });
        }
    },
//搜索联想 FF.Suggest.Show('wd',10,'index.php?s=plus-search-index','index.php?s=vod-search-wd-');
	'Suggest': {
		'Show': function($id,$limit,$ajaxurl,$jumpurl){
			$("#"+$id).autocomplete($ajaxurl, {
				width: 462,
				scrollHeight: 320,
				minChars: 1,
				matchSubset: 1,
				max: $limit,
				cacheLength: 10,
				multiple: true,
				matchContains: true,
				autoFill: false,
				dataType: "json",
				parse:function(obj) {//解释返回的数据，把其存在数组里，返回给要输出的item
					if(obj.status){
						var parsed = [];
						for (var i = 0; i < obj.data.length; i++) {   
							parsed[i] = {
								data: obj.data[i],
								value: obj.data[i].vod_name,
								result: obj.data[i].vod_name//返回的结果显示内容
							};
						}
						return parsed;
					}else{
						return {data:'',value:'',result:''};
					}
				},
				formatItem: function(row,i,max) {
					return row.vod_name;
				},
				formatResult: function(row,i,max) {
					return row.vod_name;//replace(/(<.+?>)/gi, '')
				}
			}).result(function(event, data, formatted) {
				location.href = $jumpurl+encodeURIComponent(data.vod_name);
			});	
		}
	},
    'Cookie': {
        'Set': function(name, value, days) {
            var exp = new Date();
            exp.setTime(exp.getTime() + days * 24 * 60 * 60 * 1000);
            var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
            document.cookie = name + "=" + escape(value) + ";path=/;expires=" + exp.toUTCString();
        },
        'Get': function(name) {
            var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
            if (arr != null) {
                return unescape(arr[2]);
                return null;
            }
        },
        'Del': function(name) {
            var exp = new Date();
            exp.setTime(exp.getTime() - 1);
            var cval = this.Get(name);
            if (cval != null) {
                document.cookie = name + "=" + escape(cval) + ";path=/;expires=" + exp.toUTCString();
            }
        }
    },
    'History': {
        'Json': '',
        'Display': true,
        'List': function($id) {
            this.Create($id);
            $('#' + $id).hover(function() {
                FF.History.Show();
            },
            function() {
                FF.History.FlagHide();
            });
        },

        'Create': function($id) {
            var jsondata = [];
            if (this.Json) {
                jsondata = this.Json;
            } else {
                var jsonstr = FF.Cookie.Get('FF_Cookie');
                if (jsonstr != undefined) {
                    jsondata = eval(jsonstr);
                }
            };
            html = '<table class="history_list sr0" id="hishow" cellpadding="0" cellspacing="0">';
            html += '<thead><tr><th colspan="2"><b id="clearall"><a href="javascript:void(0)" onclick="FF.History.Clear();">清空</a></b></th></tr></thead>';
            if (jsondata.length > 0) {
                html += '<tbody>';
                for ($i = 0; $i < jsondata.length; $i++) {
                    html += '<tr class="r' + [$i] + ' hisitem"><td class="op"><b class="del"></b></td><td class="tt"><a href="' + jsondata[$i].vodlink + '" title="' + jsondata[$i].vodname + '">' + jsondata[$i].vodname + '</a></td></tr>';
                }
                html += '</tbody>';
            } else {
                html += '<tfoot><tr class="emptied"><th colspan="2">暂无浏览记录</th></tr></tfoot>';
            };
			html += '</table>';
            $('#history_layer').html(html);
        },
        'Insert': function(vodname, vodlink, limit, days, cidname, vodpic) {
            var jsondata = FF.Cookie.Get('FF_Cookie');
            if (jsondata != undefined) {
                this.Json = eval(jsondata);
                for ($i = 0; $i < this.Json.length; $i++) {
                    if (this.Json[$i].vodlink == vodlink) {
                        return false;
                    }
                };
                if (!vodlink) {
                    vodlink = document.URL;
                }
                jsonstr = '{video:[{"vodname":"' + vodname + '","vodlink":"' + vodlink + '","cidname":"' + cidname + '","vodpic":"' + vodpic + '"},';
                for ($i = 0; $i <= limit; $i++) {
                    if (this.Json[$i]) {
                        jsonstr += '{"vodname":"' + this.Json[$i].vodname + '","vodlink":"' + this.Json[$i].vodlink + '","cidname":"' + this.Json[$i].cidname + $i + '","vodpic":"' + this.Json[$i].vodpic + '"},';
                    } else {
                        break;
                    }
                };
                jsonstr = jsonstr.substring(0, jsonstr.lastIndexOf(','));
                jsonstr += "]}";
            } else {
                jsonstr = '{video:[{"vodname":"' + vodname + '","vodlink":"' + vodlink + '","cidname":"' + cidname + '","vodpic":"' + vodpic + '"}]}';
            };
            this.Json = eval(jsonstr);
            FF.Cookie.Set('FF_Cookie', jsonstr, days);
        }
    },
	'Comment': {
		'Default': function($ajaxurl) {
			if($("#Comment").length>0){
				FF.Comment.Show($ajaxurl);
			}
		},
		'Show': function($ajaxurl) {
			$.ajax({
				type: 'get',
				url: $ajaxurl,
				timeout: 5000,
				error: function(){
					$("#Comment").html('评论加载失败...');
				},
				success:function($html){	
					$("#Comment").html($html);
				}
			});
		},
		'Post':function CommentPost(){
			if($("#comment_content").val() == '请在这里发表您的个人看法，最多200个字。'){
				$('#comment_tips').html('请发表您的评论观点！');
				return false;
			}
			var $data = "cm_sid="+Sid+"&cm_cid="+Id+"&cm_content="+$("#comment_content").val();
			$.ajax({
				type: 'post',
				url: Root+'index.php?s=Cm-insert',
				data: $data,
				dataType:'json',
				success:function($string){
					if($string.status == 1){
						FF.Comment.Show(Root+"index.php?s=Cm-Show-sid-"+Sid+"-id-"+Id+"-p-1");
					}
					alert($string.info);
				}
			});
		}
	}
}
$(document).ready(function() {
	FF.Home.Js();
	FF.Lazyload.Show();
	FF.Playlist.Show();
	FF.Suggest.Show('wd', 12, Root + 'index.php?s=plus-search-vod', Root + 'index.php?s=vod-search-wd-');
	FF.History.List('history');
	FF.UpDown.Vod(Root + 'index.php?s=Updown-' + FF.Home.Channel + '-id-' + Id);
	FF.UpDown.News(Root + 'index.php?s=Updown-' + FF.Home.Channel + '-id-' + Id);
	FF.Comment.Default(Root+"index.php?s=Cm-Show-sid-"+Sid+"-id-"+Id+"-p-1");
});
function SetHome(obj, vrl) {
    try {
        obj.style.behavior = 'url(#default#homepage)';
        obj.setHomePage(vrl);
    }
    catch(e) {
        if (window.netscape) {
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            }
            catch(e) {
                alert("温馨提示:\n浏览器不允许网页设置首页。\n请手动进入浏览器选项设置主页。");
            }
            var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
            prefs.setCharPref('browser.startup.homepage', vrl);
        }
    }
}

$(document).ready(function(){
	//系统初始化
	if($("#yybox").length > 0)
	{
		if($("#yybox")[0].scrollHeight>28)
		{
			$("#yymoerbtn").show();
			$("#yybox").width(305);
			$("#yymoerbtn").click(function(e){
				if($("#yybox").height()>28)
				{
					var mh = $("#movtxtbox")[0].scrollHeight-10;
					if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/7./i)=="7.")
					{
						mh = mh+11;
					}else if ($.browser.version == "6.0"){
						mh = mh+1;
					}
					var h = $("#yybox")[0].scrollHeight;
					$("#yybox").height(28);
					$("#movtxtbox").height(mh-h+28);
					
				}
				else
				{
					var mh = $("#movtxtbox")[0].scrollHeight-10; 
					var h = $("#yybox")[0].scrollHeight;
					$("#yybox").height(h);
					$("#movtxtbox").height(mh+(h-28));
				}
				e.preventDefault(); 
			});
		}
	}
	if($("#yyboxs").length > 0)
	{
		if($("#yyboxs")[0].scrollHeight>35)
		{
			$("#yymoerbtns").show();
			$("#yyboxs").width(740);
			$("#yymoerbtns").click(function(e){
				if($("#yyboxs").height()>35)
				{
					var mh = $("#movtxtboxs")[0].scrollHeight-10;
					if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/7./i)=="7.")
					{
						mh = mh+11;
					}else if ($.browser.version == "6.0"){
						mh = mh+1;
					}
					var h = $("#yyboxs")[0].scrollHeight;
					$("#yyboxs").height(35);
					$("#movtxtboxs").height(mh-h+35);
					
				}
				else
				{
					var mh = $("#movtxtboxs")[0].scrollHeight-10; 
					var h = $("#yyboxs")[0].scrollHeight;
					$("#yyboxs").height(h);
					$("#movtxtboxs").height(mh+(h-35));
				}
				e.preventDefault(); 
			});
		}
	}
});

(function(a){a.fn.slide=function(b){return a.fn.slide.defaults={effect:"fade",autoPlay:!1,delayTime:500,interTime:2500,triggerTime:150,defaultIndex:0,titCell:".hd li",mainCell:".bd",targetCell:null,trigger:"mouseover",scroll:1,vis:1,titOnClassName:"on",autoPage:!1,prevCell:".prev",nextCell:".next",pageStateCell:".pageState",opp:!1,pnLoop:!0,easing:"linear",startFun:null,endFun:null,switchLoad:null},this.each(function(){var c=a.extend({},a.fn.slide.defaults,b),d=c.effect,e=a(c.prevCell,a(this)),f=a(c.nextCell,a(this)),g=a(c.pageStateCell,a(this)),h=a(c.titCell,a(this)),i=h.size(),j=a(c.mainCell,a(this)),k=j.children().size(),l=c.switchLoad;if(null!=c.targetCell)var m=a(c.targetCell,a(this));var n=parseInt(c.defaultIndex),o=parseInt(c.delayTime),p=parseInt(c.interTime);parseInt(c.triggerTime);var r=parseInt(c.scroll),s=parseInt(c.vis),t="false"==c.autoPlay||0==c.autoPlay?!1:!0,u="false"==c.opp||0==c.opp?!1:!0,v="false"==c.autoPage||0==c.autoPage?!1:!0,w="false"==c.pnLoop||0==c.pnLoop?!1:!0,x=0,y=0,z=0,A=0,B=c.easing,C=null,D=n;if(0==i&&(i=k),v){var E=k-s;i=1+parseInt(0!=E%r?E/r+1:E/r),0>=i&&(i=1),h.html("");for(var F=0;i>F;F++)h.append("<li>"+(F+1)+"</li>");var h=a("li",h)}if(j.children().each(function(){a(this).width()>z&&(z=a(this).width(),y=a(this).outerWidth(!0)),a(this).height()>A&&(A=a(this).height(),x=a(this).outerHeight(!0))}),k>=s)switch(d){case"fold":j.css({position:"relative",width:y,height:x}).children().css({position:"absolute",width:z,left:0,top:0,display:"none"});break;case"top":j.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; height:'+s*x+'px"></div>').css({position:"relative",padding:"0",margin:"0"}).children().css({height:A});break;case"left":j.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; width:'+s*y+'px"></div>').css({width:k*y,position:"relative",overflow:"hidden",padding:"0",margin:"0"}).children().css({"float":"left",width:z});break;case"leftLoop":case"leftMarquee":j.children().clone().appendTo(j).clone().prependTo(j),j.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; width:'+s*y+'px"></div>').css({width:3*k*y,position:"relative",overflow:"hidden",padding:"0",margin:"0",left:-k*y}).children().css({"float":"left",width:z});break;case"topLoop":case"topMarquee":j.children().clone().appendTo(j).clone().prependTo(j),j.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; height:'+s*x+'px"></div>').css({height:3*k*x,position:"relative",padding:"0",margin:"0",top:-k*x}).children().css({height:A})}var G=function(){a.isFunction(c.startFun)&&c.startFun(n,i)},H=function(){a.isFunction(c.endFun)&&c.endFun(n,i)},I=function(b){b.eq(n).find("img").each(function(){a(this).attr(l)!==void 0&&a(this).attr("src",a(this).attr(l)).removeAttr(l)})},J=function(a){if(D!=n||a||"leftMarquee"==d||"topMarquee"==d){switch(d){case"fade":case"fold":case"top":case"left":n>=i?n=0:0>n&&(n=i-1);break;case"leftMarquee":case"topMarquee":n>=1?n=1:0>=n&&(n=0);break;case"leftLoop":case"topLoop":var b=n-D;i>2&&b==-(i-1)&&(b=1),i>2&&b==i-1&&(b=-1);var p=Math.abs(b*r);n>=i?n=0:0>n&&(n=i-1)}if(G(),null!=l&&I(j.children()),m&&(null!=l&&I(m),m.hide().eq(n).animate({opacity:"show"},o,function(){j[0]||H()})),k>=s)switch(d){case"fade":j.children().stop(!0,!0).eq(n).animate({opacity:"show"},o,B,function(){H()}).siblings().hide();break;case"fold":j.children().stop(!0,!0).eq(n).animate({opacity:"show"},o,B,function(){H()}).siblings().animate({opacity:"hide"},o,B);break;case"top":j.stop(!0,!1).animate({top:-n*r*x},o,B,function(){H()});break;case"left":j.stop(!0,!1).animate({left:-n*r*y},o,B,function(){H()});break;case"leftLoop":0>b?j.stop(!0,!0).animate({left:-(k-p)*y},o,B,function(){for(var a=0;p>a;a++)j.children().last().prependTo(j);j.css("left",-k*y),H()}):j.stop(!0,!0).animate({left:-(k+p)*y},o,B,function(){for(var a=0;p>a;a++)j.children().first().appendTo(j);j.css("left",-k*y),H()});break;case"topLoop":0>b?j.stop(!0,!0).animate({top:-(k-p)*x},o,B,function(){for(var a=0;p>a;a++)j.children().last().prependTo(j);j.css("top",-k*x),H()}):j.stop(!0,!0).animate({top:-(k+p)*x},o,B,function(){for(var a=0;p>a;a++)j.children().first().appendTo(j);j.css("top",-k*x),H()});break;case"leftMarquee":var q=j.css("left").replace("px","");0==n?j.animate({left:++q},0,function(){if(j.css("left").replace("px","")>=0){for(var a=0;k>a;a++)j.children().last().prependTo(j);j.css("left",-k*y)}}):j.animate({left:--q},0,function(){if(2*-k*y>=j.css("left").replace("px","")){for(var a=0;k>a;a++)j.children().first().appendTo(j);j.css("left",-k*y)}});break;case"topMarquee":var t=j.css("top").replace("px","");0==n?j.animate({top:++t},0,function(){if(j.css("top").replace("px","")>=0){for(var a=0;k>a;a++)j.children().last().prependTo(j);j.css("top",-k*x)}}):j.animate({top:--t},0,function(){if(2*-k*x>=j.css("top").replace("px","")){for(var a=0;k>a;a++)j.children().first().appendTo(j);j.css("top",-k*x)}})}h.removeClass(c.titOnClassName).eq(n).addClass(c.titOnClassName),D=n,0==w&&(f.removeClass("nextStop"),e.removeClass("prevStop"),0==n?e.addClass("prevStop"):n==i-1&&f.addClass("nextStop")),g.html("<span>"+(n+1)+"</span>/"+i)}};J(!0),t&&("leftMarquee"==d||"topMarquee"==d?(u?n--:n++,C=setInterval(J,p),j.hover(function(){t&&clearInterval(C)},function(){t&&(clearInterval(C),C=setInterval(J,p))})):(C=setInterval(function(){u?n--:n++,J()},p),a(this).hover(function(){t&&clearInterval(C)},function(){t&&(clearInterval(C),C=setInterval(function(){u?n--:n++,J()},p))})));var K;"mouseover"==c.trigger?h.hover(function(){n=h.index(this),K=window.setTimeout(J,c.triggerTime)},function(){clearTimeout(K)}):h.click(function(){n=h.index(this),J()}),f.click(function(){(1==w||n!=i-1)&&(n++,J())}),e.click(function(){(1==w||0!=n)&&(n--,J())})})}})(jQuery),jQuery.easing.jswing=jQuery.easing.swing,jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(a,b,c,d,e){return jQuery.easing[jQuery.easing.def](a,b,c,d,e)},easeInQuad:function(a,b,c,d,e){return d*(b/=e)*b+c},easeOutQuad:function(a,b,c,d,e){return-d*(b/=e)*(b-2)+c},easeInOutQuad:function(a,b,c,d,e){return 1>(b/=e/2)?d/2*b*b+c:-d/2*(--b*(b-2)-1)+c},easeInCubic:function(a,b,c,d,e){return d*(b/=e)*b*b+c},easeOutCubic:function(a,b,c,d,e){return d*((b=b/e-1)*b*b+1)+c},easeInOutCubic:function(a,b,c,d,e){return 1>(b/=e/2)?d/2*b*b*b+c:d/2*((b-=2)*b*b+2)+c},easeInQuart:function(a,b,c,d,e){return d*(b/=e)*b*b*b+c},easeOutQuart:function(a,b,c,d,e){return-d*((b=b/e-1)*b*b*b-1)+c},easeInOutQuart:function(a,b,c,d,e){return 1>(b/=e/2)?d/2*b*b*b*b+c:-d/2*((b-=2)*b*b*b-2)+c},easeInQuint:function(a,b,c,d,e){return d*(b/=e)*b*b*b*b+c},easeOutQuint:function(a,b,c,d,e){return d*((b=b/e-1)*b*b*b*b+1)+c},easeInOutQuint:function(a,b,c,d,e){return 1>(b/=e/2)?d/2*b*b*b*b*b+c:d/2*((b-=2)*b*b*b*b+2)+c},easeInSine:function(a,b,c,d,e){return-d*Math.cos(b/e*(Math.PI/2))+d+c},easeOutSine:function(a,b,c,d,e){return d*Math.sin(b/e*(Math.PI/2))+c},easeInOutSine:function(a,b,c,d,e){return-d/2*(Math.cos(Math.PI*b/e)-1)+c},easeInExpo:function(a,b,c,d,e){return 0==b?c:d*Math.pow(2,10*(b/e-1))+c},easeOutExpo:function(a,b,c,d,e){return b==e?c+d:d*(-Math.pow(2,-10*b/e)+1)+c},easeInOutExpo:function(a,b,c,d,e){return 0==b?c:b==e?c+d:1>(b/=e/2)?d/2*Math.pow(2,10*(b-1))+c:d/2*(-Math.pow(2,-10*--b)+2)+c},easeInCirc:function(a,b,c,d,e){return-d*(Math.sqrt(1-(b/=e)*b)-1)+c},easeOutCirc:function(a,b,c,d,e){return d*Math.sqrt(1-(b=b/e-1)*b)+c},easeInOutCirc:function(a,b,c,d,e){return 1>(b/=e/2)?-d/2*(Math.sqrt(1-b*b)-1)+c:d/2*(Math.sqrt(1-(b-=2)*b)+1)+c},easeInElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(1==(b/=e))return c+d;if(g||(g=.3*e),Math.abs(d)>h){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return-(h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g))+c},easeOutElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(1==(b/=e))return c+d;if(g||(g=.3*e),Math.abs(d)>h){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return h*Math.pow(2,-10*b)*Math.sin((b*e-f)*2*Math.PI/g)+d+c},easeInOutElastic:function(a,b,c,d,e){var f=1.70158,g=0,h=d;if(0==b)return c;if(2==(b/=e/2))return c+d;if(g||(g=e*.3*1.5),Math.abs(d)>h){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return 1>b?-.5*h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)+c:.5*h*Math.pow(2,-10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)+d+c},easeInBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),d*(b/=e)*b*((f+1)*b-f)+c},easeOutBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),d*((b=b/e-1)*b*((f+1)*b+f)+1)+c},easeInOutBack:function(a,b,c,d,e,f){return void 0==f&&(f=1.70158),1>(b/=e/2)?d/2*b*b*(((f*=1.525)+1)*b-f)+c:d/2*((b-=2)*b*(((f*=1.525)+1)*b+f)+2)+c},easeInBounce:function(a,b,c,d,e){return d-jQuery.easing.easeOutBounce(a,e-b,0,d,e)+c},easeOutBounce:function(a,b,c,d,e){return 1/2.75>(b/=e)?d*7.5625*b*b+c:2/2.75>b?d*(7.5625*(b-=1.5/2.75)*b+.75)+c:2.5/2.75>b?d*(7.5625*(b-=2.25/2.75)*b+.9375)+c:d*(7.5625*(b-=2.625/2.75)*b+.984375)+c},easeInOutBounce:function(a,b,c,d,e){return e/2>b?.5*jQuery.easing.easeInBounce(a,2*b,0,d,e)+c:.5*jQuery.easing.easeOutBounce(a,2*b-e,0,d,e)+.5*d+c}});

