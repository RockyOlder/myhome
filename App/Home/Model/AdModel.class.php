<?php

namespace Home\Model;

use Think\Model;

class AdModel extends Model {

    protected $_validate = array(
        array('ad_name', 'require', '广告名称不能为空！'),
        array('ad_name', '', '出错啦！广告名称已经存在！', 0, 'unique', 1),
        array('ad_url', 'require', '链接地址不能为空！'), //验证密码
        array('start_time', 'require', '开始时间不能为空！'),
        array('end_time', 'require', '结束时间不能为空！'),
    );

}

?>
