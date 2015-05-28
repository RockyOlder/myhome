<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class VolumeController extends IsloginController {
    /**
     * [index 电子消费卷列表显示页面]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:17:15+0800
     * @return [type]                   [description]
     */
    public function index() {
        //dump(session());
        //dump(time());die();
        $table = M("order");
        $where = array('is_end'=>0,'cate'=>0,'shop_id'=>session('admin.shop_id'));
        // dump($where);die();
        //dump($_POST);
        // dump($_POST);
        $statue = I('request.statue',0,'intval');
        $number = I('request.number','');
        $check_number = I('request.check_number','');
        if ($statue != 0) {
            $where['check_statue']= $statue-1;
        }else{
            $all = true;
        }
        // dump($number);
        // dump($number != 1);
        if ($number !='') {
            $where['number']= $number;
        }
        if ($check_number !='') {
            $where['check_number']= $check_number;
        }
        // dump($where);
        $count = $table->where($where)
                ->count();
        $page = pageinit($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20,$where);
        $show = $page->show();
     //   print_r($show);exit;
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $type = M("type");
        $typeList = $type->select();
        $field = "oid,number,statue,time,user_id,totle,price,sum,check_statue";
        $data = $table
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        // dump($page->parameter);
        if ($all) {
            $page->parameter['check_statue'] = '-1';
        }
        // dump($str);
        // dump($page->parameter['l.lgname']);
        $this->assign('parameter',$page->parameter);
        $this->assign('type', $typeList);
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        //分配订单状态到页面
        $statue = array('全部','未消费','已经消费','已退款');
        $this->assign('statue',$statue);
        $this->display();
    }

    /**
     * [check 验证电子消费卷验证码]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:17:25+0800
     * @return [type]                   [description]
     */
    public function check() {
        if (IS_AJAX) {
            $number = I('post.data');
            // 查找电子消费码是否未验证
            
            $w = array('check_number'=>$number);
            $bool = M('order')->field('statue,check_statue,oid')->where($w)->find();
            if (is_null($bool)) {
                $out['statue'] = 0;
                $out['msg'] = "你输入的电子劵不存在，请查证后再验证！";
                $this->ajaxReturn($out);
            }
            if ($bool['statue'] == 0) {
                $out['statue'] = 0;
                $out['msg'] = "该订单未付款，请付款后再来验证消费";
                $this->ajaxReturn($out);
            }
            if ($bool['statue'] == 1) {
                $data = array('oid'=>$bool['oid'],'statue'=>6,'check_statue'=>1);
                $n = M('order')->save($data);
                if($n){
                    $out['statue']=1;
                    $out['msg'] = "验证成功";
                }else{
                    $out['statue']=0;
                    $out['msg'] = "验证失败，因为数据修改失败";
                }
                

            }elseif($bool['statue'] == 6){
                $out['statue'] = 0;
                $out['msg'] = "该电子劵已经验证使用";
            }else{
                $out['statue'] = 0;
                $out['msg'] = "你输入的电子劵不存在，请查证后再验证！";
            }

            $this->ajaxReturn($out);
        }
        $this->display();
    }
  
  


public function check_num(){
    if (!IS_AJAX)$this->error('你无权访问该页面！');
    $number = I('post.data');
    // 查找电子消费码是否未验证
    // dump($number);
    
    $w = array('check_number'=>$number,'shop_id'=>session('admin.shop_id'));
    $bool = M('order')->field('oid,number,statue,time,user_id,shop_id,totle,goodid,sum,price,check_number,check_statue,phone')->where($w)->find();
    // dump(M('order')->getlastSql());
    //查找出商品的名称和列表图片
    
    // dump($bool);
    if (!$bool) {
        $out['statue'] = 0;
        $out['msg'] = "你输入的电子劵不存在，请查证后再验证！";
        $this->ajaxReturn($out);
    }else{
        $data = M('lifeGoods')->field('list_pic,lgname')->where(array('lgid'=>$bool['goodid']))->find();
        // dump(M(lifeGood))
        // dump($data);
        $bool['list_pic'] = $data['list_pic'];
        $bool['lgname'] = $data['lgname'];
        if ($bool['statue'] == 0) {
            $bool['statue_msg'] = "未付款";
        }else if($bool['statue'] == 1){
            $bool['statue_msg'] = "已经付款";
        }else if($bool['statue'] == 6){
            $bool['statue_msg'] = "已经验证消费";
        }
        if ($bool['check_statue'] == 0) {
            $bool['check_statue'] = "未消费";
        }else if($bool['check_statue'] == 1){
            $bool['check_statue'] = "已经消费";
        }
        $out['statue'] = 1;
        $out['data'] = $bool;
        $this->ajaxReturn($out);
    }
    
   
        
}
public function change_date(){
    if (!IS_AJAX)$this->error('你无权访问该页面！');
    $number = I('post.data');
    $w = array('oid'=>$number,'shop_id'=>session('admin.shop_id'));
    $bool = M('order')->field('oid,number,statue,time,user_id,shop_id,totle,goodid,sum,price,check_number,check_statue,phone')->where($w)->find();
    
    if ($bool['statue'] == 1) {
        $data = array('oid'=>$bool['oid'],'statue'=>6,'check_statue'=>1);
        $n = M('order')->save($data);
        if($n){
            $out['statue']=1;
            $out['msg'] = "验证成功";
        }else{
            $out['statue']=0;
            $out['msg'] = "验证失败，因为数据修改失败";
        }
        

    }elseif($bool['statue'] == 6){
        $out['statue'] = 0;
        $out['msg'] = "该电子劵已经验证使用";
    }else{
        $out['statue'] = 0;
        $out['msg'] = "你输入的电子劵不存在，请查证后再验证！";
    }
    $this->ajaxReturn($out);
}
}