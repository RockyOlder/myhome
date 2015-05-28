<?php

namespace Home\Model;

use Think\Model;

class RoleModel extends Model {

    protected $_validate = array(
        array('title', 'require', '角色名不能为空！'),
        array('des', 'require', '权限描述不能为空！'),
    );

}

?>
