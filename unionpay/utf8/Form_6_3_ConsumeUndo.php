<?php
header ( 'Content-type:text/html;charset=utf-8' );
 include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/common.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/SDKConfig.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/secureUtil.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/httpClient.php';
include_once $_SERVER ['DOCUMENT_ROOT'] . '/unionpay/utf8/func/log.class.php';

/**
 *	消费撤销
 */

$order = isset($_REQUEST['orderNo'])?$_REQUEST['orderNo']:0;
if (!$order) {
	echo "请传入订单号";
}
$sql = "select * from wrt_union_cancle where number =".$order;
$info = getInfo($sql);
// var_dump($info);
/**
 *	以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考
 */

// 初始化日志
$log = new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
$log->LogInfo ( "===========处理后台请求开始============" );

$params = array(
		'version' => '5.0.0',		//版本号
		'encoding' => 'utf-8',		//编码方式
		'certId' => getSignCertId (),	//证书ID	
		'signMethod' => '01',		//签名方法
		'txnType' => '31',		//交易类型	
		'txnSubType' => '00',		//交易子类
		'bizType' => '000301',		//业务类型
		'accessType' => '0',		//接入类型
		'channelType' => '08',		//渠道类型
		'orderId' => $info['c_number'],	//商户订单号，重新产生，不同于原消费
		'merId' => '898111457340127',			//商户代码，请改成自己的测试商户号
		'origQryId' => $info['query_id'],    //原消费的queryId，可以从查询接口或者通知接口中获取
		'txnTime' => date('YmdHis'),	//订单发送时间，重新产生，不同于原消费
		'txnAmt' => (string)intval($info['totle'] * 100),              //交易金额，消费撤销时需和原消费一致
		// 'txnAmt'=>'100',
		'backUrl' => SDK_BACK_NOTIFY_URL,	   //后台通知地址	
		'reqReserved' =>' 消费撤销', //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		'currencyCode' => '156',	//交易币种
	);


// 签名
sign ( $params );

echo "请求：" . getRequestParamString ( $params );
$log->LogInfo ( "后台请求地址为>" . SDK_BACK_TRANS_URL );
// 发送信息到后台
$result = sendHttpRequest ( $params, SDK_BACK_TRANS_URL );
$log->LogInfo ( "后台返回结果为>" . $result );
// echo "<pre>";
echo "应答：" . $result;
// echo "</pre>";

//返回结果展示
$result_arr = coverStringToArray ( $result );

echo verify ( $result_arr ) ? '验签成功' : '验签失败';
?>

