<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

/**
 * 城市管理页面
 */
class CityController extends IsloginController {

    function index() {
        if (IS_POST) {
          $action = I('post.action');   $region=D("region"); $data = $region->create();
            if($data){
 
              if ($action == "add") {   

                if ($region->add($data)) { admin_log("添加城市");$this->success("城市添加成功！", U('/Home/City/index'));  } else { $this->error("城市添加失败！", U('/Home/City/index'));  }
            
                 }elseif ($action == "delete") {
			$password = change(session('admin.salt'));
			if (session('admin.password') == $password) {
                            $info= $region->where("PARENT_ID=".$data['REGION_ID'])->find();     
                             if($info){ $this->error('该城市有下级城市！ 删除失败!',U('/Home/City/index', '', false));}else{$result=$region->where("REGION_ID=".$data['REGION_ID'])->delete();}
                             if($result){ admin_log("删除城市");$this->success("删除成功！", U('/Home/City/index'));  }
                             }else{
                          $this->error('密码错误！ 删除失败!',U('/Home/City/index', '', false));
                      }
                  }
             }
        }
        $data = $this->getprovence();
     
        $this->assign('data', $data);
        $this->display();
    }

    //异步获取城市列表内容
    function city() {
        $id = I('post.id');
        if (!IS_AJAX || $id == "")
            return false;
        $data = $this->getcity($id);
        //dump($data);
        $this->ajaxReturn($data);
    }

    function citySave() {
        $id = I('post.id');
        if (!IS_AJAX || $id == "")
            return false;
        $data = $this->getSaveCity($id);
        //dump($data);
        $this->ajaxReturn($data);
    }

    //异步获取地区列表内容
    function vallage() {
        $id = I('post.id');
        if (!IS_AJAX || $id == "")
            return false;
        $data = $this->getvillage($id);
        //dump($data);
        $this->ajaxReturn($data);
    }

    function vallcage() {
        $id = I('post.id');
        if (!IS_AJAX || $id == "")
            return false;
        $data = $this->getSaveCity($id);
        //dump($data);
        $this->ajaxReturn($data);
    }

}