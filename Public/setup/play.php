<?php 
/** 
* @author by请叫我一刀 
* @copyright 2014-12-05
* @输出USER-AGENTS print_r($_SERVER['HTTP_USER_AGENT']); 
*/ 
$playname = $_GET['playname'];
$u = $_GET['u'];
$v = $_GET['v'];//USER_AGENT转换小写
$my_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']); 
//定义播放器URL
$play_win_bdhd = "/Public/setup/bdhd.html";
$play_win_ffhd = "/Public/setup/ffhd.html";
$play_win_xigua = "/Public/setup/xigua.html";
$play_win_xfplay = "/Public/setup/xfplay.html";
$play_win_jjvod = "/Public/setup/jjvod.html";
$play_android_bdhd = "/Public/setup/bdhd.html";
$play_ios_bdhd = "/Public/setup/bdhd.html"; 
$play_win_qvod = "/Public/setup/qvod.html";
$play_android_qvod = "/Public/setup/qvod.html";
$play_ios_qvod = "/Public/setup/qvod.html"; 

//跳转函数
function headerUrl($url)
{    
     header("HTTP/1.1 302 Moved Permanently");    
	 header("Location: $url");
} 
//Windows平台
if(strpos($my_user_agent,"windows") == true)
{    
       if($playname == "bdhd")    
	   {        
	       //Windows百度影音        
		   headerUrl($play_win_bdhd);    
		   }
		   
	   elseif($playname == "qvod")
	   {        
	       //Windows快播        
		   headerUrl($play_win_qvod);    
		}
			   elseif($playname == "xigua")
	   {        
	       //Windows快播        
		   headerUrl($play_win_xigua);    
		}
			   elseif($playname == "xfplay")
	   {        
	       //Windows快播        
		   headerUrl($play_win_xfplay);    
		}
			   elseif($playname == "jjvod")
	   {        
	       //Windows快播        
		   headerUrl($play_win_jjvod);    
		}
		
		 elseif($playname == "ffhd")
	   {        
	       //Windows快播        
		   headerUrl($play_win_ffhd);    
		}
} 
 //android平台
if(strpos($my_user_agent,"android") == true)
{    
	     if($playname == "bdhd")    
		 {        
		    //android百度影音       
			headerUrl($play_android_bdhd);
	     }
		 elseif($playname == "qvod")
	    {        
	       //android快播        
		   headerUrl($play_android_avod);    
		}
} 
//iPhone平台 包含iPod
        if(strpos($my_user_agent,"iphone") == true || strpos($my_user_agent,"ipod") == true)
		{    
		    if($playname == "bdhd")    
			{       
			//iPhone百度影音        
			headerUrl($play_ios_bdhd);    
			}
			elseif($playname == "qvod") 
	    {        
	       //android快播        
		   headerUrl($play_ios_qvod);    
		}

} 
?> 