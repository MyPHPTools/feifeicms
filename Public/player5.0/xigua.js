function $Showhtml(){
	Player.LastWebPage = 'http://'+window.location.host+Player.LastWebPage;
	Player.NextWebPage = 'http://'+window.location.host+Player.NextWebPage;
	Player.Url = $BdhdUrl(Player.Url,Player.UrlName);
	Player.NextUrl = $BdhdUrl(Player.NextUrl,Player.NextUrlName);
	if(Player.Url == Player.NextUrl){ Player.NextUrl = ''; }
	var browser = navigator.appName;
	if(browser == "Netscape"){
		return $PlayerNt();
	}else if(browser == "Microsoft Internet Explorer"){
		return $PlayerIe();
	}else{
		alert('请使用IE内核浏览器观看本站影片!');
	}
}
function $BdhdUrl(url,urlname){
	if(url.indexOf("dhd://")>0){
		onurl = url.split("|");
		//获取地址后缀
		var parts = onurl[2].split(".");
		if (parts.length > 1) {
			suffix = parts.pop();
		}else{
			suffix = 'rmvb';
		}
		return(onurl[0]+"|"+onurl[1]+"|"+Player.VodName+urlname+"[www.feifeicms.cc]."+suffix);
	}
	return(url);
}
function $PlayerNt(){
	if (navigator.plugins) {
		var isInstall = false;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'XiGua Yingshi Plugin'){
				isInstall = true; break;
			}
		}
		if(isInstall){
			player = '<object id="XiguaPlayer" name="XiguaPlayer" type="application/xgyingshi-activex" progid="xgax.player.1" width="100%" height="'+Player.Height+'" progid="Xbdyy.PlayCtrl.1" param_URL="'+Player.Url+'"param_NextCacheUrl="'+Player.NextUrl+'" param_LastWebPage="'+Player.LastWebPage+'" param_NextWebPage="'+Player.NextWebPage+'" param_OnPlay="onPlay" param_OnPause="onPause" param_OnFirstBufferingStart="onFirstBufferingStart" param_OnFirstBufferingEnd="onFirstBufferingEnd" param_OnPlayBufferingStart="onPlayBufferingStart" param_OnPlayBufferingEnd="onPlayBufferingEnd" param_OnComplete="onComplete" param_Autoplay="1"></object>';
			return player;
		}
	}
	Player.Install();
}
function $PlayerIe(){
	player = '<object classid="clsid:BEF1C903-057D-435E-8223-8EC337C7D3D0" width="100%" height="'+Player.Height+'" id="XiguaPlayer" name="XiguaPlayer" onerror="Player.Install();" style="display:none"><param name="URL" value="'+Player.Url+'"/><param name="NextCacheUrl" value="'+Player.NextUrl+'"><param name="LastWebPage" value="'+Player.LastWebPage+'"><param name="NextWebPage" value="'+Player.NextWebPage+'"><param name="OnPlay" value="onPlay"/><param name="OnPause" value="onPause"/><param name="OnFirstBufferingStart" value="onFirstBufferingStart"/><param name="OnFirstBufferingEnd" value="onFirstBufferingEnd"/><param name="OnPlayBufferingStart" value="onPlayBufferingStart"/><param name="OnPlayBufferingEnd" value="onPlayBufferingEnd"/><param name="OnComplete" value="onComplete"/><param name="Autoplay" value="1"/></object>';
	return player;
}
//beta7版播放器回调函数
var onPlay = function(){
	$$('buffer').style.display = 'none';
	//强制缓冲广告倒计时
	if(Player.Second && XiguaPlayer.IsPlaying()){
		XiguaPlayer.Play();
	}
}
var onPause = function(){
	$$('buffer').height = Player.Height-60;
	$$('buffer').style.display = 'block';
}
var onFirstBufferingStart = function(){
	$$('buffer').height = Player.Height-80;
	$$('buffer').style.display = 'block';
}
var onFirstBufferingEnd = function(){
	if(Player.Second){
		XiguaPlayer.Play();
	}else{
		$$('buffer').style.display = 'none';
	}
}
var onPlayBufferingStart = function(){
	$$('buffer').height = Player.Height-80;
	$$('buffer').style.display = 'block';
}
var onPlayBufferingEnd = function(){
	$$('buffer').style.display = 'none';
}
var onComplete = function(){
	onPause();
}
var onAdsEnd = function(){
	//固定缓冲广告时间播放完毕
	Player.Second = 0;
	if(XiguaPlayer.IsPause()){
		XiguaPlayer.Play();
	}
}
Player.Show();
$$('buffer').style.display = 'none';
if(XiguaPlayer.URL != undefined){
	XiguaPlayer.style.display = 'block';
	if(Player.Second){
		setTimeout("onAdsEnd()",Player.Second*1000);
	}
}