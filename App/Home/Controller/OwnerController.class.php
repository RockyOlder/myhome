<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class OwnerController extends IsloginController {
    private $bill = 
    array('水费','电费','煤气费','管理费','停车费','网络费','电话费','健身费','游泳费');
    /**
     * 业主列表
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-09T16:06:53+0800
     * @return [type]                   [description]
     */
    public function index() {   
        $owner = M("proOwner w");
        if (session("admin.village")!=0) $where[] = array('w.property_id'=>session("admin.village"));
       // if (session("admin.pro_id")!=0) $where['p.id'] = array('LIKE', '%' . session("admin.pro_id") . '%');
        if (session("admin.property")!=0) $where[] = array('v.property_id'=> session("admin.property"));
 
        
        if (IS_POST) {
            $name = I('post.name');
            $mobile = I('post.mobile');
            $address = I('post.address');
            if ($name)
                $where['w.name'] = array('LIKE', '%' . $name . '%');
            if ($mobile)
                $where['w.mobile'] = array('LIKE', '%' . $mobile . '%');
            if ($address)
                $where['w.address'] = array('LIKE', '%' . $address . '%');
        }
        $count = $owner->join('wrt_village AS v ON w.property_id=v.id')->where($where) ->count();
        // dumo($count);
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();

        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type");
        $typeList = $type->select();
//        $data = $owner->field('w.*,v.id as vid,v.name as vname,p.id as pid,p.pname')
       
        $data = $owner->field('w.*,v.id as vid,v.name as vname')
                      ->join('wrt_village AS v ON w.property_id=v.id')
                 //     ->join('wrt_property AS p ON p.id=v.property_id')
                      ->where($where)
                      ->limit($page->firstRow . ',' . $page->listRows)
                      ->select();
     //  echo  $owner->getLastSql(); exit;
        $this->assign('type', $typeList); $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show);$this->assign('data', $data);
        $this->display();
       
    }
    
    
    public function consumption(){
        
        $owner = M("proOwner w");
          
        if (session("admin.village")!=0) $where[] = array('w.property_id'=>session("admin.village"));
       // if (session("admin.pro_id")!=0) $where['p.id'] = array('LIKE', '%' . session("admin.pro_id") . '%');
        if (session("admin.property")!=0) $where[] = array('v.property_id'=> session("admin.property"));
           $where[] = array('o.cate'=> 0);
        
       $count_select =  $owner
                      ->join('RIGHT JOIN wrt_village AS v ON w.property_id=v.id')
                      ->join('LEFT JOIN wrt_order AS o ON w.uid=o.user_id')
                      ->where($where)
                      ->group("o.user_id")
                      ->select();
       $count=count($count_select);
       //       dump($count);
       // $count = $owner->distinct(true)->field("w.uid")->join('wrt_village AS v ON w.property_id=v.id')->where($where)->count();
    // echo  $owner->getLastSql(); exit;
        
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();

        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);

//        $data = $owner->field('w.*,v.id as vid,v.name as vname,p.id as pid,p.pname')
       
        $data = $owner->field('w.*,v.id as vid,v.name as vname,sum(totle) as owner_price,o.cate')
                      ->join('wrt_village AS v ON w.property_id=v.id')
                      ->join('wrt_order AS o ON w.uid=o.user_id')
                      ->where($where)
                      ->limit($page->firstRow . ',' . $page->listRows)
                      ->group("w.id")
                      ->select();
    //  echo  $owner->getLastSql(); exit;
      $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show);$this->assign('data', $data);
      $this->display();
      }
      
  public function Vipconsumption(){
        
        //$a=$b=5;$b=++$a+$b++;echo $b;exit;
        $owner = M("proOwner w");
        
        if (session("admin.village")!=0) $where[] = array('w.property_id'=>session("admin.village"));
       // if (session("admin.pro_id")!=0) $where['p.id'] = array('LIKE', '%' . session("admin.pro_id") . '%');
        if (session("admin.property")!=0) $where[] = array('v.property_id'=> session("admin.property"));
           $where[] = array('o.cate'=> 1);
        
        
       $count_select =  $owner
                      ->join('RIGHT JOIN wrt_village AS v ON w.property_id=v.id')
                      ->join('LEFT JOIN wrt_order AS o ON w.uid=o.user_id')
                      ->where($where)
                      ->group("o.user_id")
                      ->select();
       $count=count($count_select);
  //echo  $owner->getLastSql(); exit;
        // dumo($count);
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();

        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);

