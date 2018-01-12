function $Showhtml(){
	player = '<embed allowfullscreen="true" src="http://p.you.video.sina.com.cn/swf/bokePlayer20130726_V4_1_42_23.swf?vid='+Player.Url+'&clip_id=&imgurl=&auto=1&vblog=1&type=0&tabad=1&autoLoad=1&autoPlay=1&as=0&tjAD=0&tj=0&casualPlay=1&head=0&logo=0&share=0" quality="high" bgcolor="#000" width="100%" height="'+Player.Height+'" name="player" id="playerr" align="middle" allowscriptaccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>';
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.position = 'absolute';
	$$('buffer').style.height = Player.Height-35;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}