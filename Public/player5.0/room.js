function $Showhtml(){
	player = '<embed src="http://v.6.cn/apple/player/videoplayer/xiu_vp1.9.swf?vid='+Player.Url+'&model=1" width="100%" height="'+Player.Height+'" allowscriptaccess="always" wmode="window" type="application/x-shockwave-flash"></embed>';
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.position = 'absolute';
	$$('buffer').style.height = Player.Height-42;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}