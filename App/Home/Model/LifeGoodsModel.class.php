<?php

namespace Home\Model;

use Think\Model;

class LifeGoodsModel extends Model {

//	protected $tableName = 'admin'; 
//	    array('username','require','用户名必须填写'),
//        array('password','require','密码必须填写'),
////        //可以为同一个项目设置多个验证
    // array('verify','require','验证码必须填写'),

    protected $_validate = array(
        array('cate_pid', 'require', '分类不能为空！'), //默认情况下用正则进行验证   
        array('bid', 'require', '商品所属商店不能为空！'),
        array('lgname', 'require', '商品名称不能为空！'),
        array('lgname', '', '出错啦！商品名称已经存在！', 0, 'unique', 1),
        array('list_pic', 'require', '列表图片不能为空！'),
        //      array('mobile_phone', '/(^1[0-9]{10}$)|(^00[1-9]{1}[0-9]{3,15}$)/', '手机格式不正确！', 2), //验证手机
        //     array('fax_phone', '/\d{3}-\d{8}|\d{4}-\d{7}/', '电话格式不正确！', 2), //验证电话	list_pic
        array('des', 'require', '商品描述不能为空！'),
        array('server', 'require', '商品服务不能为空！'),
        array('price', '/^\d+(\.\d+)?$/', '商场价格只能是数字', 2),
        array('m_price', '/^\d+(\.\d+)?$/', '市场价格只能是数字', 2),
        array('t_price', '/^\d+(\.\d+)?$/', '促销价格只能是数字', 2),
            //   array('des', 'require', '商家描述不能为空！'),
            //    array('user_name', 'require', '店主姓名不能为空！'),
    );

}

?>
