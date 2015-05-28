<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class DialyController extends IsloginController {

    public function index() {
        $table = M("admin_log");
        if (IS_POST) {
            $start = I('post.start');
            $start = strtotime($start);
            $end = I('post.end');
            $end = strtotime($end);
            if ($start && $end)
                $where = 'time > '. $start.' and time < '.$end;
            elseif($start)
                $where = 'time >'. $start;
            elseif($end)
                $where = 'time <'.$end;
            // dump($where);die();
        }
        $count = $table->where($where)
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();
     //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type");
        $typeList = $type->select();
        $data = $table->field('id,time,info,ip,admin_name')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        // dump($table->getlastSql());die();
        $this->assign('type', $typeList);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }

    public function wrong() {
        $table = M("error_log");
        if (IS_POST) {
            $start = I('post.start');
            $start = strtotime($start);
            $end = I('post.end');
            $end = strtotime($end);
            if ($start && $end)
                $where = 'time > '. $start.' and time < '.$end;
            elseif($start)
                $where = 'time >'. $start;
            elseif($end)
                $where = 'time <'.$end;
            // dump($where);die();
        }
        $count = $table->where($where)->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();
     //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type");
        $typeList = $type->select();
        $data = $table->field('id,time,ip,url,error')
                ->where($where)->order('time desc')
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        $this->assign('type', $typeList);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }

    
    public function delWrong() {
        // echo 1;exit;
        $id = I('get.id', 0);

        $class = D('error_log');
        $result = $class->where("id=$id")->delete();

        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function delAdminLog() {
        // echo 1;exit;
        $id = I('get.id', 0);

        $class = D('admin_log');
        $result = $class->where("id=$id")->delete();

        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    
  
}