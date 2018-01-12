document.write('<style>.FF{line-height:26px;background:#111;font-size:13px;color:#F6F6F6;margin:0px;padding:0px;position:relative;overflow:hidden;width:'+ff_width+'px;height:'+(ff_height+26)+'px;}.FF table{text-align:center;width:100%;}.FF .top{text-align:center;width:100%}.hdd{padding:0;margin:0;width:100%;height:26px;line-height: 26px;border: 1px solid #222;background: #111;border-bottom-width: 1px;border-bottom-style: solid;border-bottom-color: #202020;}.hdd a{color:#F6F6F6;text-decoration:none}.hdd a:hover{text-decoration:underline;}																																									.FF #topleft{width:186px;text-align:left;font-weight: bold;height:26px;line-height: 26px;border-bottom-width: 1px;border-bottom-style: solid;border-bottom-color: #202020;}.FF #topleft a{width:75px;display:inline; zoom:1;letter-spacing:0px;height:26px;line-height:24px;padding: 0 8px 0 8px;overflow: hidden;text-align: center;text-decoration: none;display:block;float:left;border-right-width: 1px;border-right-style: solid;border-right-color: #202020;}.FF #topleft a:hover{background: #000;}.FF #topright{font-weight:bold;text-align:right;border-right:1px solid #202020;border-left-width: 1px;border-left-style: solid;border-left-color: #202020;border-bottom-width: 1px;border-bottom-style: solid;border-bottom-color: #202020;}.FF #topcc{border-bottom-width: 1px;border-bottom-style: solid;border-bottom-color: #202020;}.FF #topright{width:280px;font-weight: bold;text-align:center;text-indent: 23px;border-right: 1px solid #202020;}.FF #playleft{width:100%;height:100%;overflow:hidden;}.FF #playright{}#list{width:280px;scrollbar-face-color:#333;scrollbar-shadow-color:#000;scrollbar-highlight-color:#202020;scrollbar-3dlight-color:#202020;scrollbar-darkshadow-color:#202020;scrollbar-track-color:#111;scrollbar-arrow-color:#666;border:1px solid #222;border-top:0px;border-bottom:0px;background:#111;overflow:auto;overflow-x:hidden;}#list span a{background:url(' + ff_root + 'Public/images/player/list.gif) no-repeat 2px5px;padding-left:15px;display:block;font-size:12px}#list .h2 h2{text-align:left;font-size:14px;cursor:pointer;color: #fff;height: 40px;line-height: 38px;overflow: hidden;text-indent: 23px;border-bottom: 1px solid #202020;border-right-width: 1px;border-right-style: solid;border-right-color: #202020;background:url(/static/css/expand.gif) 8px 13px no-repeat #000;}#list .h2_on{color:#e12160}#list .ul_on{display:block}#list .h2_on h2{color:#e12160;font-size:14px;cursor:pointer;height: 40px;line-height: 38px;overflow: hidden;text-indent: 23px;border-bottom: 1px solid #202020;border-right-width: 1px;border-right-style: solid;border-right-color: #202020;background:url(/static/css/expand.gif) 8px -20px no-repeat #000;}#list .h2_on{color:#e12160;text-align:left;}#list .ul_on{display:block}.FF#list .list_on{color:#FF0000}.expand .title{}.expand_on{color:#e12160;font-weight: bold;background-position: 8px -20px;}.expand_sub{width: 100%;clear:both;overflow: hidden;border-bottom: 1px solid #202020;background: #111;}.expand_sub ul{width: 100%;clear:both;overflow: hidden;zoom:1;}.expand_sub ul li{ width: 50%;height:26px; line-height:26px;float: left;text-align: center;margin: -1px;border: 1px solid #202020;background: #111;}.expand_sub ul li a{display:block;width: 100%;height: 100%;overflow: hidden;}.expand_sub ul a:hover{color: #fff;text-decoration: none;background: #000;}.expand_sub ul li .list_on{text-decoration: none;background: #e12160;color: #fff;}.expand_on{cursor:pointer;color: #fff;height: 40px;line-height: 38px;overflow: hidden;text-indent: 23px;border-bottom: 1px solid #202020;background:url(expand.gif) 8px 13px no-repeat #000;}.prev-next{text-align:right;}.prev-next a{width:80px;display: inline-block;height: 26px;padding: 0 0 0 2px;overflow: hidden;text-align: center;border-left: 1px solid #202020;}.prev-next a:hover{color: #fff;text-decoration: none;background: #000;}</style>');
var $$ = function(value) {
    return document.getElementById(value)
};
var killErrors = function(value) {
    return true
};
window.onerror = null;
window.onerror = killErrors;
var _$ = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '(', ')', 'block', 'none', 'topleft', '<a href="', '">上一集</a> <a href="', '">下一集</a>', 'topcc', '<div id="playppvod" style="height:26px;line-height:26px;overflow:hidden">正在播放：<a href="', '">', '</a> ', '\x20', '</div>', 'topright', '<a href="javascript:void(0)" onClick="Player.ShowList();">开启/关闭列表</a>', 'playleft', '<iframe src="', '" id="buffer" name="buffer" width="100%" height="', '" scrolling="no" frameborder="0" style="display:none;position:absolute;z-index:9;"></iframe>', 'playright', 'px', 'playright', '<div id="list" style="display:', ';height:', 'px">', '</div>', '<scr', 'ipt src="/Public/setup/ff.js" type="text/javascript"></scr', 'ipt>', "buffer", "none", '', 'display:block', 'h2_on', 'display:none', 'h2', '<div style="', '" id="sub', '" class="expand_sub"><ul>', ' class="list_on"', '', '<li><a href="', '" title="', '" ', '>', '</a></li>', '</ul></div>', '<div id="main', '" class="', '">', '<h2 onclick="Player.Tabs(', ',', ')">', 'play_', '</h2>', '</div>', 'list', "none", 'list', "block", 'list', "none", 'sub', 'main', 'h2', 'sub', 'none', 'main', 'h2_on', 'none', 'sub', 'block', 'sub', 'none', 'ff_', "install", '<iframe border="0" src="/Public/setup/play.php?playname=', '&u=', '&v=20130818" marginWidth="0" frameSpacing="0" marginHeight="0" frameBorder="0" noResize scrolling="no" width="100%" height="', '" vspale="0"></iframe>', 'install', 'block', '<div class="FF"><table border="0" cellpadding="0" cellspacing="0"><tr><td colspan="2"><table><tr class="hdd"><td width="100" id="topleft"></td><td id="topcc"></td><td width="100" id="topright"></td></tr></table></td></tr><tr><td colspan="2" id="install" style="display:none"></td></tr><tr><td id="playleft" valign="top">&nbsp;</td><td id="playright" valign="top">&nbsp;</td></tr></table></div>', 'ff_', '', '<scr', 'ipt src="', 'Public/player5.0/', '.js" type="text/javascript" ></scr', 'ipt>'];
var Player = {
    'VodName': _$[0],
    'LisName': _$[1],
    'LisUrl': _$[2],
    'Id': _$[3],
    'Sid': _$[4],
    'Pid': _$[5],
    'UrlName': _$[6],
    'PlayerName': _$[7],
    'ServerUrl': _$[8],
    'Url': _$[9],
    'LastPid': _$[10],
    'LastWebPage': _$[11],
    'NextPid': _$[12],
    'NextUrl': _$[13],
    'NextUrlName': _$[14],
    'NextWebPage': _$[15],
    'ParentUrl': document.URL,
    'UrlJson': eval(_$[16] + ff_urls + _$[17]),
    'Root': ff_root,
    'Buffer': ff_buffer,
    'Pase': ff_buffer,
    'Width': ff_width,
    'Height': ff_height,
    'Second': ff_second,
    'Show': function() {
        if (ff_showlist == 0x1) {
            var list_show = _$[18]
        } else {
            var list_show = _$[19]
        };
        if (this.NextWebPage) {
            var NextWebPage = this.NextWebPage
        } else {
            var NextWebPage = this.ParentUrl
        };
        $$(_$[20]).innerHTML = _$[21] + this.LastWebPage + _$[22] + NextWebPage + _$[23];
        $$(_$[24]).innerHTML = _$[25] + this.ListUrl + _$[26] + this.ListName + _$[27] + this.VodName + _$[28] + this.UrlName + _$[29];
        $$(_$[30]).innerHTML = _$[31];
        $$(_$[32]).innerHTML = _$[33] + this.Buffer + _$[34] + this.Height + _$[35] + $Showhtml();
        $$(_$[36]).style.height = this.Height + _$[37];
        $$(_$[38]).innerHTML = _$[39] + list_show + _$[40] + this.Height + _$[41] + this.CreateList() + _$[42];
        document.write(_$[43] + _$[44] + _$[45])
    },
    'BufferHide': function() {
        $$(_$[46]).style.display = _$[47]
    },
    'CreateList': function() {
        var html = _$[48];
        for (var i = 0x0; i < this.UrlJson.Data.length; i++) {
            if (this.Sid == i) {
                ul_display = _$[49];
                h2class = _$[50]
            } else {
                ul_display = _$[51];
                h2class = _$[52]
            };
            var sid_on;
            var sub_on;
            var html_sub;
            html_sub = _$[53] + ul_display + _$[54] + i + _$[55];
            for (var j = 0x0; j < this.UrlJson.Data[i].playurls.length; j++) {
                var href = this.UrlJson.Data[i].playurls[j][0x2];
                if (this.Sid == i && this.Pid == (j + 0x1)) {
                    var li_on = _$[56]
                } else {
                    li_on = _$[57]
                };
                html_sub += _$[58] + href + _$[59] + this.UrlJson.Data[i].playurls[j][0x0] + _$[60] + li_on + _$[61] + this.UrlJson.Data[i].playurls[j][0x0] + _$[62]
            };
            html_sub += _$[63];
            html += _$[64] + i + _$[65] + h2class + _$[66];
            html += _$[67] + i + _$[68] + (this.UrlJson.Data.length - 0x1) + _$[69] + eval(_$[70] + this.UrlJson.Data[i].playname) + _$[71];
            html += html_sub;
            html += _$[72]
        };
        return html
    },
    'ShowList': function() {
        if ($$(_$[73]).style.display == _$[74]) {
            $$(_$[75]).style.display = _$[76]
        } else {
            $$(_$[77]).style.display = _$[78]
        }
    },
    'Tabs': function(no, n) {
        var subdisply = $$(_$[79] + no).style.display;
        for (var i = 0x0; i <= n; i++) {
            $$(_$[80] + i).className = _$[81];
            $$(_$[82] + i).style.display = _$[83]
        };
        $$(_$[84] + no).className = _$[85];
        if (subdisply == _$[86]) {
            $$(_$[87] + no).style.display = _$[88]
        } else {
            $$(_$[89] + no).style.display = _$[90]
        }
    },
    'Install': function() {
	    var downurl = eval("_$[91] + this.PlayerName");
        $$(_$[92]).innerHTML = _$[93] + this.PlayerName + _$[94] + downurl + _$[95] + this.Height + _$[96];
        $$(_$[97]).style.display = _$[98]
    },
    'Html': function() {
        document.write(_$[99])
    },
    'Play': function() {
        this.Html();
        this.VodName = this.UrlJson.Vod[0x0];
        this.ListName = this.UrlJson.Vod[0x1];
        this.ListUrl = this.UrlJson.Vod[0x2];
        var URL = this.ParentUrl.match(/\d+.*/g)[0x0].match(/\d+/g);
        this.Id = URL[(URL.length - 0x3)] * 0x1;
        this.Sid = URL[(URL.length - 0x2)] * 0x1;
        this.Pid = URL[(URL.length - 0x1)] * 0x1;
        this.Pid = Math.min(this.Pid, this.UrlJson.Data[this.Sid].playurls.length);
        this.PlayerName = this.UrlJson.Data[this.Sid].playname;
        if (this.UrlJson.Data[this.Sid].servername) {
            this.ServerUrl = eval(_$[100] + this.UrlJson.Data[this.Sid].servername)
        };
        this.Url = this.ServerUrl + this.UrlJson.Data[this.Sid].playurls[(this.Pid - 0x1)][0x1];
        this.UrlName = this.UrlJson.Data[this.Sid].playurls[(this.Pid - 0x1)][0x0];
        this.LastPid = Math.max(Math.abs(this.Pid - 0x1), 0x1);
        this.LastWebPage = this.UrlJson.Data[this.Sid].playurls[this.LastPid - 0x1][0x2];
        this.NextPid = Math.min(this.Pid + 0x1, this.UrlJson.Data[this.Sid].playurls.length);
        this.NextUrl = this.ServerUrl + this.UrlJson.Data[this.Sid].playurls[this.NextPid - 0x1][0x1];
        this.NextUrlName = this.UrlJson.Data[this.Sid].playurls[this.NextPid - 0x1][0x0];
        if (this.Url == this.NextUrl) {
            this.NextWebPage = _$[101]
        } else {
            this.NextWebPage = this.UrlJson.Data[this.Sid].playurls[this.NextPid - 0x1][0x2]
        };
        document.write(_$[102] + _$[103] + this.Root + _$[104] + this.PlayerName + _$[105] + _$[106])
    }
}
Player.Play();