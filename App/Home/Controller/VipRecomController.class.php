<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class VipRecomController extends IsloginController {

    public function index() {

        $vip = M('VipFirstAd v');
        if (IS_POST) {
            $name = I('post.name');
            if ($name)
                $where['g.goods_name'] = array('LIKE', '%' . $name . '%');
        }
        
        $count = $vip->where($where)->count();
        // dump($count);
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $find = $vip->field('v.*,g.goods_name,g.goods_id')
                ->join('wrt_goods AS g ON g.goods_id=v.gid')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        //循环数据设置推荐范围

        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $find);
        $this->display();
        
    }
    
     public function RecormObjectSaveVip() {
    
        $action = I('post.action');
        if (IS_POST) {
            if ($action == "edit") {
                $goods = D("VipFirstAd"); 

                $data = $goods->create();
                if ($data) {
       
                    if ($goods->save($data)) { $this->success("修改成功！", U('/Home/VipRecom/index')); } else {  $this->error("用户修改失败！", U('/Home/VipRecom/index')); }
             
                    } else { $this->error($goods->getError());  }  }  }

        $id = I('get.id', 0);
        if ($id) {
           
            $data['action'] = 'edit';  $data['title'] = "修改商品";  $data['btn'] = "修改";
           
            $goods = D("VipFirstAd"); 
            $vipList = $goods->where("id=$id")->find();
         
        }
        $vip = D('VipActivity');
        $vipdata = $vip->select(); $this->assign('vip', $vipdata); $this->assign('info', $vipList);  $this->assign('data', $data);
        $this->display();
    }
    
    public function vdel() {
        $id = I('get.id'); $vid= I('get.vid');
   
        $vip = M('VipFirstAd');
        
        if($id){$data['type']=2;$data['id']=$id;$result= $vip->save($data);  }
     
        if($vid){$data['type']=1;$data['id']=$vid; $result= $vip->save($data);  }
    
        if ($result) { redirect($_SERVER["HTTP_REFERER"]); }
    }
    
    public function del() {
        $id = I('get.id');
        $vip = M('VipFirstAd');
        $goods=M("goods");
        
        
           $vipData=$vip->where("id=".$id)->find();
        
         $goodsData=  $goods->where("goods_id=".$vipData['gid'])->find();
         
         $goodsData['vip_num']=0;  $goods->save($goodsData);
         
           $result = $vip->where("id=$id")->delete();
           
           
        if ($result) { admin_log("删除VIP推荐商品");redirect($_SERVER["HTTP_REFERER"]); }
    }

}

?>
