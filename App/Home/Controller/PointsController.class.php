<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class PointsController extends IsloginController {

    public function index() {
        
        $data['action'] = 'add';
        $data['title'] = "添加积分规则";
        $data['btn'] = "添加规则";
        $action = I('post.action');
        $goodsinte = M('GoodsIntegral');
        $goodsintefind = $goodsinte->find();
        if ($goodsintefind) {
            $data['action'] = 'edit';
            $this->assign('word', $goodsintefind);
        }
    
        $this->assign('data', $data);
        $this->display();
    }
    public function ajax_edit(){
        if (IS_AJAX) {            
             $goodsinte = M('GoodsIntegral');
            $data = $_POST;
            $bool = $goodsinte->save($data);
            // dump($bool);
            if ($bool) {
                $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn(0);
            }           
        }else{
            return;
        }
    }

}

?>
