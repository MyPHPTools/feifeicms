function $Showhtml(){
	player ='<object width="100%" height="'+Player.Height+'" classid="clsid:1DD5176B-B71E-4956-8F32-691702ACDCFE" onError="Player.Install();">';
	player +='<PARAM NAME="URL" VALUE="'+Player.Url+'">';
	player +='<PARAM NAME="NextWebUrl" VALUE="'+Player.NextUrl+'">';
	player +='</object>';
	return player;
}
$ShowPlayer();