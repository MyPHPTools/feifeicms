function $Showhtml(){
	player = '<object id="realObj" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="100%" height="'+(Player.Height-60)+'">';
	player += '<param name="CONTROLS" value="ImageWindow">';
	player += '<param name="CONSOLE" value="Clip1">';
	player += '<param name="AUTOSTART" value="-1">';
	player += '<param name="src" value="'+Player.Url+'"></object><br>';
	player += '<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="100%" height="60">';
	player += '<param name="CONTROLS" value="ControlPanel,StatusBar">';
	player += '<param name="CONSOLE" value="Clip1"></object>';
	player += '<div style="text-align:center; margin-top:50px;">';
	player += '<a href="#" onClick="document.realObj.SetFullScreen();">点击这里全屏收看 按ESC键退出</a>';
	player += '</div>';
	return player;
}
Player.Show();