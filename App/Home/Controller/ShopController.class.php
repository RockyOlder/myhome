<?php
namespace Api\Controller;
use Think\Controller;
class ShopController extends Controller {
		function __construct()
	{

		 if (!IS_API) {
	        	die("你无权访问该页面！");
	        }
	}
    // 获取城市列表
    public function slist()
    {
        $id = I('post.version',1);
          // 获取商店需要的数据
          $type = I('post.type',0);
          // dump($type);
          //获取商店城市的id
          $city_id = I('post.city_id',0);
          //获取商店的地区id
          $area_id = I('post.area_id',0);
          $page = I('post.page',1);
          $pageSize = I('post.pageSize',20);
                     // dump($_POST);
          // var_dump($start);
          $limit = "limit ".($page-1)*$pageSize.",".$pageSize;
          $out['success']=1;
          if ($id == 1){
               $sql = "SELECT id,name,list_pic,star,type,latitude,longitude FROM ".C('DB_PREFIX')."business WHERE `lock` = 0 ";
               if ($type != 0 ) $sql .= "and parent_type = $type ";
               if ($city_id != 0 ) $sql .= "and city = $city_id ";
               if ($area_id != 0 ) $sql .= "and area = $area_id ";
               $sql .=$limit;
               // dump($sql);
               $data = M()->query($sql); 
               // dump($data);
               if (!empty($data) && is_array($data)) {
                   foreach ($data as $k => $v) {
                     $sql  = "select type_name from ".C('DB_PREFIX')."type WHERE type_id = ".$v['type'];
                     $type = M()->query($sql);
                     // dump($type);
                     if ($type && is_array($type)) {
                       $type =current($type[0]);
                       $v['typename'] = $type;
                     }
                     if ($v['star'] == -1) {
                       $v['star'] = 5;
                     }
                     $data[$k]=$v;

                   }
                 }else{
                  $out['success'] = 0;
                 }  
               $num = count($data);
               if ($num < $pageSize) {//如果数据少于请求数据则获取第三放数据
                    # code...
               }
               $out['data']=$data;    
               $this->ajaxReturn($out);
          }

	       
    }
     // 获取商店详情和商品列表
    public function shopinfo()
    {
      // dump(time());
          $id = I('request.version',1);
          // 获取商店需要的数据
          $shopid = I('request.shopid',0,"intval");
           if ($shopid == 0) {
              $out['msg']=C("no_id");
              $out['success']=0;
              $this->ajaxReturn($out);
          }
                     // dump($_POST);
          // var_dump($start);
          $limit = "limit ".($page-1)*$pageSize.",".$pageSize;
          $out['success']=1;
          if ($id == 1){
              // 获取商店的详细信息
              $sql = "SELECT id,name,des,star,type,list_pic,mobile_phone,address,latitude,longitude FROM ".C('DB_PREFIX')."business WHERE `lock` = 0 and id = $shopid";
              // dump($sql);
              $shopinfo = M()->query($sql);
              // dump($shopinfo);die();
              if (!empty($shopinfo) && is_array($shopinfo)) {
                $shopinfo = current($shopinfo);
                if ($shopinfo['star'] == '-1') {
                    $shopinfo['star'] = 5;
                }
                // 查询商店的类型名称
                $sql = "select type_name from ".C('DB_PREFIX')."type where type_id =".$shopinfo['type'];
                $data = M()->query($sql);
                if (!empty($data)) {
                   $shopinfo['typename'] = $data[0]['type_name'];
                }
                // 获取商店的评论条数
                $sql =" select count(*) as sum from ".C('DB_PREFIX')."business as b join ".C('DB_PREFIX')."life_goods as l on b.id = l.bid join ".C('DB_PREFIX')."comment as c on l.lgid = c.gid where b.id =$shopid";
                $data = M()->query($sql);
                if (!empty($data)) {
                   $shopinfo['comment'] = $data[0]['sum'];
                }
              }
              $out['shopinfo'] = $shopinfo;
              // dump($out);
              // 获取商店的商品信息
               $sql = "SELECT lgid,lgname,price,m_price,sold,list_pic,bid FROM ".C('DB_PREFIX')."life_goods WHERE `is_lock` = 0 and `bid` = $shopid limit 0,3";
              // dump($sql);
               // dump($sql);
               $goods = M()->query($sql); 
               $out['goods'] = $goods;   
               
               // 获取商店的评论3条
                $sql =" select count(*) as sum from ".C('DB_PREFIX')."business as b join ".C('DB_PREFIX')."life_goods as l on b.id = l.bid join ".C('DB_PREFIX')."comment as c on l.lgid = c.gid where b.id =$shopid";
                $data = M()->query($sql);
                if (!empty($data)) {
                   $shopinfo['comment'] = $data[0]['sum'];
                }
                // 获取评论内容
                $sql ="select id,shopid,content,star,user_id,time,gid from ".C('DB_PREFIX')."comment where `lock`=0 and shopid=$shopid limit 0,2";
                // dump($sql);
                $comm = M()->query($sql);
                if (!empty($comm)) {
                  // $comm = current($comm);
                  foreach ($comm as $key => $value) {
                    // 查找用户头像
                    $sql ="select face from ".C('DB_PREFIX')."user where user_id = $value[user_id]";
                    // dump($sql);
                    $data = M()->query($sql);
                    // dump($data);
                    if (!empty($data)) {
                      $value['user_face'] = $data[0]['face']; //设置用户的头像
                    }
                    $comm[$key] = $value;
                  }
                }
                $out['comment'] = $comm;
                // dump($comm);
              // 获取推荐商品//推荐商品表
                $sql = "select lgid,lgname,price,m_price,sold,list_pic,bid from ".C('DB_PREFIX')."give_life_goods as g join ".C('DB_PREFIX')."life_goods as lg on g.goodid =lg.lgid limit 0,3";
                // dump($sql);
                $give = M()->query($sql);
                // dump($give);
                $out['give'] = $give;
               // $num = count($data);
               // if ($num < $pageSize) {//如果数据少于请求数据则获取第三放数据
               //      # code...
               // }
                
               $this->ajaxReturn($out);
          }

         
    }
    // 获取商店详情和商店商品列表
    public function goods()
    {
          $id = I('request.version',1);
          // 获取商店需要的数据
          $shopid = I('request.shopid',0);
          if ($shopid == 0) {
              $out['msg']=C("no_id");
              $out['success']=0;
              $this->ajaxReturn($out);
          }
          $page = I('request.page',1);
          $pageSize = I('request.pageSize',20);
         // 获取商店的商品信息
           $sql = "SELECT lgid,lgname,lgnumber,des,list_pic,star,price,m_price,bid FROM ".C('DB_PREFIX')."life_goods WHERE `is_lock` = 0 and `bid` = $shopid ";
          // dump($sql);
           $sql .=$limit;
           // dump($sql);
           $goods = M()->query($sql);  
           
           $sql ="select count(*) as sum from ".C('DB_PREFIX')."life_goods WHERE `is_lock` = 0 and `bid` = $shopid";
           // dump($sql);
           $sum = M()->query($sql);
           // dump($sum);
           if ($sum) {
             $sum = current($sum);
             // dump($sum);
             $goods['sum'] = $sum['sum'];
             $goods['pagesum'] = ceil($sum['sum']/$pageSize);
             $goods['page'] = $page;
           }
           // $num = count($data);
           // if ($num < $pageSize) {//如果数据少于请求数据则获取第三放数据
           //      # code...
           // }
           $out['data'] = $goods;   
           $this->ajaxReturn($out);

         
    }
    // 获取商品详情
    public function goodinfo()
    {
          $id = I('request.version',1);
          // 获取穿入的商品id
          $goodid = I('request.goodid',0);
          if ($goodid == 0) {
              $out['msg']=C("no_id");
              $out['success']=0;
              $this->ajaxReturn($out);
          }
          $out['success']=1;
          if ($id == 1){
               $sql = "SELECT lgid,lgnumber,lgname,des,list_pic,thumb_pic,pic,star,price,m_price,t_price,add_time,content,server,bid FROM ".C('DB_PREFIX')."life_goods WHERE `is_lock` = 0 ";
                // dump($sql);
               $data = M()->query($sql);  

               if ($data) {
                  $out['data']=$data;                   
               }else{
                $out['msg'] ="没有该商品详情";
                $out['success'] = 0;
               }
              $this->ajaxReturn($out);
          }

         
    }
  
}
