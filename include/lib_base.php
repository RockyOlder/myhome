<?php
//这是一个三维数组
//递归对数组进行转义
//先写一个一维数组的转义函数

//递归转义数组


function _addslashes($arr){
	foreach($arr as $k=>$v){
		if (is_string($v)){
			$arr[$k]=addslashes($v);
		}else if (is_array($v)){//先写一个一维的函数，再加判断，如果是数组，调用自身，在转义
			$arr[$k]=_addslashes($v);
		}
	}
	return $arr;
}