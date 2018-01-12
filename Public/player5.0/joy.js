function $Showhtml(){
	player = '<embed type="application/x-shockwave-flash" src="http://client.joy.cn/flvplayer/v20081022.swf?strvid='+Player.Url+'" id="Player" bgcolor="#FFFFFF" quality="high" allowfullscreen="true" allowNetworking="internal" allowscriptaccess="never" wmode="transparent" menu="false" always="false"  pluginspage="http://www.macromedia.com/go/getflashplayer" width="100%" height="'+Player.Height+'"></embed>';
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.position = 'absolute';
	$$('buffer').style.height = Player.Height-42;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}