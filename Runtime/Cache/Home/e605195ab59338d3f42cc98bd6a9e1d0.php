<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link  href="/myhome/App/Home/View/Public/Css/style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="/myhome/App/Home/View/Public/Js/jquery.js"></script>

<script type="text/javascript">
$(function(){	
	//导航切换
	$(".menuson li").click(function(){
		$(".menuson li.active").removeClass("active")
		$(this).addClass("active");
	});
	
	$('.title').click(function(){
		var $ul = $(this).next('ul');
		$('dd').find('ul').slideUp();
		if($ul.is(':visible')){
			$(this).next('ul').slideUp();
		}else{
			$(this).next('ul').slideDown();
		}
	});
})	
</script>


</head>

<body style="background:#f0f9fd;">
	<div class="lefttop"><span></span>系统导航</div>
    
    <dl class="leftmenu">
        
     <?php if(is_array($menu)): $k = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><dd>
    <div class="title">
  
    <span><img src="/myhome/App/Home/View/Public/Images/left/<?php echo ($k); ?>.png" /></span><?php echo ($vo["title"]); ?>
    </div>
    	<ul class="menuson">
            <?php if(is_array($vo['son'])): $i = 0; $__LIST__ = $vo['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;?><li><cite></cite><a href="<?php echo U($s['name'],'','');?>" target="rightFrame"><?php echo ($s["title"]); ?></a><i></i></li><?php endforeach; endif; else: echo "" ;endif; ?> 
       
        </ul>    
    </dd><?php endforeach; endif; else: echo "" ;endif; ?>    
    
    </dl>
    
</body>
</html>