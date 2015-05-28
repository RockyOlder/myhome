<?php

namespace Home\Model;

use Think\Model;

class VipActivityModel extends Model {

//	protected $tableName = 'admin'; 
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
        array('title', 'require', '活动标题不能为空！'),
        array('start_time', 'require', '开始时间不能为空！'),
        array('end_time', 'require', '结束时间不能为空！'),
    );

}

?>
