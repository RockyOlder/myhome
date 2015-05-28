// JavaScript Document
$(function(){
	$( document ).tooltip({
		track: true,
		position: { my: "left+5 bottom-5", at: "center top" }
	});
	$( "#accordion1" ).accordion({
		collapsible: true,
		heightStyle: "content"
	});
	$(".btn").button();	
	$(".menu,.submenu,.action").bind("click",function(){
		
		if($(this).find("span").hasClass("enable")){
			changePriv($(this),"disable");
		}else if($(this).find("span").hasClass("disable")){
			changePriv($(this),"enable");
		}	
		return false;
	});
	$( "#tabs" ).tabs();
	$.ajax({ 
		url: $("#url_getRolePriv").val(),
		type:"post",
		dataType:"json",
		cache:false,
		data: {"role_id":$("#role_id").val()},
		timeout:30000,
		error:function(data, msg){
			alert("error:"+msg);
		},
		success:function(data){
			jsonRolePriv=data;
			$("#jsonRolePriv").val(JSON.stringify(jsonRolePriv));
			//alert(JSON.stringify(jsonRolePriv));
			//alert(data);
			resetData();
		}
	});
});

var tipEnable="已启用，点击禁用";
var tipDisable="已禁用，点击启用";
var jsonRolePriv='{}';

function formSubmit(){
	$.ajax({ url: $("#url_savePriv").val(),
		type:"post",
		dataType:"json",
		cache:false,
		data: {"role_id":$("#role_id").val(),"jsonRolePriv":JSON.stringify(jsonRolePriv)},
		timeout:30000,
		error:function(data, msg){
			alert("error:"+msg);
		},
		success:function(data){
			$("#jsonRolePriv").val(JSON.stringify(jsonRolePriv));
			//alert(JSON.stringify(data));
			alert('修改成功！');
		}
	});
}

//改变权限，包括显示状态及值
function changePriv(priv,privValue){
	var $priv=typeof priv=="string"?$("#"+priv):priv instanceof jQuery?priv:$(priv);	
	var $privValue=privValue=="disable"?"0":privValue=="enable"?"1":privValue;
	if($priv.hasClass("action")){
		if($privValue=="1"){
			$menu =$priv.parentsUntil(".submenus").parent().prev().find(".menu");
			$subMenu = $priv.parentsUntil("tr").parent().find(".submenu");
			changePrivState($menu,$privValue);
			changePrivValue($menu,$privValue);
			changePrivState($subMenu,$privValue);
			changePrivValue($subMenu,$privValue);
		}
	}else if($priv.hasClass("submenu")){
		$menu = $priv.parentsUntil(".submenus").parent().prev().find(".menu");
		$actions = $priv.parentsUntil("tr").parent().find(".action");
		if($privValue=="1"){
			changePrivState($menu,$privValue);
			changePrivValue($menu,$privValue);
		}
		$actions.each(function(){
			changePrivState($(this),$privValue);
			changePrivValue($(this),$privValue);
		});
	}else if($priv.hasClass("menu")){
		$priv.parent().next().find(".submenu,.action").each(function(){
			changePrivState($(this),$privValue);
			changePrivValue($(this),$privValue);
		});
	}
	changePrivState($priv,$privValue);
	changePrivValue($priv,$privValue);
}
//改变权限显示状态
function changePrivState(priv,privValue){
	var $priv=typeof priv=="string"?$("#"+priv):priv instanceof jQuery?priv:$(priv);	
	var $privValue=privValue=="disable"?"0":privValue=="enable"?"1":privValue;
	var title=tipDisable;
	if($privValue=="" || $privValue=="0"){
		title=$priv.hasClass("menu")||$priv.hasClass("submenu")?"已禁用，点击全部启用":tipDisable;
		$priv.find("span").removeClass().addClass("disable");
		setTitle($priv,title);
	}else if($privValue=="1"){
		title=$priv.hasClass("menu")||$priv.hasClass("submenu")?"已启用，点击全部禁用":tipEnable;
		$priv.find("span").removeClass().addClass("enable");
		setTitle($priv,title);
	}
}
//改变权限值
function changePrivValue(priv,privValue){
	var $priv=typeof priv=="string"?$("#"+priv):priv instanceof jQuery?priv:$(priv);	
	var $privValue=privValue=="disable"?"0":privValue=="enable"?"1":privValue;
	var jsonRolePriv_bak=$.parseJSON($("#jsonRolePriv").val());
	if($priv.hasClass("action")){
		$menu =$priv.parentsUntil(".submenus").parent().prev().find(".menu");
		$subMenu = $priv.parentsUntil("tr").parent().find(".submenu");
		jsonRolePriv[$menu.attr("id")][$subMenu.attr("id")][$priv.attr("id")]["operation"]=$privValue;
		if(jsonRolePriv_bak && $privValue!=jsonRolePriv_bak[$menu.attr("id")][$subMenu.attr("id")][$priv.attr("id")]["operation"]){
			jsonRolePriv[$menu.attr("id")][$subMenu.attr("id")][$priv.attr("id")]["changed"]=true;
		}else{
			delete jsonRolePriv[$menu.attr("id")][$subMenu.attr("id")][$priv.attr("id")].changed;
		}
	}else if($priv.hasClass("submenu")){
		$menu = $priv.parentsUntil(".submenus").parent().prev().find(".menu");
		jsonRolePriv[$menu.attr("id")][$priv.attr("id")]["operation"]=$privValue;
		if(jsonRolePriv_bak && $privValue!=jsonRolePriv_bak[$menu.attr("id")][$priv.attr("id")]["operation"]){
			jsonRolePriv[$menu.attr("id")][$priv.attr("id")]["changed"]=true;
		}else{
			delete jsonRolePriv[$menu.attr("id")][$priv.attr("id")].changed;
		}
	}else if($priv.hasClass("menu")){
		jsonRolePriv[$priv.attr("id")]["operation"]=$privValue;
		if(jsonRolePriv_bak && $privValue!=jsonRolePriv_bak[$priv.attr("id")]["operation"]){
			jsonRolePriv[$priv.attr("id")]["changed"]=true;
		}else{
			delete jsonRolePriv[$priv.attr("id")].changed;
		}
	}
}
//重置数据
function resetData(){
	var jsonRolePriv_tmp = $("#jsonRolePriv").val();
	jsonRolePriv_tmp = $.parseJSON(jsonRolePriv_tmp);
	setData(jsonRolePriv_tmp);
}
//批量设置数据
function setData(jsonObj,privId,prefix){
	for(priv in jsonObj){	
		if(typeof jsonObj[priv]=="string"){
			changePrivState(prefix?prefix+privId:privId,jsonObj[priv]);
		}else{
			setData(jsonObj[priv],priv,prefix);
		}
	}
}