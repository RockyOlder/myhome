<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class AdminController extends IsloginController {

    public function index() {
        $password = change(6917,md5('admin'));
        // dump($password);
        // dump(session());
         $owner = M("admin");
         $where = array('pid'=>session('admin.id'));
        if (IS_POST) {
            $name = I('post.name');
            $mobile = I('post.mobile');
            $true_name = I('post.true_name');
            if ($name)
                $where['name'] = array('LIKE', '%' . $name . '%');
            if ($mobile)
                $where['mobile'] = array('LIKE', '%' . $mobile . '%');
            if ($true_name)
                $where['true_name'] = array('LIKE', '%' . $true_name . '%');
        }
        // dump($where);die();
        $count = $owner->where($where)
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 30);
        $show = $page->show();
     //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        
        $data = $owner->field('id,name,true_name,mobile')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }
    /**
     * 申请页面只有慧锐通总管理平台能够查看
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-02-01T09:18:48+0800
     * @return [type]                   [description]
     */
    public function app() {
        // dump(session());
         $owner = M("admin");
         $where = array('flag'=>1);  
                
        if (IS_POST) {
            $name = I('post.name');
            $mobile = I('post.mobile');
            $address = I('post.address');
            if ($name)
                $where['name'] = array('LIKE', '%' . $name . '%');
            if ($mobile)
                $where['mobile'] = array('LIKE', '%' . $mobile . '%');
            if ($address)
                $where['address'] = array('LIKE', '%' . $address . '%');
        }
        // dump($where);die();
        $count = $owner->where($where)
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();
     //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        
        $data = $owner->field('id,name,mobile,email,true_name,type,developer,add_time')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        // dump($owner->getlastSql());
        foreach ($data as $k => $v) {            
            $data[$k] = $this->appRole($v);
        }
        // dump($where);
        // dump($data);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }
    public function add() {
        // dump(session());
            
        if (IS_POST) {
            // dump($_POST);die();            
            $user = M('Admin');                
            $salt = rand(999, 9999);
            $data['salt'] = $salt;
            $password = change($salt);
            $data['password'] = $password;
            $data['role_id'] = I('post.role_id',0,'intval');
            $data['true_name'] = I('post.true_name');
            $data['name'] = I('post.name');
            $data['mobile'] = I('post.mobile');
            $data['email'] = I('post.email');
            $data['add_time'] = time();
            $data['type'] = TYPE;//登录界面分割标志
            $data[top_name] = session('admin.top_name');
            $data[top_logo] = session('admin.top_logo');
            $data[shop_id] = session('admin.shop_id');
            $data[statue] = session('admin.statue');
            $data[house] = session('admin.house');
            $data[pid] = session('admin.id');
            $data[developer] = session('admin.developer');
            $property = I('property',0,'intval');
            $village = I('village',0,'intval');
            $data[property] = session('admin.property');                 
            $data[village] = session('admin.village');
            // dump()          
            $bool = $user->add($data);
            // dump($bool);die();
            if ($bool) {
                $this->addRole($bool,$data['role_id']);
                $url = U('/Home/Admin/index');
                admin_log('添加管理员：'.I('post.name'));
                $this->success("用户添加成功！", $url);
            } else {
                $this->error("用户添加失败！");
            }
                
            
        }
        $data['action'] = 'add';
        $data['title'] = "添加管理员";   
        admin_log($data['title']);
        $w = array('pid'=>session('admin.id'));
        $rule = M('auth_group')->field('id,title')->where($w)->select();
        $this->assign('rule',$rule);       
        //dump($data);die();
        $this->assign('info', $userList);
        $this->assign('data', $data);
           
        $tpl = 'add';
        $this->display($tpl);
    }
    /**
     * 查看用户的详情申请页面
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-02-01T09:35:33+0800
     * @return [type]                   [description]
     */
    public function info(){
        $id = I('get.id',0,'intval');
        // 查找用户的所有东西
        $data = M('admin')->where(array('id'=>$id))->find();
        if ($data['statue'] == 2) { //代表生活导航的申请商家
			$data['title'] = '生活导航的申请商家详情';
            $shop = M('business b')
                  ->join('wrt_business_shop AS s ON b.id = s.shop_id')
                  ->where('b.id='.$data['shop_id'])->find();
			$shop[more_pic] = json_decode($shop[more_pic],true);
			$tpl = 'life';
        }elseif($data['statue'] == 3 ) { //代表vip申请商家
			$data['title'] = 'VIP商家申请详情';
            $shop = M('vip')
                  ->where('store_id='.$data['shop_id'])->find();
                  //dump(M('business')->getlastSql());
            $tpl = 'vip';
        }elseif($data['statue'] == 4 ) { //代表开发商申请
        	$data['title'] = '开发商申请详情';
            $shop = M('developer d')
                  ->join('wrt_developer_info AS i ON d.id = i.did')
                  ->where('d.id='.$data['developer'])->find();
                  // dump($shop);die();
			$tpl = 'developer';
        }
		$data = $this->appRole($data);
        $this->assign('info',$data);
        $this->assign('shop',$shop);
        $this->display($tpl);
    }
    public function delApp(){
        $id = I('get.id',0,'intval');
        if ($id == 0) {
            $this->error('缺少参数');
        }
        // 查询出用户的参数
        $data = M('admin')->field('id,statue,shop_id,house')->where(array('id'=>$id))->find();
        // dump($data);die();
        if ($data[statue] == 2) { //生活导航
            admin_log('删除生活导航商家申请');
            M('admin')->delete($id);
            M('business')->delete($data[shop_id]);
            M('business_shop')->where(array('shop_id'=>$data[shop_id]))->delete();
        }elseif($data[statue] == 3){
            admin_log('删除VIP商家申请');
            M('admin')->delete($id);
            M('vip')->delete($data[shop_id]);
        }elseif($data[statue] == 4){
            admin_log('删除开发商申请');
            M('admin')->delete($id);
            M('developer')->delete($data[house]);
            M('developer_info')->where(array('did'=>$data[house]))->delete();
        }
        redirect($_SERVER["HTTP_REFERER"]);
    }
    /**
     * 删除管理员
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-20T13:28:31+0800
     * @return [type]                   [description]
     */
    public function del() {
        $id = I('get.id', 0);
        $admin = D('Admin');
        $result = $admin->where("id=$id")->delete();
        $result = M('auth_group_access')->where("uid=$id")->delete();
        if ($result) {
            admin_log('删除'.$id.'号管理员');
            $this->success('成功删除管理员',$_SERVER["HTTP_REFERER"]);
        }
    }
    /**
     * 编辑管理员
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-20T13:28:08+0800
     * @return [type]                   [description]
     */
    public function edit() {
        if(IS_POST) {
            // dump($_POST);//die();
            $admin = D("Admin");
            // $data = $_POST;
            if ($data = $admin->create()) {
                // dump($data);die();
                $bool =$admin->save($data);
                // dump($bool)
                if ($bool) {
                    $url = U('index');
                    //echo "<script>if(confirm('成功修改用户信息！'))location.href='".$url."'</script>";
                    // 修改用户的角色
                    // $this->changeRole($data['id'],$data['role_id']);
                    // $url = U('/Home/Admin/index');
                    $this->success("修改成功！", $url);
                } else {
                    $this->error("用户修改失败！");
                }
            } else {
                $this->error($admin->getError());
            }
        }
        // dump(session());
        
        $id = I('get.id', 0,'intval');
        if (!$id) {
            $this->error('缺少参数');
        }
        if ($id == 1) {
            $this->error('超级管理员不能修改');
        }
        $rule = M('auth_group')->field('id,title')->where(array('pid'=>session('admin.id')))->select();
        // dump($rule);die();
        $this->assign('rule',$rule);
              
        //echo 1;exit;
        $data['title'] = "编辑管理员";
        $user = M("Admin");
        $userList = $user->where("id=" . $id)->find();
        // dump($userList);die();
        
        $this->assign('info', $userList);
        $this->assign('data',$data);
        $this->display();
        
    }
    function ajax_edit(){
        if(IS_AJAX) {
            // dump($_POST);//die();
            $admin = D("Admin");
            // $data = $_POST;
            if ($data = $admin->create()) {
                // dump($data);die();
                $bool =$admin->save($data);
                // dump($bool)
                if ($bool) {
                    $url = U('index');
                    echo "<script>if(confirm('成功修改用户信息！'))location.href='".$url."'</script>";
                    // 修改用户的角色
                    // $this->changeRole($data['id'],$data['role_id']);
                    // $url = U('/Home/Admin/index');
                    // $this->success("修改成功！", $url);
                } else {
                    $this->error("用户修改失败！", 'index');
                }
            } else {
                $this->error($admin->getError());
            }
        }
    }
    function updatePW(){
        if (IS_POST) {           
            $user = D("Admin");
            if ($data = $user->create()) {
                $salt = rand(999, 9999);
                $data['salt'] = $salt;
                $password = change($salt);
                $data['password'] = $password;
                // dump($data);die();
                if ($user->save($data)) {
                    $this->success('用户密码修改失败！',U('index')); 
                } else {
                    $this->error("用户密码修改失败！");
                }
            } else {
                $this->error($user->getError());
            }        
        }
        $this->assign('title','修改管理员密码');
        $id = I('get.id');
        if (!$id) {
            $this->error('缺少参数');
        }
        $info = M('admin')->field('name,id')->where(array('id'=>$id))->find();
        $this->assign('info',$info);
        $this->display();


    }
	//通过管理员的申请
	function toPass(){
		$id = I('post.id');
		$life = I('post.life');
		$data=array('is_lock'=>0,'flag'=>0,'id'=>$id);
		$bool = M('admin')->save($data);
		if($bool){
            admin_log('审核通过商家申请');
			if($life){
				$data = M('admin')->field('shop_id')->where(array('id'=>$id))->find();
				$data = array('id'=>current($data),'lock'=>0);
				M('business')->save($data);
			}
			$this->ajaxReturn($bool);
		}else{
			$this->ajaxReturn(0);
		}
	}

    function addRole($uid,$rid){
        $arr = array('uid'=>$uid,'group_id'=>$rid);
        M('auth_group_access')->add($arr);

    }
    function changeRole($uid,$rid){
        $w = array('uid'=>$uid);
        $arr = array('group_id'=>$rid);
        M('auth_group_access')->where($w)->save($arr);
    }
	function appRole($v){
        // dump($v);die();
		if ($v[type] == 1) {
           $v['role'] = "VIP商家";
        }elseif ($v[type] == 0 && $v[developer] == 0) {
            $v[role] ="生活导航商家";
        }elseif ($v[developer] != 0) {
            $v[role] ="房地产开发商";
        }
		return $v;
	}
    public function village(){
        $id = I('post.id',0,'intval');
        if (!$id) {
            $this->ajaxReturn(null);
        }
        // 查找物业下面的小区
        $w = array('property_id'=>$id);
        $data = M('village')->field('id,name')->where($w)->select();
        // dump($data);die();
        if ($data) {
            $this->ajaxReturn($data);
        }else{
            $this->ajaxReturn(null);
        }
    }

}

?>
