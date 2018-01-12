var webpageurl = unescape(window.location.href);

function $Showhtml(){
	player = '<iframe border="0" src="'+Player.Root+'Public/player5.0/jjvod.html" width="100%" height="'+Player.Height+'" marginWidth="0" frameSpacing="0" marginHeight="0" frameBorder="0" scrolling="no" vspale="0" noResize></iframe>';
	return player;
	
}
Player.Show();