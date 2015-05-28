<?php
return array(
	//'配置项'=>'配置值'
	"SHOW_PAGE_TRACE"=>false,
	//应用类库不再需要使用命名空间
    // 'APP_USE_NAMESPACE'    =>    false, 
    'LOAD_EXT_CONFIG' => 'user,db,three',
    'URL_MODEL'=>3,    
    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
	'DB_HOST'   => '127.0.0.1', // 服务器地址
   // 'DB_HOST'   => '127.0.0.1,120.24.214.88', // 服务器地址
    'DB_NAME'   => 'admin_lsj', // 数据库名
    'DB_USER'   => 'root', // 用户名
  //  'DB_PWD'    => '5ae5ee52b8,wrtcloud888',  // 密码
    'DB_PWD'    => '',  // 密码//5ae5ee52b8
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'wrt_', // 数据库表前缀
  //  'DB_DEPLOY_TYPE'=>1,//开启分布式数据库   
   // 'DB_RW_SEPARATE'=>ture,//读写分离，默认第一台服务器为写入服务器，其它的只读取不写入 
    "BAIDU_MAP_APK" =>"8a7dcea6fe73b77b3c5f6d70d1cc453c",
    'web_copy'=>'关注我们：    版权所有 © 慧锐通智能科技股份有限公司 2014-'.date('Y',time()).'。保留一切权利。粤ICP备15006701号-1',//携程数据是否输出错误
    'CU_DEVELOP'=>false,
    'LOG_TYPE'  =>"Sql",// 日志记录类型 默认为文件方式
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR',
    'android_key'=>"EcGxuSUSAwLCWPEAmEgGpZmA",//百度推送android的密钥
    'android_secret' => "t1AZqO6FAwOK8X7GfHXUEv5mWGQXvKl6",
    'ios_key'=>"q0TvB87y80jU4k4UZvQfz5Ma",//百度推送ios的密钥
    'ios_secret' => "CYuWeSEvztRDxau4mWAj4FNN9WBS5BW8",
    //验证码发送短信消息
    'msg_start'=> "您好！您的短信校验码是：",
    'msg_end'=>"，请尽快按页面提示提交校验码，过期无效。请勿向任何人提供您收到的短信校验码。【慧享园】",
            

);