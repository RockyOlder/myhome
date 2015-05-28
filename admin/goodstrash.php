<?php
define('ACC',true);
require('../include/init.php');

/*
 * 接受goods_id
 * 调用trash方法
 */
if (isset($_GET['act'])&& $_GET['act']=='show'){
	//这个部分是打印所以的回事商品
	$goods= new GoodsModel();
	$goodslist=$goods->getTrash();
	include(ROOT . 'view/admin/templates/goodslist.html');
}else{
$goods_id=$_GET['goods_id']+0;
$goods= new GoodsModel();
if ($goods->trash($goods_id)){
	echo '成功';
}else{
	echo '失败';
}
}