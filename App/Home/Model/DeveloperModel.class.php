<?php
namespace Home\Model;
use Think\Model;

class DeveloperModel extends Model {

//	protected $tableName = 'admin'; 
    protected $_validate = array(
        array('name','require','名字不能为空！'),    //验证姓名
       	array('email','email','邮箱格式不对！',2),    //验证邮箱	
        array('phone','/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/','电话格式不正确！',2), //验证电话
        array('sort','number','竞价只能输入数字'), //默认情况下用正则进行验证  
    );


}
?>
