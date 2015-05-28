<?php

namespace Home\Model;

use Think\Model;

class CategoryModel extends Model {

//	protected $tableName = 'admin'; 

    protected $_validate = array(
        array('cat_name', 'require', '分类名称不能为空！'),
      //  array('cat_name', '', '出错啦！分类名称已经存在！', 0, 'unique', 1),
        array('intro', 'require', '描述不能为空！'),
        
    );

}

?>
