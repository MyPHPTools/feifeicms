function $Showhtml(){
	player = '<embed id="embed_flash_player" wmode="opaque" allowscriptaccess="always" src="http://s1.56img.com/flashApp/v_player.13.07.11.as3.b.swf?vid='+Player.Url+'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowfullscreen="true" width="100%" height="'+Player.Height+'"></embed>';
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.position = 'absolute';
	$$('buffer').style.height = Player.Height-42;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}