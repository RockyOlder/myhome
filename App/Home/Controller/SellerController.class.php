<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class SellerController extends IsloginController {
  /*
     查询分页显示
     * @return [type]  
     * @author phper丶li     
    */
     public function lifeGood() {
        $lifeGood = M("LifeGoods l");
      //  $id = session("admin.shop_id");
        if (IS_POST) {
            
             $where[] = array('l.bid > 1');
            
            $lgname = I('post.lgname');

            $type = I('post.cate_pid');
            if ($lgname)
                $where['l.lgname'] = array('LIKE', $lgname . '%');

            if ($type !== "请选择")
                $where['l.cate_pid'] = array('LIKE',  $type . '%');
               
        }
   
         
        $count = $lifeGood->join('wrt_business AS b ON l.bid=b.id')->where($where)->count();

        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type")->where("parent_id=0")->select();
        $data = $lifeGood->field('l.*,b.id,b.name')
                ->join('wrt_business AS b ON l.bid=b.id')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
   //     dump($lifeGood->getLastSql());
        $this->assign('type', $type);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }
   /*
     查询分页显示
     * @return [type]  
     * @author phper丶li     
    */
    public function vipGood(){
     
        $id=session("admin.shop_id");

        $goods = M("Goods g");
        if (IS_POST) {
         //  print_r($_REQUEST);exit;
             $parent_id = I('post.parent_id');
            $name = I('post.name');  $cat_id = I('post.cat_id');
            if ($name)
                $where['goods_name'] = array('LIKE',  $name . '%');

            if ($cat_id!=='请选择')
              $where['g.cat_id'] = array('LIKE',  $cat_id . '%');
            
            if ($parent_id!=='请选择')
              $where['g.parent_id'] = array('LIKE',  $parent_id . '%');
        }
        
        $category=M("category");
        $categoryObjectSelect = $category->where("parent_id=0")->select();
        
        //  print_r($categoryObjectSelect);exit;
      
        $count = $goods->join('wrt_category AS c ON g.cat_id=c.cat_id')->where($where)
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $goods->field('goods_id,goods_name,list_img,description,if_show,price,number,inventory,goods_img,num,vip_num,if_show')
                ->join('wrt_category AS c ON g.cat_id=c.cat_id')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
            

        $this->assign("currentPage", $currentPage);  $this->assign("totalPage", $page->totalPages); $this->assign("page", $show);  $this->assign('data', $data); $this->assign('type', $categoryObjectSelect);
        $this->display();
        
        
        
    }
     public function activity() {
    
        $action = I('post.action');
        if (IS_POST) {
            if ($action == "edit") {
                $gid = I('post.gid'); $goods = D("VipActGood"); $vipGoods=D("Goods");
             
                $vipList = $goods->where("gid=$gid")->find();
                if (isset($vipList)) {
                    $this->error('该商品已有活动!',U('/Home/Goods/index', '', false));
                }
                $data = $goods->create();
           
                if ($data) {
       
                    if ($goods->add($data)) { admin_log("添加促销商品"); $vipGoods->where("goods_id=".$gid)->setInc('num');   $this->success("添加成功！", U('/Home/Activity/index')); } else {  $this->error("用户添加失败！", U('/Home/Activity/index')); }
             
                    } else { $this->error($goods->getError());  }  }  }

        $id = I('get.id', 0);
        if ($id) {
           
            $data['action'] = 'edit';  $data['title'] = "添加活动";  $data['btn'] = "添加";
           
            $goods = M("Goods");
            $vipList = $goods->where("goods_id=$id")->find();
            //   print_r($vipList);exit;
        }
        $vip = D('VipActivity');
        $vipdata = $vip->select(); $this->assign('vip', $vipdata); $this->assign('info', $vipList);  $this->assign('data', $data);
        $this->display();
    }



    public function recommendedGoods() {
        $action = I('post.action');

        if (IS_POST) {
            if ($action == "edit") {

                $id = I('post.sid');

                $giveLifeGoods= D("giveLifeGoods");
                $goods= D("LifeGoods");
                $vipList = $giveLifeGoods->where("goodid=$id")->find();
                if (isset($vipList)) {  $this->error('该商品已有活动!',U('/Home/Business/index', '', false));}
           
                if ($data = $giveLifeGoods->create()) {
                    
                     $data['goodid']=I('post.sid'); $data['city_id']=I('post.city'); $data["add_time"] = strtotime(I('post.add_time'));  $data["deadline"] = strtotime(I('post.deadline'));
                     
                     $data["admin"] = strtotime(I('admin.true_name'));
                     
                    if ($giveLifeGoods->add($data)) {  admin_log("添加活动商品");  $goods->where("lgid=".$id)->setInc('num'); $this->success("添加成功！", U('/Home/Give/good')); } else {  $this->error("用户修改失败！", U('/Home/Business/recommendedBusiness'));  }
              
              } else {$this->error($giveLifeGoods->getError()); }  } }

        $id = I('get.id', 0);
        if ($id) {
            
            $data['action'] = 'edit'; $data['title'] = "推荐服务商品"; $data['btn'] = "促销";$data['name'] = session('admin.true_name');
            $lifeGood = M("LifeGoods");
            $businessObjFind = $lifeGood->where("lgid=$id")->find();
            
        }
        $pro = $this->getprovence();
    
        $this->assign('pro', $pro);
        $this->assign('info', $businessObjFind);
        $this->assign('data', $data);
        $this->display();
    }

     public function RecormObjectSaveVip() {
    
        $action = I('post.action');  $goods = D("VipFirstAd");
       
        $count=$goods ->count();
 
        if($count>=6){ $this->error('推荐商品只能添加六个!',U('/Home/Seller/vipGood', '', false));}
            
       if (IS_POST) {
          
            if ($action == "edit") {
                $gid = I('post.gid');  $vipGoods=D("Goods");
             
                $vipList = $goods->where("gid=".$gid)->find();
             //   print_r($vipList);exit;
                if ($vipList) {
                    $this->error('该商品已有推荐!',U('/Home/VipRecom/index', '', false));
                }
                 //  echo 1;exit;
                $data = $goods->create();
                if ($data) {
                    $data['type']=1;
                    if ($goods->add($data)) { admin_log("添加推荐商品"); $vipGoods->where("goods_id=".$gid)->setInc('vip_num');   $this->success("添加成功！", U('/Home/VipRecom/index')); } else {  $this->error("用户添加失败！", U('/Home/Activity/index')); }
             
                    } else { $this->error($goods->getError());  }  }  }

        $id = I('get.id', 0);
        if ($id) {
           
            $data['action'] = 'edit';  $data['title'] = "添加推荐";  $data['btn'] = "添加";

            $goods = M("Goods");
            $vipList = $goods->where("goods_id=".$id)->find();
        //   print_r($vipList);exit;
        }
        $vip = D('VipActivity');
        $vipdata = $vip->select(); 
     
        $this->assign('vip', $vipdata); $this->assign('info', $vipList);  $this->assign('data', $data);
        $this->display();
    }
    
    public function CategoryClassification(){
        
        $id = I('post.id', 0);
        $category=M("category"); 
        
       $categoryObjectSelect = $category->where("parent_id=".$id)->select();
       $this->ajaxReturn($categoryObjectSelect);
    }

}

?>
