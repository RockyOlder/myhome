<?php 
// 检查用户是否有权限访问
function checkpermission(){
	$go=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
    $auth = new \Think\Auth();
    $arr=array('Home/Index/index','Home/Help/index','Home/Index/left','Home/Index/top','Home/Index/start','Home/Index/loginout','Home/System/setting');
    if (session('admin.name') == 'admin' || in_array($go, $arr)) 
    { //判断是否是超级管理员则可以越过判断
            return true ;
    }else{
	    $check = $auth->check($go, session('admin.id'));
	   	
	    return $check;
    }
}

function formantpost(){
	$_POST['pass_time'] = strtotime($_POST['pass_time']);
	// dump($_POST);
	$k=array_keys($_POST);
	// dump($k);
	$v = array_values($_POST);
	// dump($v);
	$sk = "";
	foreach ($k as $k1 => $v1) {

		if ($k1<3) {			
			continue;
		}
		// dump($v1);
		// dump($v[$k]);
		$sk .= $v1."='".$v[$k1]."',";
	}
	$sk = substr($sk, 0,-1);
	// dump($sk);die();
	return $sk;
}

/**
 *	记录管理员的操作日志
 * @author xujun
 * @email  [jun0421@163.com]
 * @date   2015-01-07T20:36:20+0800
 * @param  [type]                   $info [description]
 * @return [type]                         [description]
 */
function admin_log($info){
    $arr = array(
        'admin_id' => session('admin.id'),
        'admin_name' =>session('admin.name'),
        'time' =>time(),
        'ip'=>get_client_ip(),
        'info'=>$info,
        );
    M('admin_log')->add($arr);

}

/**
  * 格式化字节大小
  * @param  number $size      字节数
  * @param  string $delimiter 数字和单位分隔符
  * @return string            格式化后的带单位的大小
  */
 function format_bytes($size, $delimiter = '') {
     $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
     for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
     return round($size, 2) . $delimiter . $units[$i];
 }