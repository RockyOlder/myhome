﻿<?php

header ( 'Content-type:text/html;charset=utf-8' );
// echo '<pre>';

// var_dump($_SERVER["DOCUMENT_ROOT"]);die();
// echo '</pre>';
define('ROOT', $_SERVER["DOCUMENT_ROOT"]);
// var_dump(ROOT);
// die(ROOT.'/unionpay/PM_700000000000001_acp.pfx');
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/common.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/SDKConfig.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/secureUtil.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/httpClient.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/log.class.php';
/**
 * 消费交易-控件后台订单推送 
 */
// var_dump(getSignCertId ());
$time = time();
$order = isset($_REQUEST['orderNo'])?$_REQUEST['orderNo']:0;
if (!$order) {
	echo "请传入订单号";
}
$sql = "select * from wrt_order where number =".$order;
$info = getInfo($sql);
$money = (string)intval($info['totle'] * 100);
// var_dump($info);
if ($info['type'] == 1) {
	$des= "用户充值订单";
}else if($info['cate'] == 0){
	$des= "生活服务订单";
}else{
	$des= "VIP特享订单";
}

// var_dump($data['totle'] * 100);die();
/**
 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
 */
// 初始化日志
// echo "<pre>";
$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
$log->LogInfo ( "============处理前台请求开始===============" );
// 初始化日志
$params = array(
		// 'version' => '5.0.0',				//版本号
		// 'encoding' => 'UTF-8',				//编码方式
		// 'certId' => getSignCertId (),			//证书ID
		// 'txnType' => '01',				//交易类型	
		// 'txnSubType' => '01',				//交易子类
		// 'bizType' => '000201',				//业务类型
		// 'frontUrl' =>  SDK_FRONT_NOTIFY_URL,  		//前台通知地址，控件接入的时候不会起作用
		// 'backUrl' => SDK_BACK_NOTIFY_URL,		//后台通知地址	
		// 'signMethod' => '01',		//签名方法
		// 'channelType' => '08',		//渠道类型，07-PC，08-手机
		// 'accessType' => '0',		//接入类型
		// 'merId' => '777290058111477',	//商户代码，请改自己的测试商户号
		// 'orderId' => $order,	//商户订单号，8-40位数字字母
		// 'txnTime' => date('YmdHis'),	//订单发送时间
		// 'txnAmt' => '1',		//交易金额，单位分
		// 'currencyCode' => '156',	//交易币种
		// 'orderDesc' => '订单描述',  //订单描述，可不上送，上送时控件中会显示该信息
		// 'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		'version' => '5.0.0',				//版本号
		'encoding' => 'utf-8',				//编码方式
		'certId' => getSignCertId (),			//证书ID
		'txnType' => '01',				//交易类型	
		'txnSubType' => '01',				//交易子类
		'bizType' => '000201',				//业务类型
		'frontUrl' =>  SDK_FRONT_NOTIFY_URL,  		//前台通知地址，控件接入的时候不会起作用
		'backUrl' => SDK_BACK_NOTIFY_URL,		//后台通知地址	
		'signMethod' => '01',		//签名方法
		'channelType' => '08',		//渠道类型，07-PC，08-手机
		'accessType' => '0',		//接入类型
		'merId' => '898111457340127',	//商户代码，请改自己的测试商户号
		'orderId' => $order,	//商户订单号，8-40位数字字母
		'txnTime' => date('YmdHis',$time),	//订单发送时间
		// 'txnAmt' => (string)intval($data['totle'] * 100),		//交易金额，单位分
		'txnAmt'=>$money,
		// 'txnAmt'=>'100',
		'currencyCode' => '156',	//交易币种
		'orderDesc' => $des,  //订单描述，可不上送，上送时控件中会显示该信息
		//'reqReserved' =>' 透传信息', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现

		);

// var_dump($params['txnAmt']);
// 签名
sign ( $params );
// echo "请求：" . getRequestParamString ( $params );
$log->LogInfo ( "后台请求地址为>" . SDK_App_Request_Url );
// 发送信息到后台
$result = sendHttpRequest ( $params, SDK_App_Request_Url );
$log->LogInfo ( "后台返回结果为>" . $result );

// echo "应答：" . $result;
// $result = json_decode($result,true);
// var_dump(expression)
//返回结果展示
$result_arr = coverStringToArray ( $result );
// explode(delimiter, string)
// echo isset ($result_arr['tn'] ) ? $result_arr['tn'] : '验签失败';
if ( isset ($result_arr['tn'] )) {
	$sql = 'insert into wrt_union set order_id="'.$order.'",time='.$time.",union_id='".$result_arr['tn']."'";
	addInfo($sql);
	echo $result_arr['tn'];
	
}else{
	echo "验签失败";
}
// json_encode(value)
// var_dump(isset ($result_arr['tn'] ) ? $result_arr['tn'] : '验签失败') ;
// echo verify ( $result_arr ) ? '验签成功' : '验签失败';
// echo "</pre>";
?>

