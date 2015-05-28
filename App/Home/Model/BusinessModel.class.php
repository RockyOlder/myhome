<?php

namespace Home\Model;

use Think\Model;

class BusinessModel extends Model {

//	protected $tableName = 'admin'; 
//	    array('username','require','用户名必须填写'),
//        array('password','require','密码必须填写'),
////        //可以为同一个项目设置多个验证
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
        array('parent_type', 'require', '分类不能为空！'), //默认情况下用正则进行验证   
        array('name', 'require', '商家名称不能为空！'),
        array('name', '', '出错啦！商家名称已经存在！', 0, 'unique', 1),
        //      array('mobile_phone', '/(^1[0-9]{10}$)|(^00[1-9]{1}[0-9]{3,15}$)/', '手机格式不正确！', 2), //验证手机
        array('fax_phone', '/\d{3}-\d{8}|\d{4}-\d{7}/', '电话格式不正确！', 2), //验证电话	
        array('province', 'require', '省市不能为空！'),
        array('address', 'require', '详细地址不能为空！'),
        array('des', 'require', '商家描述不能为空！'),
        array('user_name', 'require', '店主姓名不能为空！'),
    );

}

?>
