<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class OrderController extends IsloginController {
    /*
     查询分页显示 
     * @return [type]  
     * @author phper丶li     
     * @param fid 查看详情
     * @param oid 发货
    */
    public function index() {
        $action = I('post.action');
        $oid = I('get.id', 0);     
        $fid=I('get.fid', 0);     

      if (IS_POST) {
     //  print_r($_REQUEST);exit;
        $number = I('post.number');
        $user_name= I('post.user_name');
        $statue = I('post.statue');
        if ($number)
            $where['o.number'] = array('LIKE', '%' . $number . '%');
        if ($user_name)
            $where['u.user_name'] = array('LIKE', '%' . $user_name . '%');
        if ($statue!=='')
            $where['o.statue'] = array('LIKE', '%' . $statue . '%');
          
           $order = D('Order');
           $data = $order->create();
        if ($data) {
            
            if ($action == "add") {

                if ($order->add($data)) {  $this->success("用户添加成功！", U('/Home/order/index'));} else { $this->error("用户添加失败！",U('/Home/order/index')); }

            } elseif ($action == "edit") {

                if ($order->save($data)) {  $this->success("修改成功！", U('/Home/order/index')); } else { $this->error("用户修改失败！", U('/Home/order/index')); } 

           } elseif ($action == "delivery") {

                if ($order->save($data)) {  $this->success("正在发货！", U('/Home/order/index')); } else { $this->error("用户提交失败！", U('/Home/order/index')); } }       

        } else { $this->error($order->getError(),'',1); } }
        
       if($oid){
        $order = M("Order o");
         $list= $order->field('o.*,i.number as info_number,i.good_name,i.totle as info_totle,i.list_pici.order_number')
                ->join('wrt_user AS u ON u.user_id=o.user_id')
                ->join('wrt_order_info AS i ON i.order_number=o.number')
                ->where("o.oid=".$oid)
                ->select();
         
         foreach ($list as $value){ $total+=$value['info_totle']; }
         
          $find=$order->where('oid='.$oid)->find(); $find['action']='delivery';
          $total+=$find['freight'];
          
          $express=M("express");
          $expressFind=$express->where('id='.$find['express'])->find();  $expressList=$express->select();

          $this->assign('find', $find); $this->assign('list', $expressList); $this->assign('data', $list); $this->assign('expressFind', $expressFind); $this->assign('total', $total);
          
          $this->display('Vip/delivery');
          exit;
        }    
        
        if($fid){
        $order = M("Order o");
         $list= $order->field('o.*,i.number as info_number,i.good_name,i.totle as info_totle,i.list_pic,i.order_number')
          //      ->join('wrt_user AS u ON u.user_id=o.user_id')
                ->join('wrt_order_info AS i ON i.order_number=o.number')
                ->where("o.oid=".$fid)
                ->select();
    
         $info= $order->field('o.*,u.user_name,u.fax_phone,u.address,u.face,r.REGION_ID,r.REGION_NAME as rname,v.REGION_NAME as cityname,a.REGION_NAME as areaname')
                ->join('wrt_user AS u ON u.user_id=o.user_id')
                ->join('wrt_region AS r ON u.province=r.REGION_ID')          
                ->join('wrt_region AS v ON u.city=v.REGION_ID')   
                ->join('wrt_region AS a ON u.area=a.REGION_ID')   
                ->where("o.oid=".$fid)
                ->find();
 
      $mycars=Array("未付款","待发货","发货","配货中","发货中","待收货","已收货","评论后");
      
      if($info['statue']==0){$info['statue']=$mycars[0];} elseif($info['statue']==1){$info['statue']=$mycars[1];}  if($info['statue']==2){$info['statue']=$mycars[2];} elseif($info['statue']==3){$info['statue']=$mycars[3];} if($info['statue']==4){$info['statue']=$mycars[4];} elseif($info['statue']==5){$info['statue']=$mycars[5];}  if($info['statue']==6){$info['statue']=$mycars[6];} elseif($info['statue']==7){$info['statue']=$mycars[7];}  if($info['statue']==8){$info['statue']=$mycars[8];}  
      
         foreach ($list as $value){ $total+=$value['info_totle']; }
        
         $total+=$info['freight'];

          $this->assign('data', $list); $this->assign('total', $total);$this->assign('info', $info);
          
          $this->display('Order/orderfind');
          exit;
        }   
        
        $order = M("Order as o");
      //    ->join('wrt_user AS u ON u.user_id=o.user_id')    ->join('wrt_business AS b ON b.id=o.shop_id')->
        $w['o.type'] = 0;
        $w['o.cate'] = 0;
        $v['o.type'] = 0;
        $v['o.cate'] = 1;
        // $count = ($order->join('wrt_user AS u ON u.user_id=o.user_id')->join('wrt_vip AS v ON v.store_id=o.shop_id')->where($v)->count()+$order->join('wrt_user AS u ON u.user_id=o.user_id')->join('wrt_business AS b ON b.id=o.shop_id')->where($w)->count())/2;    
        // $vip =$order->join('wrt_user AS u ON u.user_id=o.user_id')->join('wrt_vip AS v ON v.store_id=o.shop_id')->where($v)->count();
        // dump($order->getLastSql());
        // dump($vip);
        // $life =$order->join('wrt_user AS u ON u.user_id=o.user_id')->join('wrt_business AS b ON b.id=o.shop_id')->where($w)->count();    
        // dump($order->getLastSql());
        // dump($life);
        $count = $order->where(array('o.type'=>0))->count();
        // dump($count);
        $sum = $_COOKIE['n'] ? $_COOKIE['n'] : 15;
        $page = initPage($count, $sum);
        // dump($page);
        $show = $page->show();
        $where['o.type'] = 0;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
       /*    $data = $order->field('o.*,u.user_id,b.id,b.name,b.list_pic,u.user_name')
                         ->join('wrt_user AS u ON u.user_id=o.user_id')
                         ->join('wrt_business AS b ON b.id=o.shop_id')
                         ->where($where)
                         ->limit($page->firstRow . ',' . $page->listRows)
                         ->select();
                    // dump($order->getLastSql());
            if (!$data) {
              $data = array();
            }
            $num = count($data);
            if ($num<$sum) {
              if ($num != 0) {
                $limit = $sum - $num;
              }
              $vip = $order->field('o.*,u.user_id,v.store_id as id,v.store_name as name,u.user_name')
                         ->join('wrt_user AS u ON u.user_id=o.user_id')
                         ->join('wrt_vip AS v ON v.store_id=o.shop_id')
                         ->where($where)
                         ->limit($page->firstRow . ',' . isset($limit)?$limit:$page->listRows)
                         ->select();
            }
              // dump()
      foreach ($vip  as $v){ array_push($data, $v);   } */ 
      $field = 'o.oid,o.number,o.totle,o.time,o.cate,o.shop_id,o.user_id,u.true_name as user_name,o.statue';
      $data = $order          
          ->field($field)
          ->join('wrt_user AS u ON u.user_id=o.user_id')
          ->where($where)
          ->order('time desc')
          ->limit($page->firstRow . ',' .$page->listRows)
          ->select();
      
      $mycars=Array("未付款","待发货","发货","配货中","发货中","待收货","已收货","评论后");
      
      foreach ($data as $v){
          // 查询出商家的名称
          if ($v['cate'] == 0) {//生活导航商家订单
            $name = M('business')->field('name')->where(array('id'=>$v['shop_id']))->find();
            // dump($name);die;
          }else{//生活导航商家订单
            $name = M('vip')->field('store_name as name')->where(array('store_id'=>$v['shop_id']))->find();
            // dump($name);die;
          }
          $v['name']=$name['name'];
          // 查询出购买者的真实姓名
        // dump($v);die();/**/
         if($v['statue']==0){$v['statue']=$mycars[0];} elseif($v['statue']==1){$v['statue']=$mycars[1];}
         if($v['statue']==2){$v['statue']=$mycars[2];} elseif($v['statue']==3){$v['statue']=$mycars[3];}
         if($v['statue']==4){$v['statue']=$mycars[4];} elseif($v['statue']==5){$v['statue']=$mycars[5];}    
         if($v['statue']==6){$v['statue']=$mycars[6];} elseif($v['statue']==7){$v['statue']=$mycars[7];}    
         if($v['statue']==8){$v['statue']=$mycars[8];}    
         $arr[]=$v;
       //  print_r($v);
      }//exit;
    //  print_r($arr);exit;

        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('data', $arr);$this->assign('statue', $statue);
     
        $this->display();
    }
    /*
     *删除订单
     * @return [type]       
     * @author phper丶li     
    */
    public function del() {
        $id = I('get.id', 0);
        $order = D('Order');
        $result = $order->where("oid=$id")->find();
        $ordertime = date("Y-m-d", $result['time']);
        if ($this->getChaBetweenTwoDate(date("Y-m-d"), $ordertime) > 7) {
            if ($order->where("oid=$id")->delete()) {
                admin_log("删除订单");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $url = U('/Home/order/index', '', false);
            $this->error('订单未过期!');
        }
    }
    /*
     *删除订单
     * @return [type]       
     * @author phper丶li     
    */
    public function orderDel() {
        //print_r($_REQUEST);exit;
      
        $id = I('post.oid', 0);
        $name=I('post.admin');
        $content=I("post.content");

        $order = D('Order');
        $result = $order->where("oid=$id")->find();
        $order_info=json_encode($result);

        $ordertime = date("Y-m-d", $result['time']);
        if ($this->getChaBetweenTwoDate(date("Y-m-d"), $ordertime) > 7) {
      
            $orderDelete = D('OrderDelete');
            $data['admin']=$name;  $data['time']=time();   $data['uid']=$result['user_id']; $data['order_number']=$result['number'];
            
            $data['oid']=$id;     $data['order_info']=$order_info;   $data['cate']=$result['cate'];  $data['content']=$content;
         
            if(!$orderDelete->add($data)){ $this->error('订单删除失败!',U('/Home/vip/order', '', false));}
            
          if ($order->where("oid=$id")->delete()) { admin_log("删除订单"); redirect($_SERVER["HTTP_REFERER"]); } } else { $this->error('订单未过期!',U('/Home/order/index', '', false)); }
    }

 public function urlAjaxOrderFind() {

        $id = I('post.id', 0);
        if ($id) {
            $goods = M("Order o");
            $goodsFind = $goods->field('o.*,u.user_id,b.id,b.name,b.list_pic,u.user_name')
                    ->join('wrt_user AS u ON u.user_id=o.user_id')
                    ->join('wrt_business AS b ON b.id=o.shop_id')
                    ->where('o.oid=' . $id)
                    ->find();
            $goodsFind['time'] = date("Y-m-d H:i:s", $goodsFind['time']);
            $goodsFind['action']='edit';
        } else {
            $this->error($goods->getError());
        }
        $this->ajaxReturn($goodsFind);
    }
 public function urlAjaxOrderVipFind() {

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

?>
