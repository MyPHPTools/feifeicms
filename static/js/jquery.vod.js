$(document).ready(function(){
	
	// cmt-input-tip
	$("#cmt-input-tip .ui-input").focus(function(){
		$("#cmt-input-tip").hide();
		$("#cmt-input-bd").show();
		$("#cmt-input-bd .ui-textarea").focus();
	});
	// interest-sect
	$(".user-bt").each(function(){	
		var $btn = $(this).find(".sect-btn");
		var $cancel = $(this).find(".cancel");
		var $div =$(this).find(".sect-show");
		$btn.click(function(){	
			if(!checkcookie()) {
			login_form();
			return false;
			return false;
		     }
			$.showfloatdiv({txt:'数据提交中...',cssname:'loading'});
			var curobj=$(this);
			$.get(
			 $btn.attr('data')+Id,function(r){
			 try{
			 r=eval('('+r+')');
			 $.hidediv(r);
			 if(parseInt(r.rcode)>0){
			 curobj.hide();
			 $div.show();
			 $cancel.show();
			 $cancel.attr('val',r['loveid']);
			 }else{
			 if(parseInt(r['yjdy'])>0){
			   if(parseInt(r['yjdy'])==1){
				   curobj.hide(r);
			 $div.show();			
			 $cancel.show();
			 }
			 }else{
			login_form();
			return false;
			 }
			 }
			 }catch(e){
			login_form();
			return false;
			 }
			},'html'	
			);
			
		});
		$cancel.click(function(){	
			$.showfloatdiv({txt:'数据提交中...',cssname:'loading'});
			var lid=$cancel.attr('val');
			$.get(
			$cancel.attr('data')+lid,function(r){
			 $.hidediv(r);
			 if(parseInt(r.rcode)>0){
				$btn.show();
			    $div.hide();
			 }
			},'json'	
			);
		});
	});
	
	// Rating Star Jquery
	$("ul.rating li").each(function(i){
		var $title = $(this).attr("title");
		var $lis = $("ul.rating li");
		var num = $(this).index();
		var n = num+1;
		$(this).click(function(){
			if(hadpingfen>0){
			$.showfloatdiv({txt:'已经评分,请务重复评分'});
			$.hidediv({});
			}else{
			$.showfloatdiv({txt:'数据提交中...',cssname:'loading'});
			$lis.removeClass("active");												  
			$("ul.rating li:lt("+n+")").addClass("active");	 
			$("#ratewords").html($title);
			$.post('/index.php?s=Gold',{val:$(this).attr('val'),'id':Id},function(r){
				if(parseInt(r.rcode)>0){
				$.hidediv(r);
				loadstat();
				hadpingfen=1;
				}else{
					if(parseInt(r.rcode)==-2){
						hadpingfen=1;
						$.showfloatdiv({txt:'已经评分,请务重复评分'});
			            $.hidediv({});
					}else{
					$.closefloatdiv();
					$("#innermsg").trigger('click');	
					}
					
				}
			},'json');
		}
		}
		).hover(function(){
			this.myTitle=this.title;
			this.title="";
			//$("ul.rating li:lt("+n+")").addClass("hover");
			$(this).nextAll().removeClass("active");
			$(this).prevAll().addClass("active");
			$(this).addClass("active");
			$("#ratewords").html($title);
		},function(){
			this.title=this.myTitle;	
			$("ul.rating li:lt("+n+")").removeClass("hover");
		});	
		
	});
	// rating-panle
	$(".rating-panle").hover(function(){
		$(this).find(".rating-show").show();
	},function(){
		$(this).find(".rating-show").hide();
	});
	
	
});