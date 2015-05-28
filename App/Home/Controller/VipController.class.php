<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class VipController extends IsloginController {
    /*
     查询分页显示
     * @return [type]  
     * @author phper丶li     
    */
    public function vlist() {
        $vip = M("Vip");
        if (IS_POST) {
            $name = I('post.store_name');
            if ($name)
                $where['store_name'] = array('LIKE', '%' . $name . '%');

        }
        $count = $vip->where($where) ->count();
        
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();

        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $vip->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('data', $data);
        $this->display();
    }

    public function vadd() {
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "添加商家";
        $action = I('post.action');
        $pro = $this->getprovence();
        $this->assign('pro', $pro);
        if (IS_POST) {
            $Village = D('Vip');
            $data = $Village->create();
            if ($data) {
                if ($action == "add") {
                    $data["add_time"] = time();
                    
                    if ($Village->add($data)) { $this->success("用户添加成功！", U('/Home/vip/vlist')); } else {  $this->error("用户添加失败！", U('/Home/vip/vadd')); }
              
              } elseif ($action == "edit") {
                    
                    if ($Village->save($data)) { $this->success("修改成功！", U('/Home/vip/vlist')); } else { $this->error("用户修改失败！", U('/Home/vip/vadd'));
                        
            } } } else { $this->error($Village->getError(), '', 2); } }
        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';  $data['title'] = "编辑商家"; $data['btn'] = "编辑";
            
            $vip = M("Vip");  $region = M("region");
            
            $vipList = $vip->where("store_id=$id")->find();
            $regionProv = $region->where("REGION_ID=" . $vipList['province'])->find();
            $this->assign("region", $regionProv); $this->assign('info', $vipList);
        }
        $this->assign('data', $data);
        $this->display();
    }

    public function vdel() {
        $id = I('get.id'); $vid= I('get.vid');
      
        $vip = D('Vip');
        $admin = D('Admin');

   //     print_r($adminData);exit;
        if($id){
        
             $data['lock']=1;$data['store_id']=$id; $result= $vip->save($data);
              
             $adminData=$admin->where("shop_id=".$id." and type=1")->find();
          
             $adminData['is_lock']=1; $admin->save($adminData);
         
        }
     
        if($vid){
     
              $data['lock']=0;$data['store_id']=$vid; $result= $vip->save($data); 
            
              $adminData=$admin->where("shop_id=".$vid." and type=1")->find();
            

              $adminData['is_lock']=0; $admin->save($adminData);
            }
    
        if ($result) { redirect($_SERVER["HTTP_REFERER"]); }
    }


    public function orderDel() {
        //print_r($_REQUEST);exit;
        $id = I('post.oid', 0);
        $name=I('post.admin');
        $content=I("post.content");
        $where = array('oid'=>$id);
        $order = D('Order');
        $result = $order->field('statue')->where($where)->find();
        if($result['statue'] == 0){//订单未付款可以直接删除
          $bool = M('order')->delete($id);          
        }else if($result['statue'] == 6 || $result['statue'] == 7){
          $data = array('oid'=>$id,'is_end'=>1,'del_result'=>$name.'->'.$content);
          $bool = M('order')->save($data);
        }else if($result['statue'] <6 && $result['statue'] > 0){
          $this->error('用户已经付款未确认收货，不能删除订单');
        }
        if($bool){
          $this->success('删除订单删除成功',$_SERVER["HTTP_REFERER"]);
        }
        $this->error('订单删除失败');
        }
    public function order() {
        $name=session('admin.name');
        $id = session('admin.shop_id');
        $action = I('post.action');
        $oid = I('get.id', 0);     
        $fid=I('get.fid', 0);     

       if($id=='0'){$this->redirect("Order/index");  }
       
        if ($id)
            $where['shop_id'] = array('LIKE', '%' . $id . '%');
     
        if (session('admin.type'))
           $where['cate'] = array('LIKE', '%' . session('admin.type') . '%');
    
        if(IS_GET)
        {
            $statue = I('get.statue');
            if ($statue!=='')  
                 $where[] = array('o.statue' => $statue);
            
            $where[] = array('o.cate' => 1);
         //   $where['o.statue'] = array('LIKE', '%' . $statue . '%');   
        }
        
        if (IS_POST) {
        $number = I('post.number'); $user_name= I('post.user_name');   $statue = I('post.statue');
      
        if ($number)
            $where['o.number'] = array('LIKE', '%' . $number . '%');
        if ($user_name)
            $where['u.user_name'] = array('LIKE', '%' . $user_name . '%');
        if ($statue!=='')
            $where['o.statue'] = array('LIKE', '%' . $statue . '%');
              
            $where[] = array('o.cate' => 1);
            $order = D('Order'); $data = $order->create();
            
            if ($data) {
                if ($action == "add") {
                  
                    if ($order->add($data)) {  $this->success("用户添加成功！", U('/Home/vip/order'));} else { $this->error("用户添加失败！",U('/Home/vip/order')); }
            
                } elseif ($action == "edit") {
                     if($data['freight'] >= 0)
                    {
                    
                        $find=$order->where('oid='.$data['oid'])->find();
                        if($find['freight']==0){
                         
                            $data['totle'] = $data['totle'] + $data['freight'];
                            
                        }elseif($find['freight']!=0){
                                
                            $totle_add = $data['totle'] - $find['freight'];
                           
                            $data['totle'] = $totle_add + $data['freight'];
                      //  print_r( $data['totle']);exit;
                        }
                     
                         
                     }
                    if ($order->save($data)) {  $this->success("修改成功！", U('/Home/vip/order')); } else { $this->error("用户修改失败~！", U('/Home/vip/order')); } 
              
               } elseif ($action == "delivery") { $data['statue']=5;

                    if ($order->save($data)) { sendMsg(I('post.username'),'您好，您的商品已发货'); $this->success("正在发货！", U('/Home/vip/order')); } else { $this->error("用户提交失败！", U('/Home/vip/order')); } }       
         
            } else { $this->error($order->getError(),'',1); } }
       
        if($oid){
           
        $order = M("Order o");
         $list= $order->field('o.*,i.number as info_number,i.good_name,i.totle as info_totle,i.list_pic,i.order_number')
                ->join('wrt_user AS u ON u.user_id=o.user_id')
                ->join('wrt_order_info AS i ON i.order_number=o.number')
                ->where("o.oid=".$oid)
                ->select();
         
         $username=$order->field('o.*,u.user_name')->join('wrt_user AS u ON u.user_id=o.user_id')->where("o.oid=".$oid) ->find();
         
          foreach ($list as $value){ $total+=$value['info_totle']; }
         
          $find=$order->where('oid='.$oid)->find(); $find['action']='delivery';
          $total+=$find['freight'];
          
          $express=M("express");
          $expressFind=$express->where('id='.$find['express'])->find();  $expressList=$express->select();

          $this->assign('find', $find); $this->assign('list', $expressList); $this->assign('data', $list); $this->assign('expressFind', $expressFind); $this->assign('total', $total); $this->assign('username', $username);
          
          $this->display('delivery');
          exit;
        }
        
        if($fid){
        $order = M("Order o");
         $list= $order->field('o.*,i.number as info_number,i.good_name,i.totle as info_totle,i.list_pic,i.order_number')
          //      ->join('wrt_user AS u ON u.user_id=o.user_id')
                ->join('wrt_order_info AS i ON i.order_number=o.number')
                ->where("o.oid=".$fid)
                ->select();
    
         $info= $order->field('o.*,u.user_name,u.fax_phone,u.address,u.face,d.address as userAddress,d.area,d.phone,d.name')//,r.REGION_ID,r.REGION_NAME as rname,v.REGION_NAME as cityname,a.REGION_NAME as areaname
                ->join('wrt_user_address AS d ON d.id=o.address')
                ->join('wrt_user AS u ON u.user_id=d.user_id')  
            //    ->join('wrt_region AS r ON u.province=r.REGION_ID')          
              //  ->join('wrt_region AS v ON u.city=v.REGION_ID')   
             //   ->join('wrt_region AS a ON u.area=a.REGION_ID')   
                ->where("o.oid=".$fid)
                ->find();
 
      $mycars=Array("未付款","待发货","发货","配货中","发货中","待收货","已收货","评论后");
      
      if($info['statue']==0){$info['statue']=$mycars[0];} elseif($info['statue']==1){$info['statue']=$mycars[1];}  if($info['statue']==2){$info['statue']=$mycars[2];} elseif($info['statue']==3){$info['statue']=$mycars[3];} if($info['statue']==4){$info['statue']=$mycars[4];} elseif($info['statue']==5){$info['statue']=$mycars[5];}  if($info['statue']==6){$info['statue']=$mycars[6];} elseif($info['statue']==7){$info['statue']=$mycars[7];}  if($info['statue']==8){$info['statue']=$mycars[8];} elseif($info['statue']==9){$info['statue']=$mycars[9];}       
      
         foreach ($list as $value){ $total+=$value['info_totle']; }
        
         $total+=$info['freight'];
      //   print_r($total);exit;
          $this->assign('data', $list); $this->assign('total', $total);$this->assign('info', $info);
          
          $this->display('Order/orderfind');
          exit;
        }   
        $order = M("Order o");
        $count = $order  ->join('wrt_user AS u ON u.user_id=o.user_id')
                        // ->join('wrt_vip AS v ON v.store_id=o.shop_id')
                         ->where($where)->count();
   //    echo $count;exit;
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        
           $data = $order->field('o.*,u.user_id,u.user_name')//,v.store_id as id,v.store_name as name
                         ->join('wrt_user AS u ON u.user_id=o.user_id')
                    //     ->join('wrt_vip AS v ON v.store_id=o.shop_id')
                         ->where($where)
                         ->order("time desc")
                         ->limit($page->firstRow . ',' . $page->listRows)
                         ->select();
      //   print_r($data);exit;
  //  echo  $order->getLastSql(); exit;     
           
             $mycars=Array("未付款","待发货","发货","配货中","发货中","待收货","已收货","评论后");
      
       foreach ($data as $v){
         if($v['statue']==0){$v['statue']=$mycars[0];} elseif($v['statue']==1){$v['statue']=$mycars[1];}
         if($v['statue']==2){$v['statue']=$mycars[2];} elseif($v['statue']==3){$v['statue']=$mycars[3];}
         if($v['statue']==4){$v['statue']=$mycars[4];} elseif($v['statue']==5){$v['statue']=$mycars[5];}    
         if($v['statue']==6){$v['statue']=$mycars[6];} elseif($v['statue']==7){$v['statue']=$mycars[7];}    
         if($v['statue']==8){$v['statue']=$mycars[8];}   
         $arr[]=$v;
      }

               
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('data', $arr);$this->assign('statue', $statue);
    
        $this->display();
    }
 public function urlAjaxOrderFind() {

        $id = I('post.id', 0);
        if ($id) {
            $goods = M("Order o");
            $goodsFind = $goods->field('o.*,u.user_id,v.store_id as id,v.store_name as name,u.user_name')
                    ->join('wrt_user AS u ON u.user_id=o.user_id')
                    ->join('wrt_vip AS v ON v.store_id=o.shop_id')
                    ->where('o.oid=' . $id)
                    ->find();
            $goodsFind['time'] = date("Y-m-d H:i:s", $goodsFind['time']);
            $goodsFind['action']='edit';
        } else {
            $this->error($goods->getError());
        }
        $this->ajaxReturn($goodsFind);
    }

}

        
/*
 * 计算相差月份
 *  $xx=strtotime("2014-11")-strtotime("2014-10");
    $a = floor($xx/86400/360);    //整数年
   $b = floor($xx/86400/30) - $a*12; //整数月
   echo $b;exit;
//  echo ceil($xx/3600/24);*/

?>
