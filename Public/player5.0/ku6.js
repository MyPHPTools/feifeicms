function $Showhtml(){
	player = '<embed type="application/x-shockwave-flash" src="http://player.ku6cdn.com/default/out/pV2.7.3.swf" id="mymovie" name="mymovie" quality="high" flashvars="ver=108&amp;jump=0&amp;api=1&amp;auto=1&amp;color=0&amp;deflogo=0&amp;flag=hd&amp;adss=0&amp;vid='+Player.Url+'&amp;type=v" wmode="transparent" allowscriptaccess="always" allowfullscreen="true" width="100%" height="'+Player.Height+'">';	
	return player;
}
Player.Show();
if(Player.Second){
	$$('buffer').style.position = 'absolute';
	$$('buffer').style.height = Player.Height-60;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}