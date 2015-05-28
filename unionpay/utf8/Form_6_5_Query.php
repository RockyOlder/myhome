<?php
header ( 'Content-type:text/html;charset=utf-8' );
 include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/common.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/SDKConfig.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/secureUtil.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/httpClient.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/log.class.php';

/**
 *	查询交易
 */

/**
 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
 */
$order = isset($_REQUEST['orderNo'])?$_REQUEST['orderNo']:0;
if (!$order) {
	echo "请传入订单号";
}
$sql = "select * from wrt_union where order_id =".$order;
// var_dump($sql)
$info = getInfo($sql);
// var_dump($info);
// die();

// 初始化日志
$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
$log->LogInfo ( "===========处理后台请求开始============" );

$params = array(
		'version' => '5.0.0',		//版本号
		'encoding' => 'utf-8',		//编码方式
		'certId' => getSignCertId (),	//证书ID	
		'signMethod' => '01',		//签名方法
		'txnType' => '73',		//交易类型	
		'txnSubType' => '01',		//交易子类
		'bizType' => '000201',		//业务类型
		'accessType' => '0',		//接入类型
		'channelType' => '07',		//渠道类型
		'orderId' => $info['order_id'],	//请修改被查询的交易的订单号
		'merId' => '777290058111477',	//商户代码，请修改为自己的商户号
		'txnTime' => date('YmdHis',$info['time']),	//请修改被查询的交易的订单发送时间
		'queryId'=>$info['query_id'],
		'billType'=>"U1",
		'billNo'=>$info['order_id'],
		'districtCode'=>"N4"
	);

// 签名
sign ( $params );

echo "请求：" . getRequestParamString ( $params );
$log->LogInfo ( "后台请求地址为>" . SDK_SINGLE_QUERY_URL );
// 发送信息到后台
$result = sendHttpRequest ( $params, SDK_SINGLE_QUERY_URL );
$log->LogInfo ( "后台返回结果为>" . $result );

echo "应答：" . $result;

//返回结果展示
$result_arr = coverStringToArray ( $result );
// var_dump($result_arr);
echo verify ( $result_arr ) ? '验签成功' : '验签失败';
?>

