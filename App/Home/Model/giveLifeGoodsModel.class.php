<?php

namespace Home\Model;

use Think\Model;

class giveLifeGoodsModel extends Model {

    protected $_validate = array(
        array('title', 'require', '标题不能为空！'),
     //   array('store_name', '', '出错啦！商家名称已经存在！', 0, 'unique', 1),
      //  array('mobile_phone', '/(^1[0-9]{10}$)|(^00[1-9]{1}[0-9]{3,15}$)/', '手机格式不正确！', 2), //验证手机
       // array('fax_phone', '/\d{3}-\d{8}|\d{4}-\d{7}/', '电话格式不正确！', 2), //验证电话	
 //       array('province', 'require', '省市不能为空！'),
        array('add_time', 'require', '开始时间不能为空！'),
        array('deadline', 'require', '过期时间不能为空！'),
        array('range', 'require', '全国推荐不能为空！'),
        array('sort', 'number', '排序不能为空！'),

    );

}

?>
