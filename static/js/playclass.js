islogin=0;
syndomain='http://tbmov.tbmov.com/';
function checkcookie(){
	if(document.cookie.indexOf('qr_u=')>=0){
	islogin=1;
	return true;
	}
	return false;
}
checkcookie();

function PlayHistoryClass(){
	var cookieStr,nameArray,urlArray,allVideoArray;
	this.getPlayArray=function (){
		cookieStr = document.cookie;
		var start = cookieStr.indexOf("pp_vod_v=") + "pp_vod_v=".length,end = cookieStr.indexOf("_$_|",start),allCookieStr= unescape(cookieStr.substring(start,end))
		if(end==-1){allCookieStr="";return;}
		allVideoArray = allCookieStr.split("_$_");
		nameArray = new Array(),urlArray = new Array();
		for(var i = 0; i < allVideoArray.length; i++){
			var singleVideoArray = allVideoArray[i].split("^");
			nameArray[i] = singleVideoArray[0];urlArray[i] = singleVideoArray[1];
		}
	}
	this.viewPlayHistory=function (div){
		var tag = document.getElementById(div),n = 12;
		if(checkcookie()){
		$.get(syndomain+"/user-Userdo-getplaylog", function(result){
			    if(result['rcode']>0){
					$("#his-todo").hide();
					if(result['r']!=null&&result['r'].length>0){
						innerStr='';
						for(var i in result['r']){
							var id=getidbyurl(result['r'][i]['url']);
							var vodnames=result['r'][i]['play_name'].split('|');
							var readurl="../redirect.tbmov123.com/user-comm-redirect-id-"+id;
							if(vodnames[1]!=undefined&&vodnames[1]!=null){
							innerStr += "<li><h5><a href=\"" + readurl + "\">"+vodnames[0]+"</a><em>_3C/em><a href='"+result['r'][i]['url']+"' target='_blank'>"+vodnames[1]+"</a></h5><label><a class=\"color\" href=\"" + result['r'][i]['url'] + "\">继续观看</a></label><a href=\"javascript:;\" class='delck' data=\""+result['r'][i]['id']+"\" mtype='ajax'>删除</a></li>"
							}else{
							innerStr += "<li><h5><a href=\"" + result['r'][i]['url'] + "\">"+result['r'][i]['play_name']+"</a></h5><label><a class=\"color\" href=\"" + result['r'][i]['url'] + "\">继续观看</a></label><a href=\"javascript:;\" class='delck' data=\""+result['r'][i]['id']+"\" mtype='ajax'>删除</a></li>"
							}
						}
					if (innerStr.length>0){
						$("#emptybt").unbind();
						$("#emptybt").click(function(){
							PlayHistoryObj.emptyhistory('ajax');
							return false;
						});
						tag.innerHTML= innerStr;$(".delck").click(function(e){
							PlayHistoryObj.removeHistory($(this).attr('data'),$(this).attr('mtype'));
							$(this).parent('li').remove();
							return false;
						});
					}
                }
				else{
					document.getElementById('playhistory').innerHTML="<li class='no-his'><p>暂无播放历史列表...</p></li>";
				}
			}
		},'jsonp');
		}
		if(navigator.cookieEnabled){
			var innerStr = "";
			if(nameArray!=undefined&&nameArray!=null){
			for(var i =nameArray.length - 1; i >= 0; i--){
				var textCount = nameArray[i].replace(/[^\x00-\xff]/g,"cc").length;
				if(textCount <= n*2){
					texts = nameArray[i];
				}else{
					texts = nameArray[i].substr(0,n)+"...";
				}
				var id=getidbyurl(urlArray[i]);
				var vodnames=nameArray[i].split('|');
				var readurl="../redirect.tbmov123.com/user-comm-redirect-id-"+id;
				//innerStr += "<li><a target=_blank class=name href=\"" + urlArray[i] + "\"" + " title=\"" + nameArray[i] + "\">" + texts + "</a><a target=_blank class=now href=\"" + urlArray[i] + "\"" + " title=\"" + nameArray[i] + "\">立即观看</a></li>"
				if(vodnames[1]!=undefined&&vodnames[1]!=null){
				innerStr += "<li><h5><a href=\""+readurl+"\">"+vodnames[0]+"</a><em>/</em><a href=\"" + urlArray[i] + "\">"+vodnames[1]+"</a></h5><label><a class=\"color\" href=\"" + urlArray[i] + "\">继续观看</a></label><a href=\"javascript:;\" class='delck' data='"+i+"' mtype='inck'>删除</a></li>"
				}else{
				innerStr += "<li><h5><a href=\"" + urlArray[i] + "\">"+nameArray[i]+"</a></h5><label><a class=\"color\" href=\"" + urlArray[i] + "\">继续观看</a></label><a href=\"javascript:;\" class='delck' data='"+i+"' mtype='inck'>删除</a></li>"
				}
			}
			}
			$("#his-todo").show();
			$("#morelog").hide();	
			if(innerStr.length>0){
				$("#emptybt").unbind();
				$("#emptybt").click(function(){
					PlayHistoryObj.emptyhistory('ck');
					return false;
				});
				tag.innerHTML= innerStr;$(".delck").click(function(e){
					if(PlayHistoryObj.removeHistory($(this).attr('data'),$(this).attr('mtype'))){
						$(this).parent('li').remove();
						return false;
					}
				});
			}
			else{
				document.getElementById('playhistory').innerHTML="<li class='no-his'><p>暂无播放历史列表...</p></li>";
			}
		}else{
			set(tag,"您浏览器关闭了cookie功能，不能为您自动保存最近浏览过的网页。")
		}
	}
	this.removeHistory=function(ii,type){
		if(type=='inck'){
			var count = 10; //播放历史列表调用条数
			expireTime = new Date(new Date().setDate(new Date().getDate() + 30));timeAndPathStr = "|; expires=" + expireTime.toGMTString() + "; path=/";
			if(cookieStr.indexOf("pp_vod_v=") != -1 || cookieStr.indexOf("_$_|") != -1){
				var newCookieStr = "";
				for(i in allVideoArray){
					if(i!=ii){
						newCookieStr += escape(nameArray[i]) + "^" + escape(urlArray[i]) + "_$_" ;
					}
				}
				document.cookie = "pp_vod_v=" + newCookieStr+ timeAndPathStr;
				return true;
			}
			return false;
		}else{
			$.get('user-Userdo-delplaylog',{id:ii},function(r){
			},'json');
		}
	}
	this.addPlayHistory=function (name,url,cid){
		if(checkcookie()){
		$.post("user-Userdo-renewlog", {"name": name,'url':url,'cid':cid},"json");
		}
		var id= getidbyurl(url);
		var count = 10; //播放历史列表调用条数
		var code_name = escape(name) + "^",code_url = escape(url) + "_$_",expireTime = new Date(new Date().setDate(new Date().getDate() + 30)),timeAndPathStr = "|; expires=" + expireTime.toGMTString() + "; path=/";
		if((cookieStr.indexOf("pp_vod_v=") != -1 || cookieStr.indexOf("_$_|") != -1)&&allVideoArray!=undefined){
			var newCookieStr = "";
			if(allVideoArray.length < count){
				for(i in allVideoArray){
					var curid=getidbyurl(urlArray[i]);
					if(id != curid) {		
					newCookieStr += escape(nameArray[i]) + "^" + escape(urlArray[i])+ "_$_" ;
					}
				}
			}else{
				for(var i = 1; i < count; i++){
					var curid=getidbyurl(urlArray[i]);
					if(id != curid) {	
					newCookieStr += escape(nameArray[i]) + "^" + escape(urlArray[i])+ "_$_" ;
					}
				}
			}
		//	alert("pp_vod_v=" + newCookieStr + code_name + code_url + timeAndPathStr);
			document.cookie = "pp_vod_v=" + newCookieStr + code_name + code_url + timeAndPathStr;
		}else{
		//	alert("pp_vod_v="+ code_name + code_url + timeAndPathStr);
			document.cookie = "pp_vod_v="+ code_name + code_url + timeAndPathStr;
		}
	}
this.emptyhistory=function (type){
$.showfloatdiv(
		{
txt:'确定删除？',
wantclose:2,
suredo:function(e){
		if(type=='ajax'){
				$.get('user-Userdo-emptyhistory',function(r){document.getElementById('playhistory').innerHTML="<li class='no-his'><p>暂无播放历史列表...</p></li>";});
		}else{
			_GC();
		}
		$.closefloatdiv();
		return true;
		}
		}	
		);
	return false;	
	}
}
function _GC(){
	document.getElementById('playhistory').innerHTML="<li class='no-his'><p>暂无播放历史列表...</p></li>";
	var expdate=new Date(1970, 1, 1);
	document.cookie = "pp_vod_v=; path=/";
}
var PlayHistoryObj=new PlayHistoryClass()
PlayHistoryObj.getPlayArray()
function killErrors() {
    return true;
}
function getidbyurl(url){
var id=new String(url.match(/\-id\-(.*)\-sid/g)).replace('-id-','').replace('-sid','');
return parseInt(id);
}
//window.onerror = killErrors;

var topShow=false;
	function showTop(n)
	{
		if(topShow==true)
		{
			switchTab('top',n,2,"history");
		}
		else{
			//alert("s");
			document.getElementById("Tab_top_"+n).className="history";
			//document.getElementById("List_top_"+n).className="dropbox_tigger dropbox_tigger_on";
			document.getElementById("List_top_"+n).style.display="";
			topShow=false;
		}
	}
	function hideTop()
	{
		for(i=0;i<2;i++) {
			var CurTabObj = document.getElementById("Tab_top_"+i) ;
			var CurListObj = document.getElementById("List_top_"+i) ;
			CurTabObj.className="history" ;
			CurListObj.style.display="none";
		}
	}