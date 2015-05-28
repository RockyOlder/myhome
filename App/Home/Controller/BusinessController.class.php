<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class BusinessController extends IsloginController {
    /*
     查询分页显示
     * @POST  参数模糊查询
     * @return [type]  
     * @author phper丶li     
    */
    public function index() {

        $business = M("business");
        if (IS_POST) {
            $name = I('post.name');
            $parent_type = I('post.parent_type');
            
            if ($name)
                $where['name'] = array('LIKE', $name . '%');
            if ($parent_type!=='请选择')
                $where['parent_type'] = array('LIKE', $parent_type . '%');
            
        }
        $count = $business->where($where)
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type");
        $typeList = $type->select();
        $data = $business->field('id,name,mobile_phone,fax_mobile,user_name,address,star,lock,list_pic,num')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        $this->assign('type', $typeList);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
      //  $this->assign('parent_type', $parent_type);
        $this->display();
    }

    //  添加和编辑生活导航商家
    public function add() {

        $pro = $this->getprovence();
        // dump($pro);
        $this->assign('pro', $pro);
        if (IS_POST) {
            // 提交过来的时候讲列表图片组合为一个数组
            $business = D('business');
            $data = $business->create();
            if (!$data) {
                $this->error($business->getError(), '', 2);
            }

            $path = I('post.path');
            $mid = I('post.mid');
            $name = I('post.pic_name');
            foreach ($path as $k => $v) {
                $tem = "";
                $tem['pic'] = $v;
                $tem['mid'] = $mid[$k];
                $tem['name'] = $name[$k];

                $path[$k] = $tem;
            }
            //   exit;
            $more_pic = json_encode($path);

            //  dump($more_pic);exit;
            $city = I('post.city', 0);
            $id = I('post.id');
            $act = I('post.action');
            $arr = I('post.jingwei');
            $arr = explode(',', $arr);
            // dump($arr);die();
            $latitude = $arr[1];
            $longitude = $arr[0];
            // 组合出数据库中的数据
            $post = array(
                'name' => I('post.name', ''),
                'user_name' => I('post.user_name', ''),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'star' => I('post.star'),
                'mobile_phone' => I('post.phone', ''),
                'province' => I('post.province', 0),
                'city' => I('post.city', ''),
                'area' => I('post.area', ''),
                'parent_type' => I('post.parent_type', 0),
                'list_pic' => I('post.list_pic', ''),
                'address' => I('post.address', ''),
                'des' => I('post.des', ''),
                'list_path' => I('post.list_path', ''),
                'mid_pic' => I('post.mid_pic', ''),
                'more_pic' => $more_pic,
                'type' => I('post.type'),
                'sort' => I('post.sort', 100),
                'more_pic' => $more_pic,
                'lock' => I('post.lock', 0)
            );
            $str = formant($post);
          
            if ($act == 'add') {
                $sql = "insert into " . C('DB_PREFIX') . "business set " . $str;
                
                $bool = M()->execute($sql);
                
                if ($bool) {
                     admin_log("添加商家");
                    $this->success("添加成功！", U('Business/index'), 1, FALSE);
                } else {
                    $this->error('添加失败');
                }
            }
            if ($act == 'edit') {
           
                $sql = "update " . C('DB_PREFIX') . "business set " . $str . " where id = $id";
            
                $bool = M()->execute($sql);
          
                if ($bool) {
                    $this->success("修改成功！", U('Business/index'), 1, FALSE);
                } else {
                    $this->error('修改失败');
                }
            }
        }

        //获取分类
        $cate = $this->topCate();

        $data['action'] = 'add';
        $data['title'] = "添加导航商家";
        $data['btn'] = "添加";
        $data['id'] = I('request.id', 0);
        $id = I('get.id', 0);
        if ($id) {
            // print_r($id);exit;
            $data['action'] = 'edit';
            $data['title'] = "编辑";
            $data['btn'] = "编辑";
            $update = M("business b");
            $type = M("Type");
            $find = $update->field('b.*,t.type_id,t.type_name,r.REGION_ID,r.REGION_NAME')
                    ->join('wrt_type AS t ON t.type_id=b.parent_type')
                    ->join('wrt_region AS r ON b.province=r.REGION_ID')
                    ->where('b.id=' . $id)
                    ->find();
            $class_find = $type->where("type_id=" . $find['parent_type'])->find();
            $cate = $type->where("parent_id=0 and type_id!=" . $class_find['type_id'])->select();
            //讲图册的图片显示出来
            $more_pic = $find['more_pic'];
            $find['more_pic'] = json_decode($more_pic, true);
            //组合出经纬度
            $find['jingwei'] = $find['longitude'] . "," . $find['latitude'];

            $this->assign("info", $find);
        }
        $this->assign('cate', $cate);
        $this->assign('data', $data);
        $this->display();
    }
    /*
     *锁定
     * @return [type]       
     * @author phper丶li     
    */
    public function del() {

        $id = I('get.id', 0); $vid= I('get.vid');

        $class = D('business');
        $admin = D('Admin');
        
     /*   if($id){  $result= $class->where("id=$id")->setInc('ls_lock'); }
     
        if($vid){ $result= $class->where("id=".$vid)->setDec('ls_lock'); }*/
        
        if($id)
        {
            $data['lock']=1;$data['id']=$id; $result= $class->save($data);  
            
            $adminData=$admin->where("shop_id=".$id." and type=0")->find();
          
            $adminData['is_lock']=1; $admin->save($adminData);
        }
     
        if($vid)
        {
            $data['lock']=0;$data['id']=$vid; $result= $class->save($data); 
            
            $adminData=$admin->where("shop_id=".$vid." and type=0")->find();
          
            $adminData['is_lock']=0; $admin->save($adminData);
        }
        
        
 //       $result = $class->where("id=$id")->delete();

        if ($result) {
            
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    /*
     *删除商家
     * @return [type]   
     * @POST id    
     * @author phper丶li     
    */
    public function shopDel(){
        
         $id = I('get.id', 0);

        $giveLifeShop= D("LifeGoods");   $business= M("business");
        $info= $giveLifeShop->where("bid=".$id)->find();
        
        if($info){ $this->error('该商店有商品！ 删除失败!',U('/Home/Business/index', '', false));}else{ $result = $business->where("id=$id")->delete();}
       
        if ($result) {
             admin_log("删除商家");
            redirect($_SERVER["HTTP_REFERER"]);
        }
        
        
    }
    /*
     *删除商品
     * @return [type]   
     * @POST id    
     * @author phper丶li     
    */
    public function GoodsDel(){
        
         $id = I('get.id', 0);

        $giveLifeGoods= D("LifeGoods");   $GiveLifeGoods= M("GiveLifeGoods");
        $info= $GiveLifeGoods->where("goodid=".$id)->find();
        
        if($info){ $this->error('该商品有促销！ 删除失败!',U('/Home/Business/goods', '', false));}else{ $result = $giveLifeGoods->where("lgid=$id")->delete();}
       
        if ($result) {
            admin_log("删除商品");
            redirect($_SERVER["HTTP_REFERER"]);
        }
        
        
    }
    /*
     *删除订单
     * @return [type]   
     * @POST id    
     * @author phper丶li     
     * @getChaBetweenTwoDate(参数,参数)
     * @参数1：当前时间
     * @参数2：订单生成时间
    */
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
          $this->error('用户已经付款未消费，不能删除订单');
        }
        if($bool){
          $this->success('删除订单删除成功',$_SERVER["HTTP_REFERER"]);
        }
        $this->error('订单删除失败');
    }
    /*
     *订单详情  
     * @return [type]   
     * @POST id    
     * @author phper丶li     
     * 不是服务商家禁止访问
     * session判断所属商家

    */
     public function order() {
        $name=session('admin.name');
        $id = session('admin.shop_id');
        $action = I('post.action');
        $fid=I('get.fid', 0);     
          $where[] = array('is_end' => 0);
    
         if($id==0){$this->redirect("Order/index");  }

         if ($id){
         //    echo 1;exit;
           $where[] = array('shop_id' => $id);
          //  $where['shop_id'] = array('LIKE', '%' . $id . '%');
          } 
         if (session('admin.type'))
              $where[] = array('cate' => session('admin.type'));
          // $where['cate'] = array('LIKE', '%' . session('admin.type') . '%');
         
        if(IS_GET)
        {
            $statue = I('get.statue');
            if ($statue!=='')  
                 $where[] = array('o.statue' => $statue);
            
            $where[] = array('o.cate' => 0);
         //   $where['o.statue'] = array('LIKE', '%' . $statue . '%');   
        }
         
         if (IS_POST) {  
            $number = I('post.number'); $user_name= I('post.user_name');   $statue = I('post.statue');
          
            if ($number)
               //  $where[] = array('o.number' => $number);
                $where['o.number'] = array('LIKE',  $number . '%');
            if ($user_name)
             //    $where[] = array('u.user_name' => $user_name);
                $where['u.user_name'] = array('LIKE',  $user_name . '%');
            if ($statue!=='')
                $where[] = array('o.statue' => $statue);
          
                $where[] = array('o.cate' => 0);
            
            $order = D('Order'); $data = $order->create();
           
            if ($data) {
                if ($action == "add") {
                  
                    if ($order->add($data)) {  $this->success("用户添加成功！", U('/Home/Business/order'));} else { $this->error("用户添加失败！",U('/Home/Business/order')); }
            
                } elseif ($action == "edit") {
                    
                    if ($order->save($data)) {  $this->success("修改成功！", U('/Home/Business/order')); } else { $this->error("用户修改失败！", U('/Home/Business/order')); } 
              
               }  
            } else { $this->error($order->getError(),'',1); } }
            
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
 
       $mycars=Array("未付款","未消费","已消费","退款");
      
      if($info['statue']==0){$info['statue']=$mycars[0];}  elseif($info['statue']==1){$info['statue']=$mycars[1];} if($info['statue']==2){$info['statue']=$mycars[2];}
      elseif($info['statue']==3){$info['statue']=$mycars[3];}  elseif($info['statue']==6){$info['statue']="待评价";} elseif($info['statue']==7){$info['statue']="交易成功";}
      
         foreach ($list as $value){ $total+=$value['info_totle']; }
        
         $total+=$info['freight'];

          $this->assign('data', $list); $this->assign('total', $total);$this->assign('info', $info);
          
          $this->display('Order/orderfind');
          exit;
        }   
        $order = M("Order o");
       // dump($where);die();
        $count = $order  ->join('wrt_user AS u ON u.user_id=o.user_id')
                    //     ->join('wrt_business AS b ON b.id=o.shop_id')
                         ->where($where)->count();
                    
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
     
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
           $data = $order->field('o.*,u.user_id,u.user_name')//,b.id,b.name,b.list_pic
                         ->join('wrt_user AS u ON u.user_id=o.user_id')
                       //  ->join('wrt_business AS b ON b.id=o.shop_id')
                         ->where($where)
                         ->limit($page->firstRow . ',' . $page->listRows)
                         ->select();
      
       //echo  $order->getLastSql(); exit;
       //  print_r($data);exit;
       $mycars=Array("未付款","未消费","已消费","退款");
      
      foreach ($data as $v){
         if($v['statue']==0){$v['statue']=$mycars[0];} elseif($v['statue']==1){$v['statue']=$mycars[1];}
         if($v['statue']==2){$v['statue']=$mycars[2];} elseif($v['statue']==6){$v['statue']="待评价";}
         elseif($v['statue']==7){$v['statue']="交易完成";}
   

         $arr[]=$v;
      }
    
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('data', $arr);
        $this->assign("statue", $statue);
        $this->display();
    
    }
    
    /*
     查询分页显示
     * @return [type]  
     * @POST 参数-模糊查询
     * @author phper丶li     
    */
    public function goods() {

        $lifeGood = M("LifeGoods l");
        $id=session("admin.shop_id");
        if (IS_POST) {
            $lgname = I('post.lgname');

            $type_find = I('post.cate_pid');
            if ($lgname)
                $where['l.lgname'] = array('LIKE',  $lgname . '%');

            if ($type_find!=="请选择")
                $where['l.cate_pid'] = array('LIKE', $type_find . '%');
        } 
            if ($id!=0)
                $where[] = array('l.bid'=> $id);
      
        $count = $lifeGood->where($where) ->count();
        
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type")->where("parent_id=0 and type_id !=150")->select();
        $data = $lifeGood->field('l.*,b.id,b.name')
                ->join('wrt_business AS b ON l.bid=b.id')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        
       //dump($type_find);
        $this->assign('type', $type);
        $this->assign("type_find",$type_find);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }

    // 添加导航商品
    public function goodsadd() {
         $sessid=session_id();
        if (IS_POST) {
            $lifeGoods = D('lifeGoods');
            
           if (session("admin.shop_id")==0) {$this->error("无权限操作！", U('/Home/Business/goods')); };
            
            $data = $lifeGoods->create();
            if (!$data) {
                $this->error($lifeGoods->getError(), '', 3);
            }
            $id = I('post.id');
            $act = I('post.action', '');
            // 将图册的图片转换为json格式
            $path = I('post.path');
            $mid = I('post.mid');
            $name = I('post.pic_name');
            unset($_POST['path']);
            unset($_POST['mid']);
            unset($_POST['pic_name']);
            foreach ($path as $k => $v) {
                $tem = "";
                $tem['pic'] = $v;
                $tem['mid'] = $mid[$k];
                $tem['name'] = $name[$k];
                // dump
                $path[$k] = $tem;
            }
            $_POST['pic'] = json_encode($path);
            $goods_img = json_encode($path);
            $str = formantpost();
            if ($act == "add") {
    
                $sql = "insert into " . C('DB_PREFIX') . "life_goods set bid=".session("admin.shop_id").", add_time=" . time() . "," . $str;
               
                $bool = M()->execute($sql);

                if ($bool) {
                    $this->success('添加成功',U('/Home/Business/goods'));
                } else {
                    $this->error('添加失败',U('/Home/Business/goodsadd'));
                }
            }
            if ($act == "edit") {
                $goods = D("lifeGoods");
                $data["pic"] = $goods_img;
                if ($goods->save($data)) {
                    $this->success("修改成功！", U('/Home/Business/goods'));
                } else {
                    $this->error("用户修改失败！", U('/Home/Business/goodsadd'));
                }
            }
        }
        $data['session_id']=session_id();
        $data['action'] = 'add';
        $data['title'] = "添加导航商品";
        $data['btn'] = "添加";
        $data['id'] = I('request.id', 0);
        $cate = $this->topCate();
        
        //$this->assign('cate', $cate);
        if ($data['id']) {
            $data['action'] = 'edit';
            $data['title'] = "编辑导航商品";
            $data['btn'] = "编辑";
            $type = M("Type");
            $info = M('life_goods')->where('lgid = ' . $data['id'])->find();
            $fin = $info['cate_pid'];
            $pic = $info['pic']; // dump($more_pic);
            $class_find = $type->where("type_id=" . $fin)->find();
            $cate = $type->where("parent_id=0 and type_id!=" . $class_find['type_id'])->select();
            $info['pic'] = json_decode($pic, true);
            $this->assign("find", $class_find);
            $this->assign("info", $info);
        }
   
        $pro = $this->getprovence();      
        $shop = $this->getshop();
        $this->assign('sessid',$sessid);
        $this->assign('cate', $cate);
        $this->assign('data', $data);  //获取商店的信息

        $this->assign('shop', $shop);   // 获取省份列表

        $this->assign('pro', $pro);       //获取分类

        $this->display();
    }
    /*
      添加活动
     * @return [type]  
     * @author phper丶li     
    */
    public function recommendedBusiness() {
        $action = I('post.action');

        if (IS_POST) {
            if ($action == "edit") {

                $id = I('post.sid');
        
                $giveLifeShop= D("giveLifeShop"); $business=D("business");
                $vipList = $giveLifeShop->where("shopid=$id")->find();
                if (isset($vipList)) {  $this->error('该商店已有活动!',U('/Home/Business/index', '', false));}
           
                if ($data = $giveLifeShop->create()) {
                     $data['shopid']=I('post.sid'); $data['range']=I('post.city'); $data["add_time"] = strtotime(I('post.add_time'));  $data["deadline"] = strtotime(I('post.deadline'));
                     
                    if ($giveLifeShop->add($data)) { admin_log("添加活动商家"); $business->where("id=".$id)->setInc('num');  $this->success("添加成功！", U('/Home/Give/shop')); } else {  $this->error("用户修改失败！", U('/Home/Business/recommendedBusiness'));  }
              
              } else {$this->error($giveLifeShop->getError()); }  } }

        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit'; $data['title'] = "推荐商店"; $data['btn'] = "编辑";
            $business = M("business");
            $businessObjFind = $business->where("id=$id")->find();
        }
        $pro = $this->getprovence();
        // dump($pro);
        $this->assign('pro', $pro);
        $this->assign('info', $businessObjFind);
        $this->assign('data', $data);
        $this->display();
    }
    /*
      添加活动
     * @return [type]  
     * @author phper丶li     
    */
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
                     
                    if ($giveLifeGoods->add($data)) { admin_log("添加商品");  $goods->where("lgid=".$id)->setInc('num'); $this->success("添加成功！", U('/Home/Give/good')); } else {  $this->error("用户修改失败！", U('/Home/Business/recommendedBusiness'));  }
              
              } else {$this->error($giveLifeGoods->getError()); }  } }

        $id = I('get.id', 0);
        if ($id) {
            
            $data['action'] = 'edit'; $data['title'] = "编辑商品"; $data['btn'] = "编辑";$data['name'] = session('admin.name');
            $lifeGood = M("LifeGoods");
            $businessObjFind = $lifeGood->where("lgid=$id")->find();
            
        }
        $pro = $this->getprovence();
        // dump($pro);
        $this->assign('pro', $pro);
        $this->assign('info', $businessObjFind);
        $this->assign('data', $data);
        $this->display();
    }
    

    // 异步获取次级分类
    public function sonCate() {
        //异步获取地区列表内容

        $id = I('post.id');
        if (!IS_AJAX || $id == "")
            return false;
        $data = $this->getsonCate($id);
        // dump($data);
        $this->ajaxReturn($data);
    }
    /*
      查询详细商家
     * @return json
     * @author phper丶li     
     * @param number  POST 传递ID   
    */
    public function typeAjax($id) {
        // $id = I('post.id');
        $type = M("Type");
        $business = M("business");
        $find = $business->where('id = ' . $id)->find();
        $typeList = $type->where("type_id=" . $find['type'])->find();
        $typeList['list'] = $type->where("parent_id=" . $find['parent_type'] . " and type_id!=" . $typeList['type_id'])->select();
        $this->ajaxReturn($typeList);
    }     
    /*
      查询详细商品
     * @return json
     * @author phper丶li     
     * @param number  POST 传递ID   
    */
    public function GoodstypeAjax($id) {
        //  $id = I('post.id');
        $type = M("Type");
        $lifeGoods = M("lifeGoods");
        $find = $lifeGoods->where('lgid = ' . $id)->find();
        $typeList = $type->where("type_id=" . $find['cate_id'])->find();
        $typeList['list'] = $type->where("parent_id=" . $find['cate_pid'] . " and type_id!=" . $typeList['type_id'])->select();
        $this->ajaxReturn($typeList);
    }
    /*
      查询详细订单
     * @return json
     * @author phper丶li     
     * @param number  POST 传递ID   
    */
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



    /**

     * 获取列表数据的方法


      private function getdata() {
      $page = I('get.page', 1);
      $pageSize = I('get.pageSize', 20);
      $limit = "limit " . ($page - 1) * $pageSize . ',' . $pageSize;
      $sql = "select * from " . C('DB_PREFIX') . "business " . $limit;
      $data = M()->query($sql);
      return $data;
      }
     */
}