<?php

namespace Home\Model;

use Think\Model;

class PropertyModel extends Model {

//	protected $tableName = 'admin'; 
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
        array('pname', 'require', '物业名称不能为空！'), //验证姓名
        array('pname','','出错啦！物业名称已经存在！',0,'unique',1),
        array('address', 'require', '详细地址不能为空！'), //验证地址
        array('province', 'require', '省市不能为空！'), //省市
        array('city', 'require', '市区不能为空！'), //市区
        array('area', 'require', '区县不能为空！'), //区县
        //   array('phone', 'require', '电话不能为空！'), //验证姓名
        array('phone', '/\d{3}-\d{8}|\d{4}-\d{7}/', '电话格式不正确！', 2), //验证电话
        array('manager', 'require', '主管名字不能为空！'), //验证姓名
        array('manage_phone', '/(^1[0-9]{10}$)|(^00[1-9]{1}[0-9]{3,15}$)/', '手机格式不正确！', 2), //验证手机
            //     array('phone','match','pattern'=>'/^[1-9]\d{4,14}$/','message'=>'手机号码格式不正确'),unique
            //：“^((d{3,4})|d{3,4}-)?d{7,8}$” 
    );

}

?>
