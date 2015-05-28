<?php

namespace Home\Model;

use Think\Model;

class AdminModel extends Model {

    protected $_validate = array(
        array('name', 'require', '用户名不能为空！'),
        array('name', '', '出错啦！帐号名称已经存在！', 0, 'unique', 1),
        //     array('name', 'unique', '用户名已被注册！'), //验证唯一性
        array('password', 'require', '密码不能为空！'), //验证密码
        array('password', '6,18', '密码必须六位以上！', 3, 'length'),
        array('repassword', 'password', '确认密码不正确！', 0, 'confirm'), //验证确认密码是否与密码一致
      //  array('email', 'require', '邮箱不能为空！'), //验证邮箱		
      //  array('email', 'email', '邮箱格式不对！', 2), //验证邮箱		
    );

}