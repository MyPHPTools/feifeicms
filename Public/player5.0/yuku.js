function $Showhtml(){
	player = '<embed type="application/x-shockwave-flash" src="http://static.youku.com/v1.0.0242/v/swf/qplayer.swf" id="movie_player" name="movie_player" bgcolor="#FFFFFF" quality="high" allowfullscreen="true" flashvars="isShowRelatedVideo=false&showAd=0&show_pre=1&show_next=1&VideoIDS='+Player.Url+'&isAutoPlay=true&isDebug=false&UserID=&winType=interior&playMovie=true&MMControl=false&MMout=false&RecordCode=1001,1002,1003,1004,1005,1006,2001,3001,3002,3003,3004,3005,3007,3008,9999" pluginspage="http://www.macromedia.com/go/getflashplayer" width="100%" height="'+Player.Height+'">';	
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-39;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}