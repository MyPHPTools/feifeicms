function $Showhtml(){
	player ='<object width="100%" height="'+Player.Height+'" classid="clsid:A74BF134-5213-46B5-AF36-CE1888315DC7" onError="Player.Install();" id="pipiwebplayer_control">';
	player +='<PARAM NAME="URL" VALUE="'+Player.Url+'">';
	player +='<PARAM NAME="lActiveXStyle" VALUE="1">';
	player +='</object>';
	return player;
}
Player.Show();
try {
	new ActiveXObject("PIPIWEBPLAYER.PIPIWebPlayerCtrl.1");		
	document.getElementById("pipiwebplayer_control").playurl(Player.Url);
} catch(e) {}