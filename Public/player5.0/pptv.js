function $Showhtml(){
	player = '<embed src="http://player.pptv.com/v/'+Player.Url+'.swf" quality="high" width="100%" height="'+Player.Height+'" align="middle" allowScriptAccess="always" allownetworking="all" type="application/x-shockwave-flash" wmode="window" allowFullScreen="true" play="true"></embed>';
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.position = 'absolute';
	$$('buffer').style.height = Player.Height-40;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}