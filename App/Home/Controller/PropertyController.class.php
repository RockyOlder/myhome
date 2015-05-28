<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class PropertyController extends IsloginController {
    /*
     查询分页显示
     * @return [type]  
     * @author phper丶li     
    */
    public function index() {

        if (session("admin.developer")!=0) $where[] = array('property_id'=> session("admin.developer"));
    
        $prop = M("Property p"); $admin=M("admin");
        $count = $prop->join('wrt_admin AS a ON a.property=p.id')->where($where)->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $prop->field('p.*')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();//,a.name as adminUser->join('wrt_admin AS a ON a.property=p.id')
   
        foreach ($data as $value)
        {
            $arr = $admin->field("name as adminUser")->where("property=".$value['id'])->find();//." and pid=".session("admin.pid")
            
            if(isset($arr))
            {
              
                $value['adminUser'] = $arr['adminUser'];
                
            }
            
            $array[] = $value;
            
        }
        
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('data', $array);
   
        $this->display();
    }

    public function getdata() {
        $page = I('get.page', 1);
        $pageSize = I('get.pageSize', 20);
        $limit = "limit " . ($page - 1) * $pageSize . ',' . $pageSize;
        $sql = "select * from " . C('DB_PREFIX') . "property " . $limit;
        $data = M()->query($sql);
        return $data;
    }
    /*
     添加物业并添加管理员
     * @return [type]  
     * @author phper丶li     
    */
    public function add() {
        $data['action'] = 'add';
        $data['title'] = "添加";
        $data['btn'] = "添加物业";
        $action = I('post.action'); $housesList=M("houses")->select();
        $pro = $this->getprovence();
        
        $this->assign('hlist', $housesList); 
        $this->assign('pro', $pro);
        if (IS_POST) {
         if (session("admin.developer")==0) {$this->error("无权限添加！", U('/Home/property/index')); };
         
            $user = D('property'); $data = $user->create();    $addAdmin = D('Admin'); $adminName = I('post.adminName'); $name = I('post.pname');
            if ($data) {
                
                $_villa = wrt_vialg($data);
                 
                if (!empty($_villa)) {  $this->error($_villa . '  操作失败!',U('/Home/property/add', '', false)); }
                
                if ($action == "add") {
                    $data["add_time"] = time();  $data['property_id']=session("admin.developer"); $data['admin']=session("admin.true_name");
                 
                    $bool=$user->add($data);
                    
                    $admin['true_name'] = $name;  $admin['pid']=session('admin.id'); $admin['add_time']=time();$admin['top_name'] = '慧享园-物业管理系统';
                    
                    $salt = rand(999, 9999); $admin['salt'] = $salt; $admin['statue'] = 4; $admin['role_id'] = 4;
                  
                    $admin['name'] = $adminName; $admin['property'] = $bool;  $admin['password'] = change($salt);
                   
                    $adminADDObject = $addAdmin->add($admin); 
                   
                    admin_log("添加物业管理员"); $this->addRole($adminADDObject,4);  
                    
                    
                    if ($bool) { 
                         admin_log("添加物业");
                        $this->success("用户添加成功！", U('/Home/property/index'));} else { $this->error("用户添加失败！", 'index'); }
                    
                } elseif ($action == "edit") {
                   
                    if ($user->save($data)) {  $this->success("修改成功！", U('/Home/property/index')); } else { $this->error("用户修改失败！", 'index'); } }
         
            } else { $this->error($user->getError(),'',1); } }
            
        $id = I('get.id', 0);

        if ($id) {

            $data['action'] = 'edit';
            $data['title'] = "编辑物业";
            $data['btn'] = "编辑";
            $prope = M("Property");  $region = M("region"); $houses=M("houses");
            
            $propeList = $prope->where("id=" . $id)->find();
            
            $housesFindObject = $houses->where("id=" . $propeList['property_id'])->find();

            $provine = $propeList['province'];
            
            $regionProv = $region->where("REGION_ID=" . $provine)->find();
            $this->assign('housefin',$housesFindObject);
            $this->assign("region", $regionProv);
            $this->assign('info', $propeList);
        }


        $this->assign('data', $data);
        $this->display();
    }
    /*
      删除物业
     * @return [type]  
     * @author phper丶li     
    */
    public function del() {

        $id = I('get.id', 0);
      
        $class = D('property'); $vliage=M("village");      
     
        $info= $vliage->where("property_id=$id")->find();
        if($info){ $this->error('该物业有小区！ 删除失败!',U('/Home/property/index', '', false));}else{  $result = $class->where("id=$id")->delete();}
      
        if ($result) { admin_log("删除物业"); redirect($_SERVER["HTTP_REFERER"]); }
    }
    /*
      权限设置
     * @return [type]  
     * @author phper丶li     
    */
    
    public function addRole($uid,$rid){
           
        $arr = array('uid'=>$uid,'group_id'=>$rid);
        M('auth_group_access')->add($arr);

    }
}



?>
