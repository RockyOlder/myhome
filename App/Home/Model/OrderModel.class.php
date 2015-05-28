<?php
namespace Home\Model;
use Think\Model;

class OrderModel extends Model {

//	protected $tableName = 'admin'; 

    protected $_validate = array(
        array('express', 'require', '快递不能为空！'),
        array('express_number', 'require', '运单号不能为空！'),
    );


}

?>
