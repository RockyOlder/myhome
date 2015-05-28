<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class HouseController extends IsloginController {

    public function index() {
        
        if (session("admin.developer")!=0) $where['h.developer_id'] = array('LIKE', '%' . session("admin.developer") . '%');
        
        if (IS_POST) {
            $name = I('post.name');
            if ($name)
                $where['h.name'] = array('LIKE', '%' . $name . '%');
        }
        
        $house = M("houses h");
        $count = $house->where($where)->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] :10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $house->field('h.*,d.id as did,d.name as dname')
                ->join('wrt_developer AS d ON d.id=h.developer_id')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }

    public function add() {
        $data['action'] = 'add';
        $data['title'] = "添加";
        $data['btn'] = "添加楼盘";
        $action = I('post.action'); $houses = M('Developer');
      
        $housesList = $houses->select();
        $this->assign('pro', $housesList);
        if (IS_POST) {
        if (session("admin.developer")==0) {$this->error("无权限添加！", U('/Home/house/index')); };
            $houses = D('Houses'); $data = $houses->create();
            if ($data) {
              $_villa = wrt_vialg($data);

              if (!empty($_villa)) {  $this->error($_villa . '  操作失败!',U('/Home/house/add', '', false)); }
                
                $data['developer_id']=session("admin.developer");
                if ($action == "add") {
                    if ($houses->add($data)) { $this->success("用户添加成功！", U('/Home/house/index')); } else {   $this->error("用户添加失败！", U('/Home/house/add'));  }
              
                  } elseif ($action == "edit") {
                        
                    if ($houses->save($data)) {  $this->success("修改成功！", U('/Home/house/index')); } else { $this->error('修改失败!', U('/Home/house/add')); } }
                    
            } else { $this->error($houses->getError());  } }
        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';
            $data['title'] = "编辑小区";
            $data['btn'] = "编辑";
            $house = M("houses h");
            $info = $house->field('h.*,d.id as did,d.name as dname')
                    ->join('wrt_developer AS d ON d.id=h.developer_id')
                    ->where('h.id=' . $id)
                    ->find();

            $this->assign('info', $info);
        }
        $this->assign('data', $data);
        $this->display();
    }
    
    public function del() {
       
        $id = I('get.id', 0);

        $house = M("houses");$property=M("property");
        $info= $property->where("property_id=".$id)->find();
        
        if($info){ $this->error('该楼盘有物业！ 删除失败!',U('/Home/house/index', '', false));}else{  $result = $house->where("id=".$id)->delete();}
       
        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    

}

?>
