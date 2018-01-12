var bstartnextplay = false;
function $Showhtml(){
	Player.Url = $QvodUrl(Player.Url,Player.UrlName);
	Player.NextUrl = $QvodUrl(Player.NextUrl,Player.UrlNextName);
	var browser = navigator.appName;
	if(browser == "Netscape"){
		return $PlayerNt();
	}else if(browser == "Microsoft Internet Explorer"){
		return $PlayerIe();
	}else{
		alert('请使用IE内核浏览器如 蚂蚁浏览器观看本站影片!');
	}
}
function $PlayerNt(){
	if (navigator.plugins) {
		var install = true;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'QvodInsert'){
				install = false;break;
			}
		}
		if(!install){
			player = '<embed type="application/qvod-plugin" URL="'+Player.Url+'" NextWebPage="'+Player.NextWebPage+'" Autoplay="1" QvodAdUrl="'+Player.Buffer+'" width="100%" height="'+Player.Height+'" id="QvodPlayer" name="QvodPlayer"></embed>';
			return player;
		}
	}
	Player.Install();
}
function $PlayerIe(){
	player = '<object classid="clsid:F3D0D36F-23F8-4682-A195-74C92B03D4AF" width="100%" height="'+Player.Height+'" id="QvodPlayer" name="QvodPlayer" onerror="Player.Install();" style="display:none"><param name="URL" value="'+Player.Url+'"/><param name="QvodAdUrl" value="'+Player.Buffer+'"/><param name="NextWebPage" value="'+Player.NextWebPage+'"><param name="Autoplay" value="1"/></object>';
	return player;
}
function $QvodNextDown(){
	if(QvodPlayer.get_CurTaskProcess() > 900 && !bstartnextplay){
		QvodPlayer.StartNextDown(Player.NextUrl);
		bstartnextplay = true;
	}
}
function $QvodUrl(url,urlname){
	if(url.indexOf("vod://")>0){
		onurl = url.split("|");
		//获取地址后缀
		var parts = onurl[2].split(".");
		if (parts.length > 1) {   
			suffix = parts.pop();
		}else{
			suffix = 'rmvb';
		}
		return(onurl[0]+"|"+onurl[1]+"|"+Player.VodName+urlname+"[蚂蚁浏览器看电影速度快]."+suffix+'|');
	}
	return(url);
}
function $QvodStatus(){
	if(QvodPlayer.Full == 0){
		if(QvodPlayer.PlayState == 3){
			$$('buffer').style.display = 'none';
		}else{
			$$('buffer').style.display = 'block';
		}
	}
}
Player.Show();
if(QvodPlayer.URL != undefined){
	QvodPlayer.style.display = 'block';
	var xml = '<invoke name="QvodVersion" returntype="xml"></invoke>';
	var version = QvodPlayer.CallFunction(xml).replace('"></invoke>','').replace('<invoke name="QvodVersion" returntype="xml"','').replace('vn="','').split('.');
	version = Number(version[0]);
	//alert(version);
	if(version > 3){
		$$('buffer').height = Player.Height-42;
	}else{
		$$('buffer').height = Player.Height-69;
	}
	setInterval("$QvodStatus()",588);
	if(Player.NextWebPage){
		setInterval("$QvodNextDown()",8888);
	}	
}