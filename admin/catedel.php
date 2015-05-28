<?php



// 栏目的删除页面

/**
思路:
接收cat_id

调用model

删除cat_id
**/


define('ACC',true);
require('../include/init.php');


$cat_id = $_GET['cat_id'] + 0;
$cat = new CatModel();
$sons=$cat->gesSon($cat_id);
if (!empty($sons)){
	exit('有子栏目,不允许删除');
}



if($cat->delete($cat_id)) {
    echo '删除成功';
} else {
    echo '删除失败';
}

