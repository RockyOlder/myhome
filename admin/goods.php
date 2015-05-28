<?php
define('ACC',true);
require('../include/init.php');
/*
 * 思路
 * 接受goods_id
 * 实例化goodsModel
 * 调用find方法
 * 展示商品信息
 */

//include(ROOT . 'view/admin/templates/index.html');
$goods_id=$_GET['goods_id']+0;
$goods=new GoodsModel();
$g=$goods->find($goods_id);
if (empty($g)){
	echo '商品不存在';
}
print_r($g);