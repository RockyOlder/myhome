<?php
/*
 * 技术ROOT，及根目录
 * 定义调试模式
 * 引入最基础的类
 这是初始化文件 
 作用：框架初始化
 */
//过滤参数,用递归的方式$_post,$get
//设置报错级别
//初始化当前的绝对路径
//换成正斜线是因为win/linux都支持正斜线，而linux不支持反斜线



// 初始化当前的绝对路径
// 换成正斜线是因为 win/linux都支持正斜线,而linux不支持反斜线

defined('ACC')||exit('ACC Denied');


define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))) . '/');
define('DEBUG',true);


require(ROOT . 'include/lib_base.php');

function __autoload($class) {
    if(strtolower(substr($class,-5)) == 'model') {
        require(ROOT . 'Model/' . $class . '.class.php');
    }else if (strtolower(substr($class,-4)) == 'tool'){
    	 require(ROOT . 'tool/' . $class . '.class.php');
    } else {
        require(ROOT . 'include/' . $class . '.class.php');
    }
}



// 过滤参数,用递归的方式过滤$_GET,$_POST,$_COOKIE,
$_GET = _addslashes($_GET);
$_POST = _addslashes($_POST);
$_COOKIE = _addslashes($_COOKIE);

//开启session

//if(!isset($_SESSION)){ session_start(); }



// 设置报错级别


if(defined('DEBUG')) {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

