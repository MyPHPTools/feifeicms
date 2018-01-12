function $Showhtml(){
	return '<embed type="application/x-shockwave-flash" src="http://player.pps.tv/static/vs/v1.0.0/v/swf/flvplay_s.swf" width="100%" height="'+Player.Height+'" id="myDynamicContent" name="myDynamicContent" bgcolor="#00000" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" menu="false" flashvars="url_key='+Player.Url+'&video_id=888888888&referer=http://tv.pps.tv/&fid=feifeicms"></embed>';
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-63;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}