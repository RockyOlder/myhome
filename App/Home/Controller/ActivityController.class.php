<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class ActivityController extends IsloginController {

    /*
     查询分页显示
     * @return [type]  
     * @author phper丶li     
    */
    public function index() {
        //    echo 1;exit;
        if(IS_GET)
        {
           $statue = I('get.id');
            if ($statue!=='')  
            $where[] = array('v.aid' =>$statue);
         //   $where['o.statue'] = array('LIKE', '%' . $statue . '%');   
        }
        
        $vip = M('VipActGood v');
        $count = $vip->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $find = $vip->field('v.*,w.title,w.start_time,w.end_time')
                ->join('wrt_vip_activity AS w ON w.id=v.aid')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
    //    echo  $vip->getLastSql(); exit;     
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $find);
        $this->display();
    }
    /*
     *添加与修改
     * @return [type]       
     * @author phper丶li     
    */
    public function add() {
        $data['action'] = 'add';  $data['title'] = "添加";  $data['btn'] = "添加商家";
     
        $action = I('post.action');
       
        $vip = M('VipActivity v');  $vipGood = M('VipActGood');
        $count = $vip->count();
        
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        
        $vipLIst = $vip->limit($page->firstRow . ',' . $page->listRows)->select();
        
        foreach ($vipLIst as $v)
        {
            $v['count'] = $vipGood->where("aid=".$v['id'])->count();
            
            $arr[] = $v; 
        }

         $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show);   $this->assign('into', $arr);

        if (IS_POST) {

            $vip = D('VipActivity');
            $data = $vip->create();
            if ($data) {
                $data["start_time"] = strtotime(I('post.start_time'));
                $data["end_time"] = strtotime(I('post.end_time'));
                
                $OBJECT = $vip->field("FROM_UNIXTIME(start_time,'%Y-%m-%d') as start,FROM_UNIXTIME(end_time,'%Y-%m-%d') as end")->select(); 
                
                foreach ($OBJECT as $v)
                {   
                  if($v['start'] == date('Y-m-d',$data["start_time"]))
                  {
                       $this->error("同一天里已有活动！请另外选择时间，添加失败！", U('/Home/Activity/add'));
                      
                  }elseif ($v['end'] == date('Y-m-d',$data["end_time"]))
                  {
                      $this->error("同一天里已有结束活动！请另外选择时间，添加失败！", U('/Home/Activity/add'));  
                  }    
   
                }
                if ($action == "add") {
                    if ($vip->add($data)) {
                            admin_log("添加活动");
                        $this->success("用户添加成功！", U('/Home/Activity/add'));
                    } else {
                        $this->error("用户添加失败！", U('/Home/Activity/add'));
                    }
                } elseif ($action == "edit") {
                      
                    if ($vip->save($data)) {
                        $this->success("修改成功！", U('/Home/Activity/add'));
                    } else {
                        $this->error("用户修改失败！", U('/Home/Activity/add'));
                    }
                }
            } else {
                $this->error($vip->getError());
            }
        }
      /*  $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';
            $data['title'] = "编辑活动";
            $data['btn'] = "编辑";
            $vip = D('VipActivity');
            $vipFind = $vip->where("id=$id")->find();
            $vipFind['start_time'] = date("Y-m-d H:i:s", $vipFind['start_time']);
            $vipFind['end_time'] = date("Y-m-d H:i:s", $vipFind['end_time']);
            $this->assign('info', $vipFind);
        }
       * 
       */
        //$this->redirect("home/del",array());
        $this->assign('data', $data);
        $this->display();
    }
    /*
     *删除活动商品
     * @return [type]       
     * @author phper丶li     
    */
    public function del(){
        $id = I('get.id', 0);
        $goods = D("VipActGood"); $vipGoods=D("Goods");
        $vipFind=$goods->where("id=$id")->find();
        $result = $goods->where("id=$id")->delete();
        if ($result) {
              admin_log("删除活动商品");
           $vipGoods->where("goods_id=".$vipFind['gid'])->setDec('num');
            redirect($_SERVER["HTTP_REFERER"]);
        }
        
    }
    /*
     *删除活动
     * @return [type]       
     * @author phper丶li     
    */
        public function act(){
        $id = I('get.id', 0);
        $goods = D("VipActivity"); 
        $result = $goods->where("id=$id")->delete();

        if ($result) {
              admin_log("删除活动");
            redirect($_SERVER["HTTP_REFERER"]);
        }
        
    }
    /*
     * @ajax查询详请
     * @author phper丶li     
     * @return json
    */

    public function url_ajaxCalendar() {
          // echo 1;exit;
        $id = I('post.id', 0);
        if ($id) {
            $vip = D('VipActivity');
            $vipFind = $vip->where("id=$id")->find();
            $vipFind['start_time'] = date("Y-m-d H:i:s", $vipFind['start_time']);
            $vipFind['end_time'] = date("Y-m-d H:i:s", $vipFind['end_time']);
            $vipFind['action'] = 'edit';
        } else {
            $this->error($vip->getError());
        }
        $this->ajaxReturn($vipFind);
    }
    /*
     * @编辑活动商品
     * @author phper丶li     
     * @return [type] 
    */
    public function saveAct() {
        $action = I('post.action');
        if ($action == "edit") {
            $vip = D('VipActGood');
            if ($vipData = $vip->create()) {
                if ($vip->save($vipData)) {
                    $url = U('/Home/Activity/index');
                    $this->success("修改成功！", $url);
                } else {
                    $this->error("用户修改失败！", 'index');
                }
            } else {
                $this->error($vip->getError());
            }
        }

        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';

            $vip = M('VipActGood');
            $VipActivity = M('VipActivity');
            $vipList = $vip->where("id=$id")->find();
            $aid = $vipList['aid'];
            $actoObject = $VipActivity->where("id=$aid")->find();
            $list = $VipActivity->select();
            //  print_r($actoObject);exit;
            $this->assign('info', $vipList);
            $this->assign('list', $list);
        }
        //$this->redirect("home/del",array());
        $this->assign('save', $actoObject);
        $this->assign('data', $data);
        $this->display();
    }

}

?>
