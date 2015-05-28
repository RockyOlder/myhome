<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class RoleController extends IsloginController {

    // 列表页面
    public function index() {
        // $w = array('pid' =>session('admin.id'));
        $w = "pid =".session('admin.id');
        if (IS_POST) {
            $name = I('post.name');
            if ($name) {
                $w .= " and title like '%$name%'";
            }
            // dump($w);
        }

        $auth = M('auth_group');
        $count = $auth->where($w)->count();
        // dump($auth->getlastSql());
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        //查询用户的权限条件
        $data = $auth->where($w)->limit($page->firstRow . ',' . $page->listRows)->select();
        // dump($auth->getlastSql());
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }

    // 添加页面
    public function add() {
        if (IS_POST) {
            $table = "auth_group";
            $id = I('post.id');
            $arr = array('title' => I('post.title'), 'description' => I('post.des'));
            $arr[pid] = session('admin.id');
            // dump($arr);die();
            if ($id) {//编辑
                $arr['id'] = $id;
                $bool = M($table)->save($arr);
                $msg = "编辑";

            } else {
                $bool = M($table)->add($arr);
                $msg = "添加";
                admin_log('添加角色：'.I('post.title'));
            }
            if ($bool) {
                //$this->success(U('authority',array('id'=>$bool),''),$msg . '成功');
                $this->redirect('authority',array('id'=>$bool), 0, $msg . '成功');
            } else {
                $this->error($msg . '失败');
            }
        }
        $data['action'] = 'add';
        $data['title'] = "添加";
        $data['btn'] = "添加角色";
        $action = 'add';
        $id = I('get.id', 0, 'intval');
        if ($id) {
            $data['action'] = 'add';
            $data['title'] = "修改";
            $data['btn'] = "修改角色";
            $action = 'edit';
            $info = M('auth_group')->where(array('id' => $id))->find();
            $this->assign('info', $info);
        }
        $this->assign('data', $data);
        $this->display();
    }
        public function edit() {
        if (IS_POST) {
            $table = "auth_group";
            $id = I('post.id');
            $arr = array('title' => I('post.title'), 'description' => I('post.des'));
            $arr['id'] = $id;
            // dump($arr);die();            
            $bool = M($table)->save($arr);
            $msg = "编辑";            
            if ($bool) {
                admin_log('编辑角色为：'.I('post.title'));
                $this->success($msg . '成功',U('index','',''));
                // $this->redirect('authority',array('id'=>$bool), 0, $msg . '成功');
            } else {
                $this->error($msg . '失败');
            }
        }
        $data['title'] = "修改";
        $data['btn'] = "修改角色";
        $id = I('get.id');      
        $action = 'edit';
        $info = M('auth_group')->where(array('id' => $id))->find();
        $this->assign('info', $info);
       
        $this->assign('data', $data);
        $this->display();
    }

    // 删除角色方法
    public function del() {
        $id = I('get.id', 0, 'intval');
        $table = "auth_group";
        $msg = "删除";
        $bool = M($table)->delete($id);
        if ($bool) {
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->error($msg . '失败');
        }
    }

    // 分配权限页面
    public function authority() {

        if (IS_POST) {
            //dump($_POST);
            $str = implode(',', $_POST['check']);
            //dump($str);
            $arr = array('id' => I('post.id', 0, 'intval'), 'rules' => $str,'pid'=>I('post.pid'));
            // dump($arr);die();
            $bool = M('auth_group')->save($arr);
            // dump($bool);
            if ($bool) {
                $this->success('权限分配成功',U('index'));
            }else{
                $this->error('添加权限失败');
            }
        }
        $id = I('get.id', 0, 'intval');
        if (!$id) {
            $this->error('请选择角色再来分配权限');
        }
        $info = M('auth_group')->where(array('id' => $id))->find();
        // dump($info);
        // dump(session());
        $info['rules'] = explode(',', $info['rules']);
        // dump(in_array('20', $info['rules']));
        // dump($info);
        $this->assign('info', $info);

        // dump($info);
        $data = $this->getData();
        $this->assign('data', $data);
        $this->display();
    }

    public function getData() {
        $table = "auth_rule";
        $w['type'] =1;
        $w['add_type'] = 1;
        $w['add_pid'] = 0;
        $w = 'type=1 and add_type =1 and add_pid = 0';
        if (session('admin.id') != 1) {
            //查询出用户具有的权限
            $where = array('id'=>session('admin.role_id'));
            // dump($where);
            $data = M('auth_group')->field('rules')->where($where)->find();
            // dump($data);die();
            $id_in= ' and id in ('.current($data).')';
            $w .= $id_in;
        }

        // dump($w);die();
        $field = "id,title";
        $data = M($table)->field($field)->where($w)->select();
        // dump($data);die();
        // 循环顶级数据查询自己的数据
        foreach ($data as $k => $v) {
            // dump($v);die();
            // $where = array('type' => 1, 'status' => 1,'add_pid' => $v['id'],array('id'=>));
            $where = "type = 1 and status = 1 and add_pid = ".$v['id']; 
            // dump($where);
            if ($id_in) {
                $where .= $id_in;
            }
            $son = M($table)->field($field)->where($where)->select();

            // dump($son);die();
            $data[$k]['son'] = $son;
        }
        return $data;
    }

}