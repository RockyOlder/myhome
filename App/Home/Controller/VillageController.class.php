<?php
namespace Home\Controller;
use Home\Controller\IsloginController;

class VillageController extends IsloginController {
    /*
     查询分页显示
     * @return [type]  
     * @author phper丶li     
    */
    public function index() {
        //  $data = $this->getdata();

        if (session("admin.property")!=0) $where[] = array('p.id'=>session("admin.property"));
        if (session("admin.village")!=0) $where[] = array('v.id'=>session("admin.village") );
        if (session("admin.developer")!=0) $where[] = array('p.property_id'=> session("admin.developer"));
        
   //    if (session("admin.pid")!=0) $where[] = array('a.pid'=>session("admin.pid"));
        
        $village = M("village v");  $admin=M("admin");
        $count = $village                
                ->join('wrt_property AS p ON v.property_id=p.id')
                ->join('wrt_admin AS a ON a.village=v.id')->where($where)->count();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $find = $village->field('v.*,p.id as pid,p.pname')//,a.name as adminUser
                //  $find = $village->field('v.*,h.id as hid,p.id as pid,p.pname,h.name as hname,r.REGION_ID,r.REGION_NAME')
         //       ->join('wrt_houses AS h ON v.house_id=h.id')
                ->join('wrt_property AS p ON v.property_id=p.id')
             //   ->join('wrt_admin AS a ON a.village=v.id')
              //  ->join('wrt_region AS r ON v.province=r.REGION_ID')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        
        foreach ($find as $value)
        {
            $arr = $admin->field("name as adminUser")->where("village=".$value['id'])->find();
            
            if(isset($arr))
            {
              
                $value['adminUser'] = $arr['adminUser'];
                
            }
            
            $array[] = $value;
            
        }
        
     //   print_r($array);exit;
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $array);
        $this->display();
    }
    /*小区
     *添加与修改 并添加管理员
     * @return [type]       
     * @author phper丶li     
    */

    public function add() {
        $data['action'] = 'add';
        $data['title'] = "添加";
        $data['btn'] = "添加小区";
        $action = I('post.action');
        $property = M("property");
     
         if (session("admin.developer")!=0) {$data['developer'] = session("admin.developer"); $where['property_id'] = array('LIKE', '%' . session("admin.developer") . '%');};
       
        $propertyList = $property->where($where)->select(); $pro = $this->getprovence();   //  $houselist = $house->select(); $this->assign('list', $houselist);   $house = M("Houses");
      
        $this->assign('pro', $pro);  $this->assign('prolist', $propertyList);
        if (IS_POST) {
            
      //   if (session("admin.property")==0) {$this->error("无权限添加！", U('/Home/village/index')); };
 
            $addAdmin = D('Admin'); $adminName = I('post.adminName'); $name = I('post.name');
         
            $Village = D('Village');  $data = $Village->create();
           
            if ($data) {
                
              $_villa = wrt_vialg($data);

               if (!empty($_villa)) {  $this->error($_villa . '  操作失败!',U('/Home/village/add', '', false)); }
                
                  if (session("admin.property")!=0) {  $data["property_id"] =session("admin.property");  };
              
                  if ($action == "add") {  $data["add_time"] = time();    $data['admin']=session("admin.true_name");
                   
                    $bool=$Village->add($data);
                    
                    $admin['true_name'] = $name;  $admin['pid']=session('admin.id'); $admin['add_time']=time(); $admin['top_name'] = '慧享园-小区管理系统';
                    
                    $salt = rand(999, 9999); $admin['salt'] = $salt; $admin['statue'] = 4; $admin['role_id'] = 7; $admin['property'] = session("admin.property");
                  
                    $admin['name'] = $adminName; $admin['village'] = $bool;  $admin['password'] = change($salt);
                   
                    $adminADDObject = $addAdmin->add($admin);
                    
                    if($adminADDObject){ admin_log("添加小区管理员");  $this->addRole($adminADDObject,7);   }else{    $Village->where("id=$bool")->delete(); $this->error("管理员添加失败！", U('/Home/village/add'));    }
                   
                    if ($bool) { admin_log("添加小区");  $this->success("用户添加成功！", U('/Home/village/index'));  } else { $this->error("用户添加失败！", U('/Home/village/add'));  }
            
                    } elseif ($action == "edit") {

                    if ($Village->save($data)) {  $this->success("修改成功！", U('/Home/village/index'));   } else { $this->error("用户修改失败！", U('/Home/village/add')); } }
        
              } else { $this->error($Village->getError(), '', 1); } }
        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';
            $data['title'] = "编辑小区";
            $data['btn'] = "编辑";

            $village = M("village v");
            $find = $village->field('v.*,p.id as pid,p.pname,r.REGION_ID,r.REGION_NAME')//,h.id as hid,h.name as hname
                    //->join('wrt_houses AS h ON v.house_id=h.id')
                    ->join('wrt_property AS p ON v.property_id=p.id')
                    ->join('wrt_region AS r ON v.province=r.REGION_ID')
                    ->where('v.id=' . $id)
                    ->find();

            $this->assign('info', $find);
        }
        $this->assign('data', $data);
        $this->display();
    }

    public function del() {
        $id = I('get.id', 0);

        $admin = D('village');  $owner=M("proOwner");
        $info= $owner->where("property_id=".$id)->find();
        
        if($info){ $this->error('该小区有业主！ 删除失败!',U('/Home/village/index', '', false));}else{ $result = $admin->where("id=$id")->delete();}
      
        if ($result) {
             admin_log("删除小区");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function addRole($uid,$rid){
           
        $arr = array('uid'=>$uid,'group_id'=>$rid);
        M('auth_group_access')->add($arr);

    }

}

?>
