<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class AdController extends IsloginController {

    /*
     查询分页显示  广告
     * @return [type]  
     * @author phper丶li     
    */
    public function index() {

        $ad = M("Ad");
        $count = $ad->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 8);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $find = $ad->order('type desc')->limit($page->firstRow . ',' . $page->listRows)->select();
        foreach ($find as $v)
        {
            if($v['type'] == 1)
            {
                $v['type']="生活导航";
            }elseif ($v['type'] == 2) {
                 $v['type']="vip特享";
            }else{
                 $v['type']="家园";
            }
            
            $arr[]=$v;
        }
        
        //  print_r($adcount);exit;
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $arr);
        $this->display();
    }

    /*
      添加与修改 广告
     * @return [type]  
     * @author phper丶li     
    */
    public function add() {
        $data['action'] = 'add';
        $data['title'] = "添加";
        $data['btn'] = "添加广告";
        $action = I('post.action');
   //     $vip = M('VipActivity');

        if (IS_POST) {
            $ad = D('Ad');
            $data = $ad->create();
            if ($data) {
             //   $data["start_time"] = strtotime(I('post.start_time'));
            //   $data["end_time"] = strtotime(I('post.end_time'));
                if ($action == "add") {
                    if ($ad->add($data)) {
                        admin_log("添加广告");
                        $this->success("用户添加成功！", U('/Home/Ad/index'));
                    } else {
                        $this->error("用户添加失败！", U('/Home/Ad/add'));
                    }
                } elseif ($action == "edit") {
                    if ($ad->save($data)) {
                        $this->success("修改成功！", U('/Home/Ad/index'));
                    } else {
                        $this->error("用户修改失败！", U('/Home/vip/add'));
                    }
                }
            } else {
                $this->error($ad->getError());
            }
        }
        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';
            $data['title'] = "编辑商家";
            $data['btn'] = "编辑";
            $ad = M("Ad");
            $vipList = $ad->where("ad_id=$id")->find();
       //     $vipList['start_time'] = date("Y-m-d H:i:s", $vipList['start_time']);
        //    $vipList['end_time'] = date("Y-m-d H:i:s", $vipList['end_time']);
            //   print_r($vipList);exit;
            $this->assign('info', $vipList);
        }
        //$this->redirect("home/del",array());
        $this->assign('data', $data);
        $this->display();
    }
    /*
      广告统计
     * @return [type]  
     * @author phper丶li     
    */
    public function count() {
        $ad = M("Ad");
        $adcount = $ad->order('click desc')->select();
        $this->assign('data', $adcount);
        $this->display();
    }
    /*
      广告删除
     * @return [type]  
     * @author phper丶li     
    */
    public function del() {
       
        $id = I('get.id', 0);

        $Ad = D('Ad');
        $result = $Ad->where("ad_id=".$id)->delete();
        if ($result) {
             admin_log("删除广告");
            redirect($_SERVER["HTTP_REFERER"]);
        }else {
              $this->error("用户删除失败！", 'index');
        }
    }

}

?>
