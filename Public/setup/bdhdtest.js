if(navigator.userAgent.indexOf("MSIE")>0){if(navigator.userAgent.indexOf("MSIE 9.0")>0||navigator.userAgent.indexOf("MSIE 10.0")>0){window.location='bdhd.html';}}
var downurl = 'http://dl.p2sp.baidu.com/BaiduPlayerAB.php';
var url = document.URL;
if(url.indexOf("dl.p2sp.baidu.com")>1){
	var TN = url.split("&v=")[0].split("@u=")[1].replace(/http:\/\/dl.p2sp.baidu.com\/BaiduPlayer\/un2\/BaiduPlayerNetSetup_(.*).exe/ig,"$1");
	if(TN*1){
		downurl = '../down.aq8.cc/BaiduPlayerNetSetup_'+TN+'.exe';
	}
}else if(url.indexOf("dl.client.baidu.com")>1){
	var TN = url.split("&v=")[0].split("@u=")[1].replace(/http:\/\/dl.client.baidu.com\/BaiduPlayer\/un\/BaiduPlayer_(.*).exe/ig,"$1");
	if(TN*1){
		downurl = 'http://dl.p2sp.baidu.com/BaiduPlayerNetSetup_'+TN+'.exe';
	}
}