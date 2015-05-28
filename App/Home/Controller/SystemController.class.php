<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class SystemController extends IsloginController {

    /**
     * apk列表显示页面
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-07T16:37:09+0800
     * @return [type]                   [description]
     */
    public function index() {
        //dump(time());die();
        $table = M("version");
        $where = '1 = 1';
        if (I('request.id') != '') {
            $where .= ' and version ="' . I('request.id') . '"';
        }
        if (I('request.des') != '') {
            $where .= ' and des like "%' . I('request.des') . '%"';
        }
        //dump($where);
        //dump($_POST);die();
        $count = $table->where($where)
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();
        //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type");
        $typeList = $type->select();
        $data = $table
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        foreach ($data as $k => $v) {
            $v[des] = mb_strcut($v[des], 0, 15) . '...';
            $v['url'] = basename($v[url]);
            $data[$k] = $v;
        }
        $this->assign('type', $typeList);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->display();
    }

    /*
     * 
     * 用户反馈
     */

    public function feedback() {

        $UserBack = M("UserBack b");


        //  print_r($categoryObjectSelect);exit;

        $count = $UserBack
                //  ->join('wrt_user AS u ON u.user_id=b.uid')
                ->count();

        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $UserBack
                //->field('b.*,u.user_name')
                //  ->join('wrt_user AS u ON u.user_id=b.uid')
                ->order("add_time desc")
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();

        // print_r($data);exit;
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);

        $this->display();
    }

    public function feedbackDel() {

        $id = I('get.id', 0);

        $UserBack= D('UserBack');
        $result = $UserBack->where("id=" . $id)->delete();
        if ($result) {
            admin_log("删除用户反馈");
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
            $this->error("用户删除失败！", 'index');
        }
    }

    /**
     * 上传和修改apk显示页面
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-07T16:37:39+0800
     * @return [type]                   [description]
     */
    public function upload() {
        $id = I('get.id', 0, 'intval');
        if ($id) {
            $data['title'] = '修改APK信息';
            $data['btn'] = '修改APK信息';
        } else {
            $data['title'] = '上传新版APK';
            $data['btn'] = '上传新版APK';
        }
        $this->assign('data', $data);
        $info = M('version')->where(array('id' => $id))->find();
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 检查版本号是否已经存在
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-07T15:36:22+0800
     * @return [type]                   [description]
     */
    public function check() {
        if (!IS_AJAX) {
            return;
        }
        $version = I('post.version');
        $w = array('version' => $version);
        $bool = M('version')->field('id')->where($w)->find();
        if ($bool) {
            $out = 1;
        } else {
            $out = 0;
        }
        $this->ajaxReturn($out);
    }

    /**
     * 更新apk为最新版
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-07T16:38:05+0800
     * @return [type]                   [description]
     */
    public function update() {
        // echo 1;exit;
        $id = I('get.id', 0);

        $class = D('version');
        $result = $class->where("id=$id")->save(array('is_ok' => 1));
        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /**
     * 将apk更改为旧版停止使用
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-07T16:38:25+0800
     * @return [type]                   [description]
     */
    public function down() {
        // echo 1;exit;
        $id = I('get.id', 0);

        $class = D('version');
        $result = $class->where("id=$id")->save(array('is_ok' => 0));
        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /**
     * 删除apk
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-07T16:39:03+0800
     * @return [type]                   [description]
     */
    public function del() {
        // echo 1;exit;
        $id = I('get.id', 0);

        $class = D('version');
        $result = $class->where("id=$id")->delete();

        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /**
     * 添加和编辑数据方法
     * @author xujun
     * @email  [jun0421@163.com]
     * @date   2015-01-07T16:39:21+0800
     * @return [type]                   [description]
     */
    public function insert() {
        $_POST['add_time'] = time();
        $table = 'version';
        $_POST['is_ok'] = 1;
        //查找版本不能重复如果有重复的版本则提示版本重复
        if ($_POST['id'] == '') {
            unset($_POST['id']);
            //更新其他的版本为
            $bool = M($table)->add($_POST);
            $msg = '添加';
        } else {
            $msg = '编辑';
            $bool = M($table)->save($_POST);
        }

        //dump($bool);die();
        if ($bool) {
            // 更改其他的文件为非最新
            $w = 'id != ' . $bool;
            $data = array('is_ok' => 0);
            M($table)->where($w)->save($data);
            $this->success('文件' . $msg . '成功', U('index'));
        } else {
            $this->error('文件' . $msg . '不成功');
        }
    }

    /**
     * 管理员用来设置自己的商标和名称；
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-11T16:36:08+0800
     * @return [type]                   [description]
     */
    public function setting() {
        if (IS_POST) {
            $bool = M('admin')->save($_POST);
            if ($bool) {
                $this->redirect('Index/start', '', 0, '');
            } else {
                $this->error('修改失败或者你没有做任何修改');
            }
        }
        // 获取用get参数传递过来的id编号
        $id = I('request.id', 0, 'intval');
        if (!$id) {
            $this->error('缺少参数');
        } elseif ($id == '1') {
            $this->error('超级管理员不能修改');
        }
        //查找出用户目前的顶部图片和名称
        $w = array('id' => $id);
        $info = M('admin')->field('id,top_name,top_logo')->where($w)->find();
        $this->assign('info', $info);
        $data['title'] = "设置个性名称";
        $data['btn'] = "提交";
        $this->assign('data', $data);
        $this->display();
    }

    public function word() {
        $data['action'] = 'add';
        $data['title'] = "添加/修改";
        $data['btn'] = "提交关键词";

        $action = I('post.action');

        $word = M('ProKeyword');
        $wordfind = $word->find();

        if ($wordfind) {
            $data['action'] = 'edit';
            $wordfind['wrt'] = implode(",", json_decode($wordfind['wrt']));
            $this->assign('word', $wordfind);
        }
        if (IS_POST) {
            $word = D('ProKeyword');
            $data = $word->create();
            if ($data) {
                $data['wrt'] = json_encode(explode(",", $data['wrt'])); // $dataname = explode(",", $data['pname']);

                if ($action == "add") {

                    if ($wordfind) {
                        $this->error('已有关键设置,添加失败!', U('/Home/System/word', '', false));
                    }   //  $url = U('/Home/proInfo/word', '', false);

                    if ($word->add($data)) {
                        $this->success("用户添加成功！", U('/Home/System/word'));
                    } else {
                        $this->error("用户添加失败！", U('/Home/System/word'));
                    }
                } elseif ($action == "edit") {

                    if ($word->save($data)) {
                        $this->success("修改成功！", U('/Home/System/word'));
                    } else {
                        $this->error("用户修改失败！", U('/Home/System/word'));
                    }
                }
            } else {
                $this->error($word->getError());
            }
        }

        $this->assign('data', $data);
        $this->display();
    }

}