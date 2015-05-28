<?php 
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model {
	protected $tableName = 'admin'; 
	    array('username','require','用户名必须填写'),
        array('password','require','密码必须填写'),
//        //可以为同一个项目设置多个验证
        // array('verify','require','验证码必须填写'),
    
    function getlogin(){
    	$sql="select * from wrt_admin"
    	$data=$this->query($sql);
    	$this->assign('data',$data)
    	dump($data);die;
    }
}