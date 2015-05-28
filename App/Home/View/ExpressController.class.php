<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class ExpressController extends IsloginController {

    public function index() {
        // dump($password);
        // dump(session());
         $owner = M("express");
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
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 30);
        $show = $page->show();
     //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        
        $data = $owner//->field('id,name,mobile_phone,fax_mobile,user_name,address,star,lock,list_pic')
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
            if ($name)
                $where['name'] = array('LIKE', '%' . $name . '%');
            
        }
        // dump($where);die();
        $count = $owner->where($where)
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();
     //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        
        $data = $owner->field('id,name,phone,price')
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
        $model = M('express');   
        $data['title'] = "添加快递公司"; 
        $id = I('get.id',0,'intval');
        if ($id != 0) {
            $w = array('id'=>$id);
            $data['title'] = "修改快递公司"; 
              $info = $model->where($w)->find();
              cookie('experss_id',$info['id']);
              $this->assign('info',$info);
           }   
        // dump(cookie());
        $this->assign('data', $data);
        $this->display();
    }
    public function ajax_add(){
         if (IS_AJAX) {
            // dump($_POST);die();  
            $model = M('express');
            $data['name'] = I('post.name');
            $data['phone'] = I('post.phone');
            $data['price'] = I('post.price');            
            // dump() 

            $bool = $model->add($data);
            // dump($bool);die();
            if ($bool) {
                if (cookie('experss_id') != 0) {
                    cookie('experss_id',null);
                    admin_log('编辑快递公司：'.I('post.name'));
                }
                cookie('experss_id',null);
                admin_log('添加快递公司：'.I('post.name'));
                $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn(0);
            }           
        }else{
            $this->error('你无权访问该页面！');
        }
    }
    /**
     * 异步验证快递公司名称是否存在
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-14T11:01:07+0800
     * @return [type]                   [description]
     */
    public function ajax_check_name(){
        $model = M('express');
        $where = 'name = '.I('post.name');
        if (cookie('express_id') != 0) {
               $where = ' and id !='.cookie('express_id');
            } 
        $data = $model->where($w)->find();
        if($data){
            echo 'success';
        }else{
            echo '你输入的快递公司名称已经存在';
        }
    }
    
    /**
     * 删除快递公司
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-20T13:28:31+0800
     * @return [type]                   [description]
     */
    public function del() {
        $id = I('get.id', 0);
        $admin = D('express');
        $result = $admin->where("id=$id")->delete();
        if ($result) {
            admin_log('删除'.$id.'号快递公司');
            $this->success('成功删除快递公司',$_SERVER["HTTP_REFERER"]);
        }
    }
     /**
     * 删除快递公司特殊地区
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-20T13:28:31+0800
     * @return [type]                   [description]
     */
    public function delInfo() {
        $id = I('post.id', 0);
        $admin = D('express_info');
        $result = $admin->where("id=$id")->delete();
        // dump($admin->getlastSql());
        if ($result) {
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }
    public function info(){
        $id=I('get.id',0,'intval');
        if ($id == 0) {
            $this->error('您无权访问该页面！');
        }
        $model = M('express');
        $w = array('id'=>$id);
        $info = $model->where($w)->find();
        $this->assign('info',$info);
        $w = array('eid'=>$id);
        $table = M('express_info')->where($w)->select();
        $this->assign('array',$table);
        $this->assign('info',$info);
        $data['title'] = '快递公司详情';
        $this->assign('data',$data);
        $this->display();
    }
    /**
     * 快递公司特殊地区详情
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-20T13:28:31+0800
     * @return [type]                   [description]
     */
    public function ajaxInfo() {
        $id = I('get.id', 0);
        $admin = D('express_info');
        $result = $admin->where("id=$id")->find();
        if ($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn(0);
        }
    }
    public function expressAdd(){
        $data = M('express_info')->create();
        if($data['eid'] == 0)$this->error('你无权访问该页面！');
        if ($data['id'] != 0) {
            $result = M('express_info')->save($data);
        }else{
            $result = M('express_info')->add($data);
        }
        if ($result) {
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn(0);
        }
    }
    public function editinfo(){
        $id = I('post.id',0,'intval');
        if ($id == 0) {
            $this->error('你没有权限访问该页面！');
        }
        $w = array('id'=>$id);
        $data = M('express_info')->where($w)->find();
        // dump(M('express_info')->getlastSql());
        if ($data) {
            $this->ajaxReturn($data);
        }else{
            $this->ajaxReturn(0);
        }
    }
   

}
