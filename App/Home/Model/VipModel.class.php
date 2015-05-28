<?php

namespace Home\Model;

use Think\Model;

class VipModel extends Model {

//	protected $tableName = 'admin'; 
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
       
        array('store_name', 'require', '商家名称不能为空！'),
        array('store_name', '', '出错啦！商家名称已经存在！', 0, 'unique', 1),
        array('mobile_phone', '/(^1[0-9]{10}$)|(^00[1-9]{1}[0-9]{3,15}$)/', '手机格式不正确！', 2), //验证手机
    //    array('fax_phone', '/\d{3}-\d{8}|\d{4}-\d{7}/', '电话格式不正确！', 2), //验证电话	
        array('province', 'require', '省市不能为空！'),
        array('address', 'require', '详细地址不能为空！'),
        array('user_name', 'require', '店主姓名不能为空！'),
      //  array('business_license', 'require', '营业执照不能为空！'),
     //   array('qq', 'number', 'Q Q不能为空！'),
            //array('des', 'require', '商家描述不能为空！'),
    );

}

?>
