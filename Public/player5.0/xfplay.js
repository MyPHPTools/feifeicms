
function $Showhtml(){
       if(window.ActiveXObject || window.ActiveXObject !== undefined)
            return $PlayerIe();
       else
            return $PlayerNt();
}

function $PlayerNt(){
	if (navigator.plugins) {
            var Install = false;
				for (i=0; i < navigator.plugins.length; i++ ) 
				{
					var n = navigator.plugins[i].name;
					if( navigator.plugins[n][0]['type'] == 'application/xfplay-plugin')
					{
						Install = true; break;
					}		
				} 

		if(Install){
			player = '<embed type="application/xfplay-plugin" PARAM_URL="'+Player.Url+'" PARAM_Status="1" width="100%" height="'+Player.Height+'" id="Xfplay" name="Xfplay"></embed>';
			return player;
		}
	}
	Player.Install();
}

function $PlayerIe(){
         player = '<object ID="Xfplay" name="Xfplay" width="100%" height="'+Player.Height+'" onerror="Player.Install();" classid="clsid:E38F2429-07FE-464A-9DF6-C14EF88117DD" >';
         player += '<PARAM name="URL" value="'+Player.Url+'">';
         player += '<PARAM name="Status" value="1"></object>';
         return player;
}

Player.Show();
