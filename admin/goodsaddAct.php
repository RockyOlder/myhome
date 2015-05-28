<?php
define('ACC',true);
require('../include/init.php');


//
$goods= new GoodsModel();
$_POST['goods_weight']*=$_POST['weight_unit'];

$data=array();
$data=$goods->_facade($_POST);//自动过滤
$data=$goods->_autoFill($data);//自动填充
//print_r($data);exit;
//添加自动商品货号
if (empty($data['goods_sn'])){
	$data['goods_sn']=$goods->createSn();
}


if(!$goods->_validate($data)) {
    echo '数据不合法<br />';
    echo implode(',',$goods->getErr());
}


//data 上传图片
$uptool = new UpTool();
$ori_img = $uptool->up('ori_img');

if($ori_img) {
    $data['ori_img'] = $ori_img;
}
//如果$ori_img上传成功。在次生成中等大小缩略图 300*400
//根据原始地址 定  中等图缩略地址
if ($ori_img){
$ori_img=ROOT.$ori_img;//加上绝对路径
$goods_img=dirname($ori_img) . '/goods_' . basename($ori_img);
if (ImageTool::thumb($ori_img,$goods_img,300,400)){
	$data['goods_img']=str_replace(ROOT,'',$goods_img);
}
//在次生成浏览时用的缩略图 160*220
//定好缩略图的地址
//
//加上绝对路径
$thumb_img=dirname($ori_img) . '/thumb_' . basename($ori_img);
if (ImageTool::thumb($ori_img,$thumb_img,160,220)){
	$data['thumb_img']=str_replace(ROOT,'',$thumb_img);
}
}
if ($goods->add($data)){
	echo '商品发布成功';
}else{
	echo '商品发布失败';
}

?>