/* @name: 飞飞PHP影视系统JS
 * @version: 2.0.beta4
 * @lasttime: 2012-01-16
 * 	FF.Home
		.Url;
		.Getchannel(sid);
		.Js();
	FF.UpDown
 		.Vod(ajaxurl);
		.News(ajaxurl);
		.Show(up=100:down=88,model);
	FF.Comment
		.Show(ajaxurl);
		.Post();
	FF.Gold
		.Default(ajaxurl);
		.Show(ajaxurl);
	FF.Lazyload
   		.Show();
   		.Box('idname');		
	FF.Suggest
   		.Show(id,limit,ajaxurl,jumpurl);
	FF.Cookie
 		.Set(name,value,days);
		.Get(name);
		.Del(name);
	FF.History
		.Insert(vodname,vodlink,limit,days,cidname,vodpic);
		.List(id);	
 */
var FF = {
	//初始化前端js
	'Home': {
		'Url': document.URL,
		'Tpl': 'defalut',
		'Channel': '',
		'GetChannel': function ($sid){
			if($sid == '1') return 'vod';
			if($sid == '2') return 'news';
			if($sid == '3') return 'special';
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
			//加入收藏夹
			$("#fav").click(function(){
				var url = window.location.href;					 
				try{
					window.external.addFavorite(url,document.title);
				}catch(err){
					try{
						window.sidebar.addPanel(document.title, url,"");
					}catch(err){
						alert("请使用Ctrl+D为您的浏览器添加书签！");
					}
				}
			});
		}
	},
	//监听顶踩操作事件
	'UpDown': {
		'Vod': function($ajaxurl) {
			if($("#Up").length || $("#Down").length){
				this.Ajax($ajaxurl,'vod','');
			}
			$('.Up').click(function(){					
				FF.UpDown.Ajax($ajaxurl,'vod','up');
			})
			$('.Down').click(function(){
				FF.UpDown.Ajax($ajaxurl,'vod','down');
			})
		},
		'News': function($ajaxurl) {
			if($("#Digup").length || $("#Digdown").length){
				this.Ajax($ajaxurl,'news','');
			}else{
				FF.UpDown.Show($("#Digup_val").html()+':'+$("#Digdown_val").html(),'news');
			}
			$('.Digup').click(function(){
				FF.UpDown.Ajax($ajaxurl,'news','up');
			})
			$('.Digdown').click(function(){					
				FF.UpDown.Ajax($ajaxurl,'news','down');
			})
		},		
		'Ajax': function($ajaxurl,$model,$ajaxtype){
			$.ajax({
				type: 'get',
				url: $ajaxurl+'-type-'+$ajaxtype,
				timeout: 5000,
				dataType:'json',
				success:function($html){
					if(!$html.status){
						alert($html.info);
					}else{
						FF.UpDown.Show($html.data,$model);
						//if($ajaxtype){alert($html.info);}
					}
				}
			});
		},
		'Show': function ($html,$model){
			if($model == 'vod'){
				$(".Up>span").html($html.split(':')[0]);
				$(".Down>span").html($html.split(':')[1]);
			}else if($model = 'news'){
				var Digs = $html.split(':');
				var sUp = parseInt(Digs[0]);
				var sDown = parseInt(Digs[1]);
				var sTotal = sUp+sDown;
				var spUp=(sUp/sTotal)*100;
				spUp = Math.round(spUp*10)/10;
				var spDown = 100-spUp;
				spDown = Math.round(spDown*10)/10;
				if(sTotal!=0){
					$('#Digup_val').html(sUp);
					$('#Digdown_val').html(sDown);
					$('#Digup_sp').html(spUp+'%');
					$('#Digdown_sp').html(spDown+'%');
					$('#Digup_img').width(parseInt((sUp/sTotal)*55));
					$('#Digdown_img').width(parseInt((sDown/sTotal)*55));
				}				
			}
		}
	},
	//监听评论操作事件
	'Comment': {
		'Show': function($ajaxurl) {
			if($("#Comment").length>0){
				$.ajax({
					type: 'get',
					url: $ajaxurl,
					timeout: 5000,
					error: function(){
						$("#Comment").html('评论加载失败，请刷新...');
					},
					success:function($html){
						$("#Comment").html($html);
					}
				});
			}
		},
		'Post': function(){
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
					$('#comment_tips').html($string.info);
				}
			});
		}
	},
	//监听评分事件
	'Gold': {
		'Default': function($ajaxurl){
			if($("#Gold").length>0 || $("#Goldnum").length>0){
				$.ajax({
					type: 'get',
					url: $ajaxurl,
					timeout: 5000,
					dataType:'json',
					error: function(){
						$(".Gold").html('评分加载失败');
					},
					success: function($html){
						FF.Gold.Show($ajaxurl,$html.data,'');
					}
				});			
			}else{
				if($(".Gold").length>0 || $(".Goldnum").length>0){
					FF.Gold.Show($ajaxurl,$(".Goldnum").html()+':'+$(".Golder").html(),'');
				}
			}
		},
		'Show': function($ajaxurl,$html,$status){
			//去除与创建title提示
			$(".Goldtitle").remove();
			$(".Gold").after('<span class="Goldtitle" style="width:'+$(".Gold").width()+'px"></span>');
			$(".Goldtitle").css({margin:'20px 0 0 -95px'});
			if($status == 'onclick'){
				$(".Goldtitle").html('评分成功！');
				$(".Goldtitle").show();
				$status = '';
			}
			//展示星级>评分>评分人
			$(".Gold").html(FF.Gold.List($html.split(':')[0]));
			$(".Goldnum").html($html.split(':')[0]);
			$(".Golder").html($html.split(':')[1]);
			//鼠标划过
			$(".Gold>span").mouseover(function(){
				$id = $(this).attr('id')*1;
				$(".Goldtitle").html(FF.Gold.Title($id*2));
				$(".Goldtitle").show();
				//刷新星级图标
				$bgurl = $(this).css('background-image');
				for(i=0;i<5;i++){
					if(i>$id){
						$(".Gold>#"+i+"").css({background:$bgurl+" 41px 0 repeat"});
					}else{
						$(".Gold>#"+i+"").css({background:$bgurl});
					}
				}
			});
			//鼠标移出
			$(".Gold>span").mouseout(function(){
				//去除title提示	
				$(".Goldtitle").hide();
				//刷新星级图标
				$score = $html.split(':')[0]*1/2;
				$id = $(this).attr('id')*1;
				$bgurl = $(this).css('background-image');
				for(i=0;i<5;i++){
					if(i<Math.round($score)){
						if(i == parseInt($score)){
							$(".Gold>#"+i+"").css({background:$bgurl+" 20px 0 repeat"});
						}else{
							$(".Gold>#"+i+"").css({background:$bgurl});
						}
					}else{
						$(".Gold>#"+i+"").css({background:$bgurl+" 41px 0 repeat"});
					}
				}
			});
			//鼠标点击
			$(".Gold>span").click(function(){
				$.ajax({
					type: 'get',
					url: $ajaxurl+'-type-'+(($(this).attr('id')*1+1)*2),
					timeout: 5000,
					dataType:'json',
					error: function(){
						$(".Goldtitle").html('评分失败!');
					},
					success: function($html){
						if(!$html.status){
							//alert($html.info);
							$(".Goldtitle").html($html.info);
							$(".Goldtitle").show();
						}else{
							FF.Gold.Show($ajaxurl,$html.data,'onclick');
						}
					}
				});
			});
		},
		//星级评分展示
		'List': function($score){
			var $html = '';
			$score = $score/2;
			for(var i = 0 ; i<5; i++){
				var classname = 'all';
				if(i < $score && i<Math.round($score)){
					if(i == parseInt($score)){
						classname = 'half';
					}
				}else{
					classname = 'none';
				}
				$html += '<span id="'+i+'" class="'+classname+'"></span>';// title="'+this.GoldTitle(i*2)+'"
			}
			return $html;
		},
		//提示信息
		'Title': function($score){//星级鼠标浮层提示信息
			var array_str = ['很差！','一般！','不错！','很好！','力荐！'];
			var $score = parseFloat($score);
			if($score < 2.0) return array_str[0];
			if($score>=2.0 && $score<4.0) return array_str[1];
			if($score>=4.0 && $score<6.0) return array_str[2];
			if($score>=6.0 && $score<8.0) return array_str[3];
			if($score>=8.0) return array_str[4];
		}
	},
	//图片延时加载 FF.Lazyload.Box('frame'); <img class="lazy" data-original="{$ppvod.vod_picurl}" src="/images/blank.gif" alt="xx" />
	'Lazyload':{
		'Show': function(){
			$("img.lazy").lazyload({effect : "fadeIn"});
		},
		//指定ID范围内的效果
		'Box': function($id){
			$("img.lazy").lazyload({         
				 container: $("#"+$id)
			});	
		}
	},	
	//搜索联想 FF.Suggest.Show('wd',10,'index.php?s=plus-search-index','index.php?s=vod-search-wd-');
	'Suggest': {
		'Show': function($id,$limit,$ajaxurl,$jumpurl){
			$("#"+$id).autocomplete($ajaxurl, {
				width: 175,
				scrollHeight: 300,
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
	//Cookie FF.Cookie.Set(name,value,days);
	'Cookie': {
		'Set': function(name,value,days){
			var exp = new Date();
			exp.setTime(exp.getTime() + days*24*60*60*1000);
			var arr=document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
			document.cookie=name+"="+escape(value)+";path=/;expires="+exp.toUTCString();
		},
		'Get': function(name){
			var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
			if(arr != null){
				return unescape(arr[2]);
				return null;
			}
		},
		'Del': function(name){
			var exp = new Date();
			exp.setTime(exp.getTime()-1);
			var cval = this.Get(name);
			if(cval != null){
				document.cookie = name+"="+escape(cval)+";path=/;expires="+exp.toUTCString();
			}
		}
	},
	//观看历史记录 FF.History.Insert('硝烟背后的战争','',10,1,'','')
	'History': {
		'Json': '',
		'Display': true,
		'List': function($id){
			this.Create($id);
			$('#'+$id).hover(function(){
				FF.History.Show();		
			}, function(){
				FF.History.FlagHide();
				/*setTimeout(function() {
					FF.History.FlagHide();
				}, 200);*/
			});
		},
		'Clear': function(){
			FF.Cookie.Del('FF_Cookie');
			$('#history_box').html('<li class="hx_clear">已清空观看记录。</li>');
		},	
		'Show': function(){
			$('#history_box').show();
		},
		'Hide': function(){
			$('#history_box').hide();
		},
		'FlagHide': function(){
			$('#history_box').hover(function(){
				FF.History.Display = false;
				FF.History.Show();
			}, function(){
				FF.History.Display = true;
				FF.History.Hide();
			});
			if(FF.History.Display){
				FF.History.Hide();
			}
		},
		'Create': function($id){
			var jsondata = [];		
			if(this.Json){
				jsondata = this.Json;
			}else{
				var jsonstr = FF.Cookie.Get('FF_Cookie');
				if(jsonstr != undefined){
					jsondata = eval(jsonstr);
				}
			}
			html = '<dl class="history_box" id="history_box" style="display:none;position:absolute;">';
			html +='<dt><a href="javascript:void(0)" onclick="FF.History.Clear();">清空</a> | <a href="javascript:void(0)" onclick="FF.History.Hide();">关闭</a></dt>';
			if(jsondata.length > 0){
				for($i=0; $i<jsondata.length; $i++){
					if($i%2==1){
						html +='<dd class="odd">';
					}else{
						html +='<dd class="even">';
					}
					html +='<a href="'+jsondata[$i].vodlink+'" class="hx_title">'+jsondata[$i].vodname+'</a></dd>';
				}
			}else{
				html +='<dd class="hide">暂无观看记录。</dd>';
			}
			html += '</dl>';
			$('#'+$id).after(html);
			
			var w = $('#'+$id).width();
			var h = $('#'+$id).height();
			var position = $('#'+$id).position();
			$('#history_box').css({'left':position.left,'top':(position.top+h)});
			//$('#history_box').width(w);
		},	
		'Insert': function(vodname,vodlink,limit,days,cidname,vodpic){
			var jsondata = FF.Cookie.Get('FF_Cookie');
			if(jsondata != undefined){
				this.Json = eval(jsondata);
				//排重
				for($i=0;$i<this.Json.length;$i++){
					if(this.Json[$i].vodlink == vodlink){
						return false;
					}
				}
				//组合新json
				if(!vodlink){ vodlink = document.URL; }			
				jsonstr = '{video:[{"vodname":"'+vodname+'","vodlink":"'+vodlink+'","cidname":"'+cidname+'","vodpic":"'+vodpic+'"},';
				for($i=0; $i<=limit; $i++){
					if(this.Json[$i]){
						jsonstr += '{"vodname":"'+this.Json[$i].vodname+'","vodlink":"'+this.Json[$i].vodlink+'","cidname":"'+this.Json[$i].cidname+$i+'","vodpic":"'+this.Json[$i].vodpic+'"},';
					}else{
						break;//continue;
					}
				}
				jsonstr = jsonstr.substring(0,jsonstr.lastIndexOf(','));
				jsonstr += "]}";
			}else{
				jsonstr = '{video:[{"vodname":"'+vodname+'","vodlink":"'+vodlink+'","cidname":"'+cidname+'","vodpic":"'+vodpic+'"}]}';
			}
			this.Json = eval(jsonstr);
			FF.Cookie.Set('FF_Cookie',jsonstr,days);
		}
	}	
}
// 栏目页分页跳转
var pagego = function($url,$total){
	$page = document.getElementById('page').value;
	if($page>0 && ($page<=$total)){
		$url=$url.replace('{!page!}',$page);
		if($url.split('index-1')){
			$url=$url.split('index-1')[0];
		}
		top.location.href = $url;
	}
	return false;
}
//
$(document).ready(function(){
	//系统初始化
	FF.Home.Js();
	//延时加载
	FF.Lazyload.Show();	
	//搜索联想
	FF.Suggest.Show('wd',12,Root+'index.php?s=plus-search-vod',Root+'index.php?s=vod-search-wd-');
	//历史记录
	FF.History.List('history');
	//影视顶踩初始化
	FF.UpDown.Vod(Root+'index.php?s=Updown-'+FF.Home.Channel+'-id-'+Id);
	//新闻顶踩初始化
	FF.UpDown.News(Root+'index.php?s=Updown-'+FF.Home.Channel+'-id-'+Id);	
	//评论初始化
	FF.Comment.Show(Root+"index.php?s=Cm-Show-sid-"+Sid+"-id-"+Id+"-p-1");
	//积分初始化
	FF.Gold.Default(Root+'index.php?s=Gold-'+FF.Home.Channel+'-id-'+Id);	
});