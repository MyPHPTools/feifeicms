function isIE() {
	if (!!window.ActiveXObject || "ActiveXObject" in window)  
		return true;  
	else  
		return false;  
}  
function $Showhtml(){
	if(!isIE()){
		return $PlayerNt();
	}else{
		return $PlayerIe();
	}
}
function $PlayerNt(){
	if (navigator.plugins) {
		var install = true;
		for (var i=0;i<navigator.plugins.length;i++) {
			if(navigator.plugins[i].name == 'FFPlayer Plug-In'){
				install = false;break;
			}
		}
		if(!install){
			player = '<object id="FFHDPlayer" name="FFHDPlayer" type="application/npFFPlayer" width="100%" height="'+Player.Height+'" progid="XLIB.FFPlayer.1" url="'+Player.Url+'"></object>';
			return player;
		}
	}
	Player.Install();
}
function $PlayerIe(){
	player = '<object classid="clsid:D154C77B-73C3-4096-ABC4-4AFAE87AB206" width="100%" height="'+Player.Height+'" id="FFHDPlayer" name="FFHDPlayer" onerror="Player.Install();"><param name="url" value="'+Player.Url+'"/></object>';
	return player;
}
//beta7�沥�����ص�����
var onPlay = function(){
	$$('buffer').style.display = 'none';
	//ǿ�ƻ����浹��ʱ
	if(Player.Second && FFHDPlayer.IsPlaying()){
		FFHDPlayer.Play();
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
		FFHDPlayer.Play();
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
	//�̶�������ʱ�䲥�����
	Player.Second = 0;
	if(FFHDPlayer.IsPause()){
		FFHDPlayer.Play();
	}
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-39;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}