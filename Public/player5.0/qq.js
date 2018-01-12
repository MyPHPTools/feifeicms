function $Showhtml(){
	player = '<embed height="'+Player.Height+'" width="100%" flashvars="vid='+Player.Url+'&amp;skin=http://imgcache.qq.com/minivideo_v1/vd/res/skins/QQPlayerSkin.swf&amp;autoplay=1&amp;gourl=http://video.qq.com/v1/videopl?v=&amp;list=1&amp;" allowfullscreen="true" wmode="Opaque" allowscriptaccess="always" quality="high" src="http://cache.tv.qq.com/QQPlayer.swf" type="application/x-shockwave-flash" id="player">';
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-30;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}