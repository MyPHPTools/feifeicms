	var a=Player.Url.split("&tvid=");
	var u1=a[0];
	var u2=a[1];
function $Showhtml(){
  var ua = navigator.userAgent.toLowerCase();
  if(ua.match(/ipad/i)=="ipad" || ua.match(/iphone/i)=="iphone")
    {
    var player = ['<iframe width="100%" height="'+Player.Height+'" src="http://www.iqiyi.com/common/openiframe.html?vid=' + u1 + '&tvid=' + u2+ '&coop=coop_4399&cid=qc_100001_300047&autoPlay=1" frameborder="no">',
    '</iframe>'].join('');
    }
  else
    {
    var player = ['<iframe width="100%" height="'+Player.Height+'" src="http://dispatcher.video.qiyi.com/common/shareplayer.html?vid=' + u1 + '&tvid=' + u2+ '&coop=coop_4399&cid=qc_100001_300047&autoPlay=1" frameborder="no">',
    '</iframe>'].join('');
    }
	return player;
}
Player.Show();
/* 海洋修复，www.feifeicms.cc 请叫我一刀 整理 */
if(Player.Second){
	$$('buffer').style.height = Player.Height-28;
	$$("buffer").style.display = "block";
	setTimeout("Player.BufferHide();",Player.Second*1000);
}
/* 海洋修复，www.feifeicms.cc 请叫我一刀 整理 */
