<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class UserController extends IsloginController {

    public function index() {
        $user = M("user u");
  
        if (IS_POST) {
        
         $village = M("village");   $prop = M("Property");
       
         if(I('post.uid')){ $userlist = $user->where("user_id=" . I('post.uid'))->find();
        
          $userlist['reg_time']=date("Y-m-d H:i:s", $userlist['reg_time']);
       
         if($userlist['user_rank']==1){$userlist['user_rank']='普通会员';}elseif($userlist['user_rank']==2){$userlist['user_rank']='VIP会员';}
          
          $userlist['vill']=$village->where("id=" . $userlist['village_id'])->find(); $userlist['pro']= $prop->where("id=" .$userlist['property_id'])->find();
         
         $this->ajaxReturn($userlist); exit; }
         
            $name = I('post.user_name');
            if ($name)
                $where['u.user_name'] = array('LIKE', $name . '%');
        }

        $count = $user->where($where)->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $user->field('user_id,user_name,fax_phone,email,user_rank,address,reg_time')->where($where)->limit($page->firstRow . ',' . $page->listRows) ->select();
        foreach ($data as $v){if($v['user_rank']==1){$v['user_rank']='普通会员';}elseif($v['user_rank']==2){$v['user_rank']='VIP会员';}   $arr[]=$v; }

        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $arr);
        $this->display();
    }

    public function add() {
        //echo change();
        // print_r($salt);exit;
        $data['action'] = 'add';
        $data['title'] = "添加";
        $data['btn'] = "添加会员";
        $action = I('post.action');

        $property = M("property");
        $village = M("village");
        $Vlist = $village->select();
        $propertyList = $property->select();
        $pro = $this->getprovence();

        $this->assign('pro', $pro);
        $this->assign('Vlist', $Vlist);
        $this->assign('prolist', $propertyList);
        if (IS_POST) {
            $user = D('User');
            $data = $user->create();
            if ($data) {
                
              $_villa = wrt_vialg($data);        
              if (!empty($_villa)) { $this->error($_villa . '  添加失败!',U('/Home/User/add', '', false));  }
                
                $village = M("village v");
                $vfind = $village->field('v.*,p.id as pid,p.pname')
                        ->join('wrt_property AS p ON v.property_id=p.id')
                        ->where("v.id=" . $data['village_id'])
                        ->find();
                if ($action == "add") {
                    $salt = rand(999, 9999);
                    $data['salt'] = $salt; $password = change($salt); $data['password'] = $password; $data["reg_time"] = time(); $data['property_id'] = $vfind['pid']; $data['property'] = $vfind['pname'];
                 
                    if ($user->add($data)) {
                        $this->success("用户添加成功！", U('/Home/User/index'));
                    } else {
                        $this->error("用户添加失败！", U('/Home/User/add'));
                    }
                } elseif ($action == "edit") {
                    $data['property_id'] = $vfind['pid'];  $data['property'] = $vfind['pname'];
                 
                    if ($user->save($data)) {
                        $this->success("修改成功！", U('/Home/User/index'));
                    } else {
                        $this->error("用户修改失败！", U('/Home/User/add'));
                    }
                }
            } else {
                $this->error($user->getError());
            }
        }
        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';
            $data['title'] = "编辑会员";
            $data['btn'] = "编辑";
            $user = M("User");
            $region = M("region");
            $userlist = $user->where("user_id=" . $id)->find();
            $vfind = $village->where("id=" . $userlist['village_id'])->find();
            $regionProv = $region->where("REGION_ID=" . $userlist['province'])->find();
          
            $this->assign('vfind', $vfind); $this->assign("region", $regionProv); $this->assign('info', $userlist);
        }
        $this->assign('data', $data);
        $this->display();
    }

    public function updatePW() {
        $id = I('get.id', 0);
        $action = I('post.action');
        if (IS_POST) {
            if ($action == "edit") {
                $user = D("User");
                if ($data = $user->create()) {
                    $salt = rand(999, 9999);
                    $data['salt'] = $salt;
                    $password = change($salt);
                    $data['password'] = $password;
                    if ($user->save($data)) {
                        // 修改用户的角色
                        //       $this->changeRole($data['id'], $data['role_id']);
                        $url = U('/Home/User/index');
                        $this->success("修改成功！", $url);
                    } else {
                        $this->error("用户修改失败！", 'index');
                    }
                } else {
                    $this->error($user->getError());
                }
            }
        }
        $user = M("User");
        $userlist = $user->field('user_id,user_name')->where("user_id=" . $id)->find();
        $data['action'] = 'edit';
        $data['title'] = "编辑会员";
        $data['btn'] = "修改";
        $this->assign('info', $userlist);
        $this->assign('data', $data);

        $this->display();
    }

    public function del() {
       
        $id = I('get.id', 0);

        $admin = D('User');
        $result = $admin->where("user_id=$id")->delete();
        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }else {
              $this->error("用户修改失败！", 'index');
        }
    }

}

?>
