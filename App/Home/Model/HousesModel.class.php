<?php

namespace Home\Model;

use Think\Model;

class HousesModel extends Model {

//	protected $tableName = 'admin'; 
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
        array('name', 'require', '楼盘名字不能为空！'),
        array('name', '', '出错啦！楼盘名字已经存在！', 0, 'unique', 1),
        array('info', 'require', '楼盘信息不能为空！'),
        array('developer_id', 'require', '所属开发商不能为空！'), //验证密码
        array('start_time', 'require', '开始时间不能为空！'),
        array('end_time', 'require', '结束时间不能为空！'),
    );

}

?>
