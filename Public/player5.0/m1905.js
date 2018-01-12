function $Showhtml(){
	player ='<embed type="application/x-shockwave-flash" src="http://static.m1905.com/v/20130321/v.swf" width="100%" height="'+Player.Height+'" style="" id="__M1905FlashPlayer__" name="__M1905FlashPlayer__" bgcolor="#00000" quality="high" allowscriptaccess="always" allownetworking="all" allowfullscreen="true" wmode="Opaque" flashvars="configUrl=http://static.m1905.com/profile/vod/'+Player.Url.substring(0,1)+'/'+Player.Url.substring(1,2)+'/'+Player.Url+'_1.xml&LoGo=false&wide=false&hd=true&light=true&playAd=false&autoPlay=true&cdn=false">';	
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.height = Player.Height-36;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}