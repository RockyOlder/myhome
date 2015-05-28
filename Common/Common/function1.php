<?php 
// 对密码进行加密
function change($salt="",$password=''){
    // dump($salt);
	if ($password == '') {
// 		dump($_POST['password']);
		$password = md5($_POST['password']);
	}	
	// dump(!empty($salt));
	if (!empty($salt)) {
// 		var_dump(md5($salt.$password));die;
		return md5($salt . $password);
	}else {
		return $password;
	}
}
// 讲数组转化为字符串
function formant($arr ){
    // dump($_POST);
    $k=array_keys($arr);
    // dump($k);
    $v = array_values($arr);
    // dump($v);
    $sk = "";
    foreach ($k as $k1 => $v1) {
        // dump($v1);
        // dump($v[$k]);
        $sk .= "`".$v1."`='".$v[$k1]."',";
    }
    $sk = substr($sk, 0,-1);
    // dump($sk);die();
    return $sk;
}

//计算两个经纬度之间的距离
function getdistance($lng1,$lat1,$lng2,$lat2){
	//将角度转为狐度
	$radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
	$radLat2=deg2rad($lat2);
	$radLng1=deg2rad($lng1);
	$radLng2=deg2rad($lng2);
	$a=$radLat1-$radLat2;
	$b=$radLng1-$radLng2;
	$s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
	return $s;
}

//推送android设备消息
function test_pushMessage_android ($title,$des)
{
	include_once("../../baidu/Channel.class.php");
    $apiKey = "yQHRf415WcePs7REO70dTuyy";
    $secretKey = "G67RrUpM3YNftsYmA7u4G3chgMMZjFAH";

 //    global $apiKey;
    // global $secretKey;
    $channel = new Channel ( $apiKey, $secretKey ) ;
    //推送消息到某个user，设置push_type = 1; 
    //推送消息到一个tag中的全部user，设置push_type = 2;
    //推送消息到该app中的全部user，设置push_type = 3;
    $push_type = 3; //推送单播消息
    // $optional[Channel::USER_ID] = $user_id; //如果推送单播消息，需要指定user
    //optional[Channel::TAG_NAME] = "xxxx";  //如果推送tag消息，需要指定tag_name
    //指定发到android设备
    $optional[Channel::DEVICE_TYPE] = 3;
    //指定消息类型为通知
    $optional[Channel::MESSAGE_TYPE] = 1;
    //通知类型的内容必须按指定内容发送，示例如下：
    // $message = '{ 
    //         "title": "'.$title.'",
    //         "description": "open url",
    //         "notification_basic_style":7,
    //         "open_type":1,
    //         "url":"http://www.baidu.com"
    //     }'; 
    //通知必须按以下格式指定
    $message = '{ 
    "title": "'.$title.'",
    "description": "'.$des.'"
    }';
    // var_dump($message);die;
    $message_key = "msg_key";
    $ret = $channel->pushMessage ( $push_type, $message, $message_key, $optional );
    // $message = '{ 
    // "title": "title",
    // "description": "description"
    // }';
    // $message_key = "msg_key";
    // $ret = $channel->pushMessage ( $push_type, $message, $message_key, $optional ) ;
    // $ret = $channel->pushMessage($push_type, $messages, $message_keys);
    // if ( false === $ret )
    // {
    //     // error_output ( 'WRONG, ' . __FUNCTION__ . ' ERROR!!!!!' ) ;
    //     // error_output ( 'ERROR NUMBER: ' . $channel->errno ( ) ) ;
    //     // error_output ( 'ERROR MESSAGE: ' . $channel->errmsg ( ) ) ;
    //     // error_output ( 'REQUEST ID: ' . $channel->getRequestId ( ) );
    //     // echo "NO";

    // }
    // else
    // {
    //     // right_output ( 'SUCC, ' . __FUNCTION__ . ' OK!!!!!' ) ;
    //     // right_output ( 'result: ' . print_r ( $ret, true ) ) ;
    //     // echo "OK";
    // }
}
function Post($data, $target) {
    $url_info = parse_url($target);
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader .= "Host:" . $url_info['host'] . "\r\n";
    $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
    $httpheader .= "Connection:close\r\n\r\n";
    //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
    $httpheader .= $data;

    $fd = fsockopen($url_info['host'], 80);
    fwrite($fd, $httpheader);
    $gets = "";
    while(!feof($fd)) {
        $gets .= fread($fd, 128);
    }
    fclose($fd);
    if($gets != ''){
        $start = strpos($gets, '<?xml');
        if($start > 0) {
            $gets = substr($gets, $start);
        }        
    }
    return $gets;
}
//短信发送接口调用方法
function sendMsg($phone,$content){
    $target = "http://cf.lmobile.cn/submitdata/Service.asmx/g_Submit";
    // dump($phone);
    // dump($content);
    //替换成自己的测试账号,参数顺序和wenservice对应
    $post_data = "sname=dlszhxy8&spwd=9vXG0bmf&scorpid=&sprdid=1012818&sdst=$phone&smsg=".rawurlencode($content);
    // "sname=kwsm&spwd=kwsm&scorpid=&sprdid=101&sdst=13910862579&smsg=".rawurlencode("短信内容")

    return  Post($post_data, $target);

}

function _vialg($value) {

    $sbust = implode(",", $value);
    $word = D('ProKeyword');
    $wordlist = $word->where("id=1")->find();
    $list = json_decode($wordlist['pname']);
    foreach ($list as $v) {
        if (strstr($sbust, $v)) {
            $res = $v . "是关键字";
        }
    }
    return $res;
}

function maskWord($str) {
    $word = D('ProKeyword');
    $wordlist = $word->where("id=1")->find();
    $list = json_decode($wordlist['pname']);
    // dump($list);die();
    foreach ($list as $v) {
        $str = str_replace($v, '**', $str);
    }
    return $str;
}

