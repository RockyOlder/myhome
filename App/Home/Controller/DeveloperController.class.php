<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

//开发商管理
class DeveloperController extends IsloginController {

   
    /*
     * 开发商管理列表
     * @return [type]       
     * @author phper丶li     
    */
    public function index() {
        $devop = M("developer_sum");
        $admin=M("admin");
        if (IS_POST) {
            $name = I('post.name');
            if ($name)
                $where['name'] = array('LIKE', $name . '%');
        }

        $count = $devop->where($where)->count();//->join('wrt_admin AS a ON a.developer=s.id')->field('s.*,a.name as adminUser')->join('wrt_admin AS a ON a.developer=s.id')
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $devop->field('name,owner,phone,addtime,admin,id')->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();

        foreach ($data as $v){  $arr=$admin->field('name,developer')->where("developer=".$v['id'])->find();   if($v['id']==$arr['developer']){ $v['adminUser']=$arr['name']; } $add[]=$v; }


        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('data', $add);
        $this->display();
    }

    public function selectDeveloper() {
        $data = $this->getdata();
        echo json_encode($data);
        //$this->display ();
    }

    // 开发商分部列表页面
    public function sonlist() {
        $data = $this->getson();
        // dump($data);
        $this->assign('data', $data);
        $this->display();
    }
    /*
     *删除开发商
     * @return [type]       
     * @author phper丶li     
    */
    public function del() {

        $id = I('get.id', 0);
        $pid = I('get.pid', 0);
        if (!empty($_GET['pid'])) {
            //     $id = I('get.pid', 0);
            $class = D('developer');
            $result = $class->where("id=$pid")->delete();
        } elseif (!empty($_GET['id'])) {
            $class = D('developer_sum');
            $result = $class->where("id=$id")->delete();
        }
        if ($result) {
            admin_log("删除开发商");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    
  /*
    * @添加和编辑开发商管理并添加管理员
    * @return [type]       
    * @Add admin  
    * @author phper丶li     
    */
    public function add() {

        $data['action'] = 'add';
        $data['title'] = "添加开发商";
        $data['btn'] = "添加";
        $pro = $this->getprovence();

        $this->assign('pro', $pro);

        if (IS_POST) {
            $adminName = I('post.adminName');$action = I('post.action'); $name = I('post.name');
        
            $developer_sum = D("developerSum");   $user = D('Admin');
          
            $data = $developer_sum->create();
         
            if ($data) {
                if ($action == "add") {
                    $msg = "添加";     $data['admin']=session("admin.true_name"); $data['addtime']=time();
                    $bool = $developer_sum->add($data);  
               
                if($bool){
                    $admin['true_name'] =$name;   $admin['add_time']=time(); $admin['top_name']='慧享园-开发商管理系统'; //$admin['pid']=session('admin.id');
                    
                    $salt = rand(999, 9999); $admin['salt'] = $salt; $admin['statue'] = 4;$admin['role_id'] = 2;
                  
                    $admin['name'] = $adminName; $admin['developer'] = $bool;  $admin['password'] = change($salt);
                   
                    $adminADDObject = $user->add($admin);admin_log("添加管理员");
                   
                    $this->addRole($adminADDObject,2);  
         
                    }
             } elseif ($action == "edit") {
                    $data['admin']=session("admin.true_name"); 
                    $msg = "编辑";
                    $bool = $developer_sum->save($data);
                }
                if ($bool) {
                    admin_log("添加开发商");
                    $this->success($msg . "成功！", U('Developer/index'), 1, FALSE);
                } else {
                    $this->error($msg . "失败！", U('Developer/add'), 1, FALSE);
                }
            } else {
                $this->error($developer_sum->getError());
            }
        }
        // 如果是编辑开发商的话，传递一个开发商的id编号过来
        $id = I('get.id', 0);
        $data['id'] = $id;
         
        if ($id) {
            $developer_sum = M("developerSum");$region = M("region"); 
             $data['action'] = 'edit';  $data['title'] = "编辑开发商"; $data['btn'] = "编辑";
            $info = $developer_sum->where("id=" . $id)->find();
         
            $regionProv = $region->where("REGION_ID=" . $info['province'])->find();

            $this->assign("region", $regionProv);
            $this->assign('info', $info);
        }
        // 将数据传递给页面
        $this->assign('data', $data);
        $this->display();
    }

    public function class_Update() {
      
        $id = I('get.id', 0);
        if (!empty($_POST)) {
            // echo 1;exit;
            $del_update = D('developer');
            if ($data = $del_update->create()) {
                $pid = $data['pid'];
                $_villa = wrt_vialg($data);

                if (!empty($_villa)) {
                    $this->error($_villa . '  操作失败!', U('/Home/Developer/index', '', false));
                }
                if ($del_update->save($data)) {
                    $url = U('Developer/delvePid', array('id' => $pid));
                    //   print_r($url);exit;
                    $this->success('修改成功!', $url);
                } else {
                    $url = U('Developer/delvePid', '', false);
                    $this->error('修改失败!');
                }
            } else {
                $url = U('Developer/delvePid', '', false);
                $this->error($del_update->getError());
            }
        } else {
            $del_update = M("developer");
            $class_find = $del_update->where("id=" . $id)->find();
            $this->assign("info", $class_find);
            $this->display('Developer:add');
        }
    }

    public function delvePid() {
        //echo 1;exit;
        $id = $_GET['id'];
        $obj = M('developer_sum');
        $data = $obj->where("id=" . $id)->find();
        //  print_r($data);exit;
        $data['action'] = 'edit';
        $pid = $this->getson();
        $this->assign('pid', $pid);

        $this->assign('data', $data);

        $this->display();
    }

    // 获取顶级开发商数据
    public function getdata() {
        $page = I('get.page', 1);
        $pageSize = I('get.pageSize', 20);
        // dump($page);
        // dump($pageSize);
        $limit = "limit " . ($page - 1) * $pageSize . ',' . $pageSize;
        $sql = "select * from " . C('DB_PREFIX') . "developer_sum " . $limit;
        // dump($sql);die();
        $data = M()->query($sql);
        // dump($data);die();
        return $data;
    }

    // 获取开发商的分公司
    public function getson() {
        $id = I('get.id', 0);
        $page = I('get.page', 1);
        $pageSize = I('get.pageSize', 20);
        $limit = "limit " . ($page - 1) * $pageSize . ',' . $pageSize;
        $sql = "select * from " . C('DB_PREFIX') . "developer where pid = $id " . $limit;
        $data = M()->query($sql);
        // dump($data);die();
        return $data;
    }
    function addRole($uid,$rid){
        $arr = array('uid'=>$uid,'group_id'=>$rid);
        M('auth_group_access')->add($arr);

    }
    

}