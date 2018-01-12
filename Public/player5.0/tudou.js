function $Showhtml(){
	player = '<embed allowNetworking="internal" allowFullScreen="true" allowscriptaccess="never" src="http://www.tudou.com/v/'+Player.Url+'/dW5pb25faWQ9MTAyMTk1XzEwMDAwMV8wMV8wMQ/&videoClickNavigate=false&withRecommendList=false&withFirstFrame=false&autoPlay=true/v.swf" type="application/x-shockwave-flash" width="100%" height="'+Player.Height+'"></embed>';
	return player;
}
Player.Show();
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-20;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}