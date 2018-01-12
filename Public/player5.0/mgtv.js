function $Showhtml(){	
player = '<embed type="application/x-shockwave-flash" src="http://i1.hunantv.com/ui/swf/player/v0818/main.swf" id="movie_player" name="movie_player" bgcolor="#FFFFFF" quality="high" allowScriptAccess="best" wmode="transparent" allowfullscreen="true"  flashvars="VideoIDS=tid=1&cid='+Player.Url+'&cdn=3" pluginspage="http://www.macromedia.com/go/getflashplayer" width="100%" height="'+Player.Height+'">';
return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-28;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}