<?php
namespace Api\Controller;
use Think\Controller;
class HomeController extends Controller {
		function __construct()
	{

		 if (!IS_API) {
	        	die("你无权访问该页面！");
	        }
	}
    // 获取城市列表
    public function city()
    {
        $arr = array('a','b','c','d','e','f','g','h','i','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		// $data = data();
		$id = I('request.version',1);
		if ($id == 1) 
        {
            $out['success']  = 1;
            $city = S('all_city');//读取缓存数据
            if (empty($city)) 
            {
                $city = array();
                foreach ($arr as $key => $value) {
                    // dump($value);die();
                    $w = strtoupper($value);// dump($sql);die();//加入北京
                    if ($w == 'B') {
                        $sql = "select region_id,region_code,region_name from ".C('DB_PREFIX')."region where (region_initial = '$w' and region_level = 2) or region_id = 2";
                    }
                    elseif ($w == 'T') {
                        $sql = "select region_id,region_code,region_name from ".C('DB_PREFIX')."region where (region_initial = '$w' and region_level = 2) or region_id = 3";
                    }
                    elseif ($w == 'S') {
                        $sql = "select region_id,region_code,region_name from ".C('DB_PREFIX')."region where (region_initial = '$w' and region_level = 2) or region_id = 10";
                    }
                    elseif ($w == 'C') {
                        $sql = "select region_id,region_code,region_name from ".C('DB_PREFIX')."region where (region_initial = '$w' and region_level = 2) or region_id = 23";
                    }else{
                        $sql = "select region_id,region_code,region_name from ".C('DB_PREFIX')."region where region_initial = '$w' and region_level = 2";
                    
                    }
                    // dump($sql);die();
                    $city[$value] = M()->query($sql);                    
                    // dump($w);die;
                }
                S("all_city",$city,3600);                
            }
			$out['data'] = $city;    			
			$this->ajaxReturn($out);
		}
	       
    }
    // 获取首页用户的爱好
    public function hobby(){
        // dump(time());
        // dump(strtotime('2015-02-02'));
        $id = I('request.version',1);
        $uid = I('request.user_id',1);
        if ($id ==1) {
            $data = S('system');//读取缓存的数据
            if (!$data) {
                $sql ="select statue,is_price from ".C('DB_PREFIX')."system";
                $data = m()->query($sql);
                if ($data) {
                    S('system',$data,36000);
                }
            }            
            // dump($data);
            if ($data[0]['statue'] == 1) { //表示首页爱好需要显示
                $out['is_show'] = 1;
                // dump($home_cate);查询用户自己设置的快捷方式
                $sql ="select hid,title,type_id from ".C('DB_PREFIX')."user_hobby where uid = $uid";
                // dump($sql);
                $user = M()->query($sql);
                
                // dump($user);
                $out['data'] = $user;
            }else{
                $out['is_show'] = 0;
                $out['data'] = array();
            }
          
            $out['success'] = 1;
            // dump($out);
            $this->ajaxReturn($out);
            
        }
    }
    // 用户添加爱好
    public function hobbyAdd(){
        $id = I('request.version',1,'intval');
        $type_id = I('request.typeId',0,'intval');
        $uid = I('request.userId',0,'intval');
        $arr = array(
            "type_id" => $type_id,
            "title" => I('request.typeName',''),
            'uid' =>$uid,
            );
        // dump($_REQUEST);
        // dump($arr);
        if ($type_id == 0 || $uid == 0) {
            $out['success'] = 0;
            $out['msg'] =C('no_id');
            $this->ajaxReturn($out);
        }
        $str =formant($arr);
        // dump($str);
        if ($id == 1) {
            // 查询用户是否已经添加了次爱好
            $sql = "select * from ".C('DB_PREFIX')."user_hobby where uid = $uid and type_id = $type_id";
            $bool = M()->query($sql);
            if ($bool) {
                $out['success'] = 0;
                $out['msg'] = '已经添加，不能重复添加';
                $this->ajaxReturn($out);
            }
            // dump($sql);
           $bool= M('user_hobby')->add($arr);

           if ($bool) {
               $out['success'] = 1;
               $out['hid']=$bool;
               $out['msg'] = '成功添加爱好';
           }else{
                $out['success'] = 0;
               $out['msg'] = '添加爱好失败';
           }
           $this->ajaxReturn($out);     
        }
    }
    // 用户添加爱好
    public function hobbyDel(){
        $id = I('request.version',1,'intval');
        $hid = I('request.hobbyId',0,'intval');
            
        // dump($_REQUEST);
        // dump($arr);
        if ($hid == 0) {
            $out['success'] = 0;
            $out['msg'] =C('no_id');
            $this->ajaxReturn($out);
        }
        // dump($str);
        if ($id == 1) {
            $sql = "delete from ".C('DB_PREFIX')."user_hobby where hid= ".$hid;
            // dump($sql);
           $bool= M()->execute($sql);
           if ($bool) {
               $out['success'] = 1;
               $out['msg'] = '成功删除爱好';
           }else{
                $out['success'] = 0;
               $out['msg'] = '删除爱好失败';
           }
           $this->ajaxReturn($out);     
        }
    }
    public function homeShop(){
        $id = I('request.version',1,'intval');
        $type = I('request.type',0,'intval');
        if ($id == 1) {
            $good = S('homeShop_'.$type);//读取商品推荐的缓存
            
            $data = S('system');//读取缓存的数据
            if (!$data) {
                $sql ="select statue,is_price from ".C('DB_PREFIX')."system";
                $data = m()->query($sql);
                S('system',$data,36000); //存入缓存                
            }
            if ($data[2]['statue'] == 1) { //表示推荐商店需要显示
                $out['is_show'] = 1;
                $out['success'] =1;
                if (!empty($good)) {
                    // echo '缓存';
                    $out['data'] = $good;
                    $this->ajaxReturn($out);
                }
                
                $time = time();
                // dump($time);
                $sql ="select b.id,list_pic,g.title,g.info,g.id from ".C('DB_PREFIX')."give_life_shop as g join ".C('DB_PREFIX')."business as b on g.shopid = b.id where deadline > ".time()." and g.lock=0 and (g.range = '$type' or is_all = 1)";
                // dump($sql);
                if ($data[1]['is_price'] == 1) {
                    $sql .= " order by g.sort desc limit 0,6";
                }
                // dump($sql);
                $good = M()->query($sql);
                S('homeShop_'.$type,$good,360);//添加进入缓存
                $out['data']= $good;
                
            }else{
                $out['success'] =0;
                $out['data'] = array();
                $out['is_show'] =0;
            }  
            $this->ajaxReturn($out);      
        }

    }
        public function homeShopAll(){
        $id = I('request.version',1,'intval');
        $type = I('request.type',0,'intval');
        $page = I('request.page',1,'intval');
        $pageSize = I('request.pageSize',20,'intval');
        if ($page<1) $page = 1;
        if ($pageSize < 1) $pageSize = 20; 
        if ($id == 1) {
           
            
            $data = S('system');//读取缓存的数据
            if (!$data) {
                $sql ="select statue,is_price from ".C('DB_PREFIX')."system";
                $data = m()->query($sql);
                S('system',$data,36000); //存入缓存                
            }
            if ($data[2]['statue'] == 1) { //表示推荐商店需要显示
                $good = S('homeShop_'.$type.'_'.$page);//读取商品推荐的缓存
                $out['is_show'] = 1;
                $out['success'] =1;
                if (!empty($good)) {
                    // echo '缓存';
                    $out['data'] = $good;
                    $this->ajaxReturn($out);
                }
                
                $time = time();
                // dump($time);
                $sql ="select b.id,list_pic,g.title,g.info from ".C('DB_PREFIX')."give_life_shop as g join ".C('DB_PREFIX')."business as b on g.shopid = b.id where deadline > ".time()." and g.lock=0 and (g.range = '$type' or is_all = 1)";
                // dump($sql);
                $limit = ($page - 1)*$pageSize.",".$pageSize;
                if ($data[1]['is_price'] == 1) {
                    $sql .= " order by g.sort desc limit ".$limit;//;
                }
                $good = M()->query($sql);
                // dump($good);
                S('homeShop_'.$type.'_'.$page,$good,360);//添加进入缓存
                $out['data']= $good;
                
            }else{
                $out['success'] =0;
                $out['data'] = array();
                $out['is_show'] =0;
            }  
            $this->ajaxReturn($out);      
        }

    }
    public function homeGood(){
        $id = I('request.version',1,'intval');
        $type = I('request.type',0,'intval');
        if ($page<1) $page = 1;
        if ($pageSize < 1) $pageSize = 20; 
        // dump($pageSize);
         if ($id == 1) {
            $good = S('homeGood_'.$type);//读取缓存的数据
            // dump($good);
            $data = S('system');//读取缓存的数据
            if (!$data) {
                $sql ="select statue,is_price from ".C('DB_PREFIX')."system";
                $data = m()->query($sql);
                S('system',$data,36000); //存入缓存                
            }
            if ($data[1]['statue'] == 1) { //表示推荐商品需要显示
                 $out['success'] =1;
                $out['is_show'] = 1;
                $out['page'] = $page;
                if ($good) {
                $this->ajaxReturn($good);
                }
                if ($good) {
                    $out['data'] = $good;
                    $this->ajaxReturn($out);
                }
                $limit = ' limit '.($page - 1)*$pageSize.','.$pageSize;
                $sql = "select lgid,lgname,des,list_pic,price from ".C('DB_PREFIX')."give_life_goods as g join ".C('DB_PREFIX')."life_goods as l on g.goodid = l.lgid where deadline>".time()." and g.lock=0 and (g.city_id = '$type' or is_all = 1) order by g.sort desc limit 0,6";
                // $sql .= $limit;
                // dump($sql);
                $good = M()->query($sql);
               if (!empty($good)) {
                    //循环数据将评论条数加入数据中
                    foreach ($good as $k => $v) {
                        $sql = "select count(*) as sum from ".C('DB_PREFIX')."comment where gid = ".$v['lgid'];
                        // dump($sql);
                        $sum = M()->query($sql);
                        if ($sum) {
                            // dump($sum);die();
                           $v['commentSum'] = $sum[0]['sum'];
                        }
                        $good[$k]= $v;
                    }
                    
                    $out['data'] = $good;
               }
            }else{
                $out['is_show'] = 0;
                $out['success'] = 0;
                $out['data'] = array();
            }
            S('homeGood_'.$type,$out,360);//存入缓存
            $this->ajaxReturn($out);      
        }
        
    }
    public function homeGoodAll(){
        $id = I('request.version',1,'intval');
        $type = I('request.type',0,'intval');
        $page = I('request.page',1,'intval');
        $pageSize = I('request.pageSize',20,'intval');
        if ($page<1) $page = 1;
        if ($pageSize < 1) $pageSize = 20; 
        // dump($pageSize);
         if ($id == 1) {
            $good = S('homeGood_'.$type."_".$page);//读取缓存的数据

            // dump($good);
            $data = S('system');//读取缓存的数据
            if (!$data) {
                $sql ="select statue,is_price from ".C('DB_PREFIX')."system";
                $data = m()->query($sql);
                S('system',$data,36000); //存入缓存                
            }
            if ($data[1]['statue'] == 1) { //表示推荐商品需要显示
                 $out['success'] =1;
                $out['is_show'] = 1;
                $out['page'] = $page;
                if ($good) {
                $this->ajaxReturn($good);
                }
               
                //查询总共有多少商品
                $sql  = "select count(*) as sum from ".C('DB_PREFIX')."give_life_goods where deadline > ".time()." and g.lock=0 and (g.city_id = '$type' or is_all = 1)";
                $sum = M()->query($sql);
                // dump($sum);
                if ($sum) {
                    $out['sum'] = $sum[0]['sum'];
                    $out['pageNumber'] = ceil($out['sum']/$pageSize); //计算出总共有多少页
                }
                if ($good) {
                    $out['data'] = $good;
                    $this->ajaxReturn($out);
                }
                $limit = ' limit '.($page - 1)*$pageSize.','.$pageSize;
                $sql = "select lgid,lgname,des,list_pic,price from ".C('DB_PREFIX')."give_life_goods as g join ".C('DB_PREFIX')."life_goods as l on g.goodid = l.lgid where deadline>".time()." and g.lock=0 and (g.city_id = '$type' or is_all = 1) order by g.sort desc";
                $sql .= $limit;
                // dump($sql);
                $good = M()->query($sql);
               if (!empty($good)) {
                    //循环数据将评论条数加入数据中
                    foreach ($good as $k => $v) {
                        $sql = "select count(*) as sum from ".C('DB_PREFIX')."comment where gid = ".$v['lgid'];
                        // dump($sql);
                        $sum = M()->query($sql);
                        if ($sum) {
                            // dump($sum);die();
                           $v['commentSum'] = $sum[0]['sum'];
                        }
                        $good[$k]= $v;
                    }
                    
                    $out['data'] = $good;
               }
            }else{
                $out['is_show'] = 0;
                $out['success'] = 0;
                $out['data'] = array();
            }
            S('homeGood_'.$type."_".$page,$out,360);//存入缓存
            $this->ajaxReturn($out);      
        }
        
    }
    //获取广告的图片和链接地址
    public function ad(){
        // for ($i=1; $i < 19; $i++) { 
        //     M('ad')->where('ad_id ='.$i)->save(array('pic'=>'http://120.24.214.88/Uploads/ad/ad/'.$i.'.png'));
        // }
        // die('ok');
        $id = I('request.version',1);
        $type = I('request.type',1);
        $time = time();
        if ($id == 1) {
            if (!$type) {
                $out['success'] = 0;
                $out['msg'] = "失败";
                $out['data'] =null;
                $this->ajaxReturn($out);
            }
            $out['success'] = 1;
            $model = M('ad');
            $out['success']  = 1;
            // 更新广告的浏览量
            $model->where('type = '.$type)->setInc('skim');
            $name = "ad_".$type;
            $data =S($name);//获取缓存
            // dump($data);
            if (empty($data)) {
                $field = "ad_id,ad_name,pic,ad_url";
                // dump($field);
                $w = "type = $type";// and (end_time > $time or end_time = 0 ) and (start_time < $time or start_time = 0)";
                // dump($w);
                $data = $model->where($w)->limit(5)->select();
                // dump($data);
                //$sql ="select ad_id,ad_name,pic,ad_url from ".C('DB_PREFIX')."ad where type = $type and end_time > $time and start_time < $time limit 0,5";
                // dump($sql);
                //$ad = M()->query($sql);
                if ($data) {
                    S($name,$data,36000);
                }                
            }
            $out['msg'] = "成功";
            $out['data'] = $data;
            $this->ajaxReturn($out);          
           
        }

    }  
    //获取统计数据
    public function adCount(){
        // dump($_REQUEST);
        $id = I('request.version',1);
        $adid = I('request.adid',0);
        $uid =I('request.uid',0);
        // dump($uid);
        if ($adid == 0 && $uid == 0) {
            $out['success'] = 0;
            $out['msg'] = C('no_id');
            $this->ajaxReturn($out);
        }
        $time = time(); //点击时间
        if ($id == 1) {
            $out['success']  = 1;
            // 更新广告的点击
            // $sql ="update ".C('DB_PREFIX')."ad SET click = click+1 where type =".$adid;
            // M()->query($sql);
            $w= array('ad_id'=>$adid);
            // dump($)
            $bool = M('ad')->where($w)->setInc('click');
            // dump($bool);
            // $sql ="insert  into ".C('DB_PREFIX')."ad_count set ad_id = $adid , uid = $uid ,time = $time";
            // dump($sql);
            $arr = array(
                'ad_id'=>$adid,
                'uid'=>$uid,
                'time'=>time()
                );
            $bool = M('ad_count')->add($arr);
            // dump($bool);
            if($bool){
                $out['msg'] = '成功';
                $out['data'] = $bool;
            }else{
                $out['msg'] = '失败';
                $out['success'] = 0;
            }  

            $this->ajaxReturn($out);       
           
        }

    }  
    //根据地址获取城市的区域列表
    public function cityArea(){
    	$id = I('request.version',1);
    	$city = I('request.cityid',234,'intval');
    	if ($id == 1) {
    		$out['success']  = 1;
            if (in_array($city, array(2,3,10,23))) {
                $model = M('region');
                $gid = $model->field('region_id')->where('parent_id='.$city)->select();
                foreach ($gid as $k => $v) {
                    $rid[] = $v['region_id'];
                }
                $rid = '('.implode(',', $rid).')';
                $field = "region_id,region_code,region_name";
                $data = $model->field($field)->where('parent_id in '.$rid)->select();
                    $arr['region_id'] = "$city";
                    $arr['region_code'] = null;
                    $arr['region_name'] = "全城";
               array_unshift($data, $arr);
               $out[data] = $data;
               $out[msg] = "成功";
               $this->ajaxReturn($out);

            }
    
    		$sql = "select region_id,region_code,region_name from ".C('DB_PREFIX')."region where parent_id = $city and region_level = 3";
    		// dump($sql);
    		$data = M()->query($sql);
    		if (is_array($data)) {
    			$out['data'] = $data;
    		}else{
    			$out['data'] = null;
    			$out['success'] = 0;
    			$out['msg'] = "没有你需要的地区信息";
    		}    		 
    
    		$this->ajaxReturn($out);
    		 
    	}
    
    }
    //根据地址获取城市的id
    public function getcityid(){
        $id = I('request.version',1);
        $city = I('request.cityname',234);
        if ($id == 1) {
            $out['success']  = 1;
    
            $sql = "select region_id,region_name from ".C('DB_PREFIX')."region where region_name like '$city%'";
            // dump($sql);
            $data = M()->query($sql);
            // dump($data);
            if ($data) {
                $out['city_id'] = $data[0]['region_id'];
            }else{
                $out['data'] = null;
                $out['success'] = 0;
                $out['msg'] = "没有你需要的地区id";
            }            
    
            $this->ajaxReturn($out);
             
        }
    
    }
    // 根据地区的id获取小区的id
     public function getVillage(){
        $id = I('request.version',1);
        $areaId = I('request.areaId',234);
        if ($id == 1) {
            $out['success']  = 1;
    
            $sql = "select id,name from ".C('DB_PREFIX')."villages where area = '$areaId'";
            // dump($sql);
            $data = M()->query($sql);
            // dump($data);
            if ($data) {
                $out['data'] = $data;
                $out['success'] = 1;
            }else{
                $out['success'] = 0;
                $out['msg'] = "你所在的地区暂时没有合作的小区";
            }            
    
            $this->ajaxReturn($out);
             
        }
    
    }
   
  
}
