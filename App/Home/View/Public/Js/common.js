// JavaScript Document
$(function(){
	saveNumPerPageToCookie();
	$(window).resize(function() {
		saveNumPerPageToCookie();
	});
});
//初始化数据表格下方翻页效果
function initPager(){
	$("#pager a,#pager #gopage_btn_confirm").addClass("ui-state-default").bind("mouseover",function(){
		$(this).addClass("ui-state-hover");	
	}).bind("mouseout",function(){
		$(this).removeClass("ui-state-hover");	
	});	
	$("#pager").find(".current").addClass("ui-state-focus");
	$("#gopage_btn_confirm").bind("click",function(){
    
		var patt=new RegExp(/\/p\/\d+/);
		var newUrl=location.href;
		if(patt.test(location.href)){
			newUrl=newUrl.replace(patt,"/p/"+$("#gopage_input").val()); //把当前的url地址匹配为查询输入的页码的值并替换掉以前的页码值
		}else{
			newUrl=newUrl.replace(/\w+=$|\w+=&/g,""); // g 表示替代所有匹配此正则的 newUrl  \w+= 匹配多个数字、字符或下划线且结尾带个=&号的字符串 或者匹配多个数字、字符或下划线且结尾带=&的字符串
			//newUrl=newUrl.replace(/\?|&|=/g,"/");
			if(newUrl.indexOf(".html")!=-1){
				newUrl=newUrl.replace(/\.html/,"/p/"+$("#gopage_input").val()+".html");
			}else{
				newUrl=newUrl+"/p/"+$("#gopage_input").val();
			}
		}
            //   if(newUrl.indexOf(".html")!=-1){ location.href=newUrl.replace(/\/\/p\//,"/p/");  }
         //   alert(newUrl.replace(/\/\/p\//,"/p/"))
		location.href=newUrl.replace(/\/\/p\//,"/p/");
	});
	
}

function saveNumPerPageToCookie(){
	if($("#tabs ul")[0] && $(".filter")[0] && $(".tableList th")[0] && $("#pager")[0]){
		var n=parseInt(($(window).height()-$("#tabs ul").height()-40-$(".filter").height()-$(".tableList th").height()-$("#pager").height())/31)-1;
		n=n<8?8:n;
		$.cookie('n', n);
	}
	
}

//设置title
function setTitle($target,content){
	$target.tooltip({
		content: content,
		track: true,
		position: { my: "left+5 bottom-5", at: "center top" }
	});	
}

function updateTips(t) {
	$(".validateTips")
		.text( t )
		.removeClass("errorTip")
		.addClass("errorTip");
		/*
	setTimeout(function() {
		$(".validateTips").removeClass("errorTip",2000).fadeOut(1500);
	}, 1000);*/
}

function checkEmpty(o,n){
	if(!o.val() || o.val()==''){
		o.addClass( "ui-state-error" );
		updateTips( n );
		$(".validateTips").show();
		return false;	
	}else{
		return true;	
	}
}

function checkEquals(target,source,n){
	if(target.val()!=source.val()){
		source.addClass( "ui-state-error" );
		updateTips( n );
		$(".validateTips").show();
		return false;	
	}else{
		return true;	
	}	
}

function checkLength(o, n, min, max) {
	if ( o.val().length > max || o.val().length < min ) {
		o.addClass( "ui-state-error" );
		updateTips( n + " 长度只能在 " +
			min + "-" + max + "之间！" );
		$(".validateTips").show();
		return false;
	} else {
		return true;
	}
}

function checkRegexp( o, regexp, n ) {
	if ( !( regexp.test( o.val() ) ) ) {
		o.addClass( "ui-state-error" );
		updateTips( n );
		$(".validateTips").show();
		return false;
	} else {
		return true;
	}
}