<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Think\Log\Driver;

class Sql {

    protected $config  =   array(
        'log_time_format'   =>  ' c ',
        'log_path'          =>  '',
    );

    // 实例化并传入参数
    public function __construct($config=array()){
        $this->config   =   array_merge($this->config,$config);
    }

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination  写入目标
     * @return void
     */
    public function write($log,$destination='') {
        
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        $arr = array(
            'time'=>time(),
            'ip'=>$_SERVER['REMOTE_ADDR'],
            'url'=>$_SERVER['REQUEST_URI'],
            'error'=>$log
            );
        M('error_log')->add($arr);
    }
}
