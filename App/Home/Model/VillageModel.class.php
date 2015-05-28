<?php

namespace Home\Model;

use Think\Model;

class VillageModel extends Model {

//	protected $tableName = 'admin'; 
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
        //array('email', 'require', '名称不能为空！'),
        array('name', 'require', '小区名字不能为空！'), //验证姓名
        array('province', 'require', '省市不能为空！'), //省市
        array('city', 'require', '市区不能为空！'), //市区
        array('area', 'require', '区县不能为空！'), //区县
        array('house_id', 'require', '所属楼盘不能为空！'), //验证姓名
        array('property_id', 'require', '所属物业不能为空！'), //验证姓名
    );

}

?>
