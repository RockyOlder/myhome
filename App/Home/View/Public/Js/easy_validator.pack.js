/*
	Copyright (C) 2009 - 2012
	WebSite:	Http://wangking717.javaeye.com/
	Author:		wangking
*/
$(function(){
	var xOffset = -20; // x distance from mouse
    var yOffset = 20; // y distance from mouse  
	
	//input tips
	$("[reg],[url]:not([reg])").hover(
		function(e) {
			if($(this).attr('tip') != undefined){
				var top = (e.pageY + yOffset);
				var left = (e.pageX + xOffset);
				$('body').append( '<p id="vtip"><img id="vtipArrow" src="Mobile/View/Public/images/vtip_arrow.png"/>' + $(this).attr('tip') + '</p>' );
				$('p#vtip').css("top", top+"px").css("left", left+"px");
			}
		},
		function() {
			if($(this).attr('tip') != undefined){
				$("p#vtip").remove();
			}
		}
	).mousemove(
		function(e) {
			if($(this).attr('tip') != undefined){
				var top = (e.pageY + yOffset);
				var left = (e.pageX + xOffset);
				$("p#vtip").css("top", top+"px").css("left", left+"px");
			}
		}
	).blur(function(){
		if($(this).attr("reg") == undefined){
			ajax_validate($(this),this.tagName);
		}else{
			validate($(this),this.tagName);
		}
	});
	
	$("form").submit(function(){
		var isSubmit = true;
		$(this).find("[reg],[url]:not([reg])").each(function(){
			if($(this).attr("reg") == undefined){
				if(!ajax_validate($(this),this.tagName)){
					isSubmit = false;
				}
			}else{
				if(!validate($(this),this.tagName)){
					isSubmit = false;
				}
			}
		});
		return isSubmit;
	});
	
});

function validate(obj,tagName){
	var reg = new RegExp(obj.attr("reg"));
	var objValue = obj.attr("value");
	if(!reg.test(objValue)){
		change_error_style(obj,tagName,"add");
		if(obj.attr("is_tip_null") == "yes"){
			obj.removeAttr("tip");
			obj.removeAttr("tip_bak");
		}else{
			obj.attr("tip",obj.attr("tip_bak"));
			obj.removeAttr("tip_bak");
		}
		return false;
	}else{
		if(obj.attr("url") == undefined){
			obj.attr("tip",obj.attr("tip_bak"));
			obj.removeAttr("tip_bak");
			change_error_style(obj,tagName,"remove");
			return true;
		}else{
			return ajax_validate(obj);
		}
	}
}

function ajax_validate(obj,tagName){
	if(obj.attr("tip") == undefined){
		obj.attr("is_tip_null","yes");
	}
	var url_str = obj.attr("url");
	if(url_str.indexOf("?") != -1){
		url_str = url_str+"&"+obj.attr("name")+"="+obj.attr("value");
	}else{
		url_str = url_str+"?"+obj.attr("name")+"="+obj.attr("value");
	}
	var feed_back = $.ajax({url: url_str,cache: false,async: false}).responseText;
	feed_back = feed_back.replace(/(^\s*)|(\s*$)/g, "");
	if(feed_back == 'success'){
		change_error_style(obj,tagName,"remove");
		if(obj.attr("is_tip_null") == "yes"){
			obj.removeAttr("tip");
			obj.removeAttr("tip_bak");
		}else{
			obj.attr("tip",obj.attr("tip_bak"));
			obj.removeAttr("tip_bak");
		}
		return true;
	}else{
		change_error_style(obj,tagName,"add");
		if(obj.attr("tip_bak") == undefined){
			obj.attr("tip_bak",obj.attr("tip"));
			obj.attr("tip",feed_back);
		}
		return false;
	}
}

function change_error_style(obj,tagName,action_type){
	if(action_type == "add"){
		if(tagName == "SELECT"){
			obj.addClass("select_validation-failed");
		}else{
			obj.addClass("input_validation-failed");
		}
	}else{
		if(tagName == "SELECT"){
			obj.removeClass("select_validation-failed");
		}else{
			obj.removeClass("input_validation-failed");
		}
	}
}