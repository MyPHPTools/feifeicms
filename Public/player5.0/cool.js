function $Showhtml(){
	Player.Url = $Coolurl(Player.Url,Player.UrlName);
	Player.NextUrl = $Coolurl(Player.NextUrl,Player.NextUrlName);
	player ='<object id="CoolPlayer" name="CoolPlayer" width="100%" height="'+Player.Height+'" classid="clsid:73BAB958-AC02-5108-B2B8-665834A9C63A" onError="Player.Install();" style="display:none;">';
	player +='<PARAM NAME="URL" VALUE="'+Player.Url+'">';
	player +='<PARAM NAME="Autoplay" VALUE="1">';
	player +='<PARAM NAME="CoolAdUrl" VALUE="'+Player.Buffer+'">';
	player +='<PARAM NAME="NextWebPage" VALUE="'+Player.NextWebPage+'">';
	player +='</object>';
	if(!window.ActiveXObject){
		player +='<embed URL="'+Player.Url+'" type="application/cool-plugin" width="100%" height="'+Player.Height+'" onError="Player.Install();" ></embed>';
	}
	return player;
}
function $CoolStatus(){
	if(CoolPlayer.Full == 0){
		if(CoolPlayer.PlayState == 3){
			$$('buffer').style.display = 'none';
		}else{
			$$('buffer').style.display = 'block';
		}
	}	
}
function $CoolNextDown(){
	if(CoolPlayer.get_CurTaskProcess() > 900 && !bstartnextplay){
		CoolPlayer.StartNextDown(Player.NextUrl);
		bstartnextplay = true;
	}
}
function $Coolurl(url,urlname){
	if(url.indexOf("cool://")>0){
		url = url.split("|");
		coolurl = url[0]+"|"+url[1]+"|"+urlname+"[www.feifeicms.com].rmvb|";
		return coolurl;
	}
	return url;
}
ff_cool = '';
Player.Show();
if(CoolPlayer.URL != undefined){
	$$('buffer').height = Player.Height-60;
	CoolPlayer.style.display = 'block';
	setInterval("$CoolStatus()",500);
	if(Player.NextWebPage){
		var bstartnextplay = false;
		setInterval("$CoolNextDown()",9333);
	}
}