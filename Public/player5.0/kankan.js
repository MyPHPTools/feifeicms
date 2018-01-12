	var a=Player.Url.split("-");
	var u1=a[0];
	var u2=a[1];
function $Showhtml(){
	return '<embed allowfullscreen="true" wmode="transparent" quality="high" src="http://video.kankan.com/dt/swf/v_sina.swf?id='+u1+'&sid='+u2+'&vtype=1&mtype=teleplay" quality="high" bgcolor="#000" width="100%" height="'+Player.Height+'" name="player" id="playerr" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>';
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-28;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}