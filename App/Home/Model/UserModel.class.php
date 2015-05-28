<?php

namespace Home\Model;

use Think\Model;

class UserModel extends Model {

//	protected $tableName = 'admin'; 
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
        array('user_name', 'require', '用户名不能为空！'),
        array('user_name', '', '出错啦！帐号名称已经存在！', 0, 'unique', 1),
        array('password', 'require', '密码不能为空！'), //验证密码
        array('password2', 'password', '确认密码不正确！', 0, 'confirm'), //验证确认密码是否与密码一致
        array('village_id', 'require', '小区不能为空！'),
        array('province', 'require', '省市不能为空！'),
        array('city', 'require', '市不能为空！'),
        array('area', 'require', '区不能为空！'),
        array('mobile_phone', '/(^1[0-9]{10}$)|(^00[1-9]{1}[0-9]{3,15}$)/', '手机格式不正确！', 2), //验证手机
        array('fax_phone', '/\d{3}-\d{8}|\d{4}-\d{7}/', '电话格式不正确！', 2), //验证电话	
        array('email', 'email', '邮箱格式不对！', 2), //验证邮箱	
    );

}

?>
