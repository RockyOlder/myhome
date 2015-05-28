<?php
define('ACC',true);
require('../include/init.php');

/*
 * 思路
 * 实例化 GoodsModel
 * 调用select 方法
 */
$goods= new GoodsModel();
$goodslist=$goods->getGoods();

include(ROOT . 'view/admin/templates/goodslist.html');