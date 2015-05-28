<?php

namespace Home\Model;

use Think\Model;

class SpecificationModel extends Model {
    
        protected $_validate = array(
        array('parent_id', 'require', '分类不能为空！'),
      //  array('des', 'require', '权限描述不能为空！'),
    );
    
    
}
?>