//        $data = $owner->field('w.*,v.id as vid,v.name as vname,p.id as pid,p.pname')
       
        $data = $owner->field('w.*,v.id as vid,v.name as vname,sum(totle) as owner_price,o.cate')
                      ->join('wrt_village AS v ON w.property_id=v.id')
                      ->join('wrt_order AS o ON w.uid=o.user_id')
                      ->where($where)
                      ->group("w.id")
                      ->limit($page->firstRow . ',' . $page->listRows)
                      ->select();
  // echo  $owner->getLastSql(); exit;
      $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show);$this->assign('data', $data);
      $this->display();
      }

    /**
     * 添加或者修改单个的业主
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-09T13:10:26+0800
     */
    public function add() {     
        $model = M('pro_owner');
        if (IS_POST) {
            $data = $_POST;
   
              $_villa = wrt_vialg($data);
             if (!empty($_villa)) {  $this->error($_villa . '  操作失败!',U('/Home/Owner/add', '', false)); }
             if($data['id'] == ""){
                unset($data['id']);
                // dump($data);
                //dump($model);
                 if (session("admin.village")!=0){$data['property_id']=session("admin.village") ;}
                $bool = $model->add($data);
     
                $msg = "添加业主失败";
            }else{
                $bool = $model->save($data);
                $msg = "修改业主信息失败";
            }
           if ($bool) {
              $this->redirect('index','', 0, '');
           }else{
            $this->error($msg);
           }
            
        }
        $id = I('get.id',0,'intval');
    //    echo $id;exit;
        if ($id == 0) {
            $data['title'] = '添加业主信息';
            $data['btn'] = '添加业主';
        }else{
            // 根据id查找用户的信息
            $w = array('id'=>$id);            
            $info=$model->where($w)->find();
            $find = M('village')->where("id=".$info['property_id'])->find();
         //   print_r($find);exit;
            $info=$this->assign('info',$info);
            $data['id'] = $id;
            $data['title'] = '修改业主信息';
            $data['btn'] = '修改业主';
          $this->assign('find',$find);
        }
    
      $data['village'] = session("admin.village");
        if (session("admin.property")!=0) $where['property_id'] = array('LIKE', '%' . session("admin.property") . '%');
        
        $village = M('village')->where($where)->select();
        // dump($property);
        $this->assign('property',$village);
        $this->assign('data',$data);
        $this->display();
    }
    public function ajax_edit(){
        if (IS_AJAX) {
            $model = M('pro_owner');
            // dump($_POST);die();
            $data = $_POST;
            // dump($_POST);
            // dump($data['act'] != 'add');die();
            if($data['act'] == 'add'){
                unset($data['act']);
                //dump($data);
                //dump($model);
                $bool = $model->add($data);
                if ($bool) {
                    $out['msg'] = "添加业主信息成功";
                    $out['statue'] = 1;
                }else{
                    $out['msg'] = "添加业主信息失败";
                    $out['statue'] = 0;
                }
            }
            if($data['act'] == 'edit'){
                $sql = "update wrt_pro_owner set name='$data[name]',mobile='$data[mobile]',address='$data[address]' where id=$data[id]";
                // dump($sql);die();
                $bool = M()->query($sql);           
                

                $out['msg'] = "修改业主信息成功";
                $out['statue'] = 1;                
                  
                }                
            
            $this->ajaxReturn($out);
           } 
            
        
    }
    /**
     * 导入业主信息
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-09T13:10:49+0800
     * @return [type]    [description]
     */
    public function get(){
        // 获取所有的物业信息，选择导入那个物业
        $data['title'] = "导入业主信息";
        $this->assign('data',$data);
        $field = "id,name";
        $w = array('id'=>session('admin.village'));
        $property = M('village')->field($field)->where($w)->find();
        // dump($property);die();
        $this->assign('village',$property);
        $this->display();
    }
    public function owner_ajax(){
        if (IS_AJAX) {


            $file = $_POST['file'];
            
            $data = $this->phpexcel($file);
            $sum = count($data)+1;
            // dump($sum);
            for ($i=2; $i < $sum ; $i++) { 
                // dump($i);
                $v=$data[$i];
                // dump($v);
                array_shift($v);
                if ($v[B] != '') {
                    $str = "'".implode("','", $v)."'";
                    $field = "name,mobile,address";
                    $sql =" insert into ".C('DB_PREFIX')."pro_owner(".$field.",property_id) values(".$str.",".I('request.id').")";
                // dump($sql);die();
                    $where = array('name'=>$v['B'],'mobile'=>$v['C']);

                    $check = M('pro_owner')->field('id')->where($where)->find();
                    // dump($check);
                    // dump(!$check);
                    // die();
                    if (!$check) {
                        $bool =M()->execute($sql);
                    }else{
                        $bool = 1;
                    }
                
                    
                }
                
                if ($bool && $i == $sum-1) {
                    $this->ajaxReturn(1);
                }
                            
            }
            
        }
    }
    public function putIn(){
        $model = M('pro_owner_bill_put');
        $where = array('vid'=>session('admin.village'));
         if (IS_POST) {
            // dump($_POST);
            $date = I('post.date');
            $type = I('post.type');
            if ($date)
                $where['date'] = array('LIKE', '%' . $date . '%');
            if ($type != '全部')
                $where['no'] = $type-1 ;
        }
        $this->assign('select',$this->bill);
        $count = $model->where($where) ->count();
        // dumo($count);
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();

        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type");
        $typeList = $type->select();
//        $data = $owner->field('w.*,v.id as vid,v.name as vname,p.id as pid,p.pname')
       // dump($where);
        $data = $model->where($where)
                      ->limit($page->firstRow . ',' . $page->listRows)
                      ->order('time desc')
                      ->select();
     //  echo  $owner->getLastSql(); exit;
        $this->assign('type', $typeList); $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show);$this->assign('data', $data);
        
        $this->display();
    }
    /**
     * 导入业主账单
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-09T13:11:24+0800
     * @return [type]                   [description]
     */
    public function bill() {
        //$this->getbillarray();
        // dump($_SERVER);
        // dump(session());die();
        // 获取所有的物业信息，选择导入那个物业
        $data['title'] = "导入业主账单";
        $this->assign('data',$data);
        $field = "id,pname";
        $property = M('property')->field($field)->select();
        // dump($property);
        $this->assign('property',$property);
        $this->display();        
    }
    public function ajax_bill(){
        if (IS_AJAX) {
            $time = strtotime(I('request.id'));
            $type = I('post.type');
            // dump($time);
            $table = "pro_owner_bill";
            $file = $_POST['file'];
            $data = $this->phpexcel($file);
            if ($type <3 && is_null($data[2]['D'])) {
                 $out = '你上传的账单格式不正确';
                 
                $this->ajaxReturn($out);
            }
            // dump($v);
            // dump($v['D']);
            if ($type >2 && $data[2]['D']) {
                 $out = '你上传的账单格式不正确';
                 
                $this->ajaxReturn($out);
            }
            // dump($data);die();            
            $sum = count($data)+1;
            $uid = array();
            for ($i=2; $i < $sum ; $i++) {  
            //dump($v);               
                $v=$data[$i];
                // dump($v);
                // dump($v['D']);
                
                // dump($v);
                //查询出用户的id
                //根据地址查询用户的oid如果地址匹配不成功怎用用户名和电话匹配
                //$w = array('address'=>$v['D'],'property_id'=>session('admin.village'));
                //$oid = M('pro_owner')->field('id')->where($w)->find();
                //if (!$oid) {//,'name'=>$v['B']
                    $w = array('mobile'=>$v['B'],'property_id'=>session('admin.village'));
                    // dump($w);
                    $oid = M('pro_owner')->field('id')->where($w)->find();
                    // dump($oid);
                    if (!$oid) {
                        $out = $v['B'].该用户不存在;
                       continue;
                    }
                //}
                $id = current($oid);
                $uid[] = $id;
                // dump($uid);
                $where = array('date'=>$time,'oid'=>$id);//用来查找用户的该月的账单是否已经导入
                // dump($where);
                $have = M('pro_owner_bill')->field('id')->where($where)->find();
                // dump($bool);   die(); 
                // dump($v);die();
                $bill = $this->getbillarray($type,$v);
                // dump($bill);
                // dump($_POST);die();
                // dump($have);
                // 组合出对应的数组
                if ($have) {//有就是修改对应的项目
                    // die('我们都会好！');
                    // dump($bill);
                    $bool = M('pro_owner_bill')->where($where)->save($bill);
                    // dump($bool);
                }else{//没有则添加对应的项目
                    $bill['date'] = $where['date'];
                    $bill['oid'] = $where['oid'];
                    $bool = M('pro_owner_bill')->add($bill);
                }
                // dump($bool);die;
                // dump($i);
                // dump($sum);
                if ($bool && $i == $sum-1) {
                    // dump(!empty($uid));
                    if (!empty($uid)) {
                        $log = array(
                            'date' => I('request.id'),
                            'type' => $this->bill[$type],
                            'operate'=> session('admin.name'),
                            'time'=>time(),
                            'no'=>$type,
                            'oid'=>implode(',', $uid),
                            'vid'=>session('admin.village'),
                            'file'=>$file,
                            );
                        $this->billLog($log);
                        $bool = 1;
                    }

                }else if(!$bool && $i == $sum-1){
                        $out = '无任何住户账单导入,或者你要导入的账单和原来的账单没有差别';
                        $bool = 0;
                    }
                          
            }
            // dump($bool);         
            if (!$bool) {
                $this->ajaxReturn($out);
            }else{
                $this->ajaxReturn(1);
            }
        }
    }
    public function del() {
        // dump($_SERVER);die();
        // echo 1;exit;
        $id = I('post.id', 0);
        // dump($id);
        $class = D('pro_owner');
        // 查询住户是否已经申请为vip
        $where = "id = $id";
        $data = $class->where($where)->find();
        // dump($data);
        // dump(M('pro_owner')->getlastSql());
        // dump($check);
        // dump(!$check);
        // die();
        M()->startTrans();
        // dump(is_null($check));
        if ($data['uid'] != 0) {
          //要求删除用户
            $change = array(
              'user_id'=>$data['uid'],
              'property'=>'',
              'property_id'=>0,
              'village'=>'',
              'village_id'=>0,
              'flag'=>0,
              'user_rank'=>1,
              );
            $bool = M('user')->save($change);
            $bool1 = $class->delete($id);
            $result = $bool && $bool1;
        }else{
            // die();
            $result = $class->delete($id);   
        }
        if ($result) {
                M()->commit();
                $out['statue'] = 1;
                $out['msg'] = '成功删除住户信息';
                $this->ajaxReturn($out);
            }else{
              M()->rollback();
                $out['statue'] = 1;
                $out['msg'] = '删除住户信息失败';
                $this->ajaxReturn($out);
            }
        
    }
    /**
     * 删除用户账单发布记录
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-11T13:55:43+0800
     * @return [type]                   [description]
     */
    public function delbill() {
        $id = I('post.id', 0);
        // dump($id);
        $class = D('pro_owner_bill_put');
            $result = $class->delete($id);

            if ($result) {
                $out['statue'] = 1;
                $out['msg'] = '成功删除住户账单发布记录';
                $this->ajaxReturn($out);
            }else{
                $out['statue'] = 0;
                $out['msg'] = '删除住户账单发布记录失败';
                $this->ajaxReturn($out);
            }       
    }
    /**
     * 撤销用户账单发布记录
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-11T13:55:43+0800
     * @return [type]                   [description]
     */
    public function backup() {
        $id = I('post.id', 0);
        // dump($id);
        $model = D('pro_owner_bill_put');
            $data = $model->where(array('id'=>$id))->find();
            $type = $data['no'];
            // $sql = "update wrt_pro_"
            $arr = $this->getback($type);
            $where = "oid in ({$data[oid]}) and date =".strtotime($data['date']);
            // dump($where);
            // dump($arr);die();
            $result=M('pro_owner_bill')->where($where)->save($arr);
            // dump(M('pro_owner_bill')->getlastSql());

            if ($result) {
                $data = array('id'=>$id,'statue' =>1);
                $model->save($data);
                $out['statue'] = 1;
                $out['msg'] = '成功撤销住户账单发布记录';
                $this->ajaxReturn($out);
            }else{
                $out['statue'] = 0;
                $out['msg'] = '撤销住户账单发布记录失败';
                $this->ajaxReturn($out);
            }       
    }
    /**
     * 读取excel文件的内容返回一个数组
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-09T13:11:42+0800
     * @param  [type]                   $file [description]
     * @return [type]                         [description]
     */
    function phpexcel($file){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
        //要导入的xls文件，位于根目录下的Public文件夹
        $filename=$file;
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel=new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        import("Org.Util.PHPExcel.Reader.Excel5");
        //如果excel文件后缀名为.xlsx，导入这下类
        //import("Org.Util.PHPExcel.Reader.Excel2007");
        // $PHPReader=new \PHPExcel_Reader_Excel2007();

        $PHPReader=new \PHPExcel_Reader_Excel5();
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=1;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $arr[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
            }
        
        }
            //dump($arr);
            return $arr;
    }
    /**
     * 业主申请列表
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-09T16:06:53+0800
     * @return [type]                   [description]
     */
    public function app() {  
        $vid = session('admin.village'); 
        // dump(!$vid);die();
        if (!$vid) {
            
            // $this->assign('title','只有小区管理员才能查看用户申请，谢谢配合');
            echo "<script>alert('只有小区管理员才能查看到所在小区的用户申请，谢谢配合')</script>";
            // $this->success(U('index'),'你没有访问权限');
        }else{
            $owner = M("user");

            $where = array('flag' => 1,'village_id'=>session('admin.village'));
            if (IS_POST) {
                $name = I('post.name');
                $mobile = I('post.mobile');
                if ($name)
                    $where['true_name'] = array('LIKE', '%' . $name . '%');
                if ($mobile)
                    $where['user_name'] = array('LIKE', '%' . $mobile . '%');
                
            }
            $field = "user_id,user_name,true_name,address,property,village";
            $count = $owner->where($where)
                    ->count();
            // dump($owner->getlastSql());
            $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
            $show = $page->show();
         //   print_r($show);exit;
            $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
            $type = M("type");
            $typeList = $type->select();
            $data = $owner->field($field)
                    ->where($where)
                    ->limit($page->firstRow . ',' . $page->listRows)
                    ->select();
                    // dump($owner->getlastSql());die;
            $this->assign('type', $typeList);
            $this->assign("currentPage", $currentPage);
            $this->assign("totalPage", $page->totalPages);
            $this->assign("page", $show);
            // dump($data);
            $this->assign('data', $data);
        }
        
        $this->display();
       
    }
    public function ajax_pass(){
        $id = I('request.id',0,'intval');
        if ($id == 0) {
            $out['statue'] = 0;
            $out['msg'] = "没有相应的申请用户";
            $this->ajaxReturn($out);
        }
        // 根据id查找用户的信息
        $w= array('user_id'=>$id);
        $field = 'true_name as name,user_name as mobile,address ,village_id as property_id';
        $data = M('user')->field($field)->where($w)->find();
        if ($data) {
            M()->startTrans();
            $data[uid] = $id;
            $bool = M('pro_owner')->add($data);
            $statue[flag] = 2;
            $statue[user_rank] = 2;
            $statue[user_id] = $id;
            $bool1 = M('user')->save($statue);
            // dump(M()->)
            if ($bool && $bool1) {
              M()->commit();
                $out['statue'] = 1;
                $out['msg'] = "成功通过用户申请";
                
            }else{
                M()->rollback();
                $out['statue'] = 0;
                $out['msg'] = "通过用户申请操作失败";
            } 
            $this->ajaxReturn($out);
        }else{
            $out['statue'] = 0;
            $out['msg'] = "没有相应的用户信息";
            $this->ajaxReturn($out);
        }
    }
    
    public function ajax_del(){
        $id = I('request.id',0,'intval');
        if ($id == 0) {
            $out['statue'] = 0;
            $out['msg'] = "没有相应的申请用户";
            $this->ajaxReturn($out);
        }
        
        
        $statue[flag] = 3;
        $statue[user_id] = $id;
        $bool1 = M('user')->save($statue);
        // dump(M()->)
        if ($bool1) {
            $out['statue'] = 1;
            $out['msg'] = "成功删除用户申请";
            
        }else{
            $out['statue'] = 0;
            $out['msg'] = "删除用户申请操作失败";
        } 
        $this->ajaxReturn($out);
        
    }
    /**
     * 获取用户的账单数组
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-11T08:55:01+0800
     * @return [type]                   [description]
     */
    public function getbillarray($type,$data){
        $v = $data;
        // dump($v);
        // dump($type);
        // // dump($type);
        // dump($data);
        switch ($type) {
            case '0'://水费
                $arr = array(
                    'use_water'=>$v['C'],
                    'water_price'=>$v['D'] / $v['C'],
                    'water_fee'=>$v['D'],
                    );
                break;
            case '1'://电费
                $arr = array(
                    'use_elec'=>$v['C'],
                    'elec_price'=>$v['D'] / $v['C'],
                    'elec_fee'=>$v['D'],
                    );
                break;
            case '2'://煤气费
                $arr = array(
                    'use_gas'=>$v['C'],
                    'gas_price'=>$v['D'] / $v['C'],
                    'gas_fee'=>$v['D'],
                    );
                break;
            case '3'://管理费
                $arr = array(
                    'manage_fee'=>$v['C'],
                    );
                break;
            case '4'://停车卡费
                $arr = array(
                    'car_fee'=>$v['C'],
                    );
                break;           
            case '5'://网络费
                $arr = array(
                    'net_fee'=>$v['C'],
                    );
                break;
            case '6'://手机费
                $arr = array(
                    'mobile_fee'=>$v['C'],
                    );
                break;
            case '7'://健身费
                $arr = array(
                    'fit_fee'=>$v['C'],
                    );
                break;
            case '8'://游泳费
                $arr = array(
                    'swim_fee'=>$v['C']
                    );
                break;
            
        }
        // dump($arr);die();
        return $arr;

    }
    /**
     * 记录用户导入账单的记录表
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-11T08:57:41+0800
     * @return [type]                   [description]
     */
    private function billLog($data){
        M('pro_owner_bill_put')->add($data);
    }
    private function getback($type){
        switch ($type) {
            case '0'://水费
                $arr = array(
                    'use_water'=>0,
                    'water_price'=>0,
                    'water_fee'=>0,
                    );
                break;
            case '1'://电费
                $arr = array(
                    'use_elec'=>0,
                    'elec_price'=>0,
                    'elec_fee'=>0,
                    );
                break;
            case '2'://煤气费
                $arr = array(
                    'use_gas'=>0,
                    'gas_price'=>0,
                    'gas_fee'=>0,
                    );
                break;
            case '3'://管理费
                $arr = array(
                    'manage_fee'=>0,
                    );
                break;
            case '4'://停车卡费
                $arr = array(
                    'car_fee'=>0,
                    );
                break;           
            case '5'://网络费
                $arr = array(
                    'net_fee'=>0,
                    );
                break;
            case '6'://手机费
                $arr = array(
                    'mobile_fee'=>0,
                    );
                break;
            case '7'://健身费
                $arr = array(
                    'fit_fee'=>0,
                    );
                break;
            case '8'://游泳费
                $arr = array(
                    'swim_fee'=>0
                    );
                break;
            
        }
        // dump($arr);die();
        return $arr;
    }
               /* $arr = array(  
                    'date'   =>$time,               
                    'use_water'=>$v['E'],
                    'water_price'=>$v['F'],
                    'water_fee'=>$v['G'],
                    'use_elec'=>$v['H'],
                    'elec_price'=>$v['I'],
                    'elec_fee'=>$v['J'],
                    'use_gas'=>$v['K'],
                    'gas_price'=>$v['L'],
                    'gas_fee'=>$v['M'],
                    'manage_fee'=>$v['N'],
                    'car_fee'=>$v['O'],
                    'net_fee'=>$v['P'],
                    'mobile_fee'=>$v['Q'],
                    'fit_fee'=>$v['R'],
                    'swim_fee'=>$v['S']
                    );*/
  
}

?>
