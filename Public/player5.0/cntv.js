function $Showhtml(){
	player = '<embed allowfullscreen="true" wmode="opaque" src="http://player.cntv.cn/standard/cntvplayer.swf?v=0.171.5.7.9.0&videoCenterId='+Player.Url+'&videoId=20110711" quality="high" bgcolor="#000" width="100%" height="'+Player.Height+'" name="player" id="playerr" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>';	
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-35;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}