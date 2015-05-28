<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class ProInfoController extends IsloginController {

    public function index() {
        $house = M("ProNotice p");
         $id=I("get.id");
         
         if($id){   $sql="UPDATE ".C('DB_PREFIX')."pro_notice SET sorc=1 WHERE id=".$id; M()->execute($sql);   $sql="UPDATE ".C('DB_PREFIX')."pro_notice SET sorc=0 WHERE id!=".$id;  M()->execute($sql);  }
       
         if (session("admin.property")!=0) $where['p.proid'] =  session("admin.property") ;
         if (session("admin.village")!=0) 
          $where['p.vid'] = array('in', '(0,' . session("admin.village") . ')');
        $where['p.pid'] = 0;
        if (IS_POST) {
            $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
              }
         
         
        $count = $house->join('wrt_property AS y ON p.proid=y.id')->where($where)->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 20);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
       
        $data = $house->field('p.*,y.id as yid,y.pname')->join('wrt_property AS y ON p.proid=y.id')->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('sorc desc')->select();
        //  echo  $house->getLastSql(); exit;
      //  if (empty($data)) { $this->redirect("add"); }
    
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('data', $data);
        $this->display();
    }

    public function add() {
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "添加资讯";
     
        $action = I('post.action');  $pro = M('property');
      
        $proList = $pro->select();
        $this->assign('pro', $proList);
     
        if (IS_POST) {
            $pntice = D('ProNotice'); $data = $pntice->create();
            
      if (session("admin.property")==0) {$this->error("无权限添加！", U('/Home/proInfo/examine')); };
         
           if ($data) {
              if ($action == "add") {
                $data["add_time"] = strtotime(I('post.add_time'));   $data["pass_time"] = strtotime(I('post.pass_time')); $data['author']=session('admin.true_name'); $data['vid']=session("admin.village");
                $_villa = wrt_vialg($data);

                   if (!empty($_villa)) {  $this->error($_villa . '  添加失败!',U('/Home/proInfo/add', '', false)); }
                
                   $data['content'] = $_POST['content']; $data['proid'] = session("admin.property");
                    if ($pntice->add($data)) {admin_log("添加社区资讯");  $this->success("社区资讯添加成功！", U('/Home/proInfo/index')); } else { $this->error("社区资讯添加失败！", U('/Home/proInfo/index')); }
                
             } elseif ($action == "edit") {
             
                    if ($pntice->save($data)) { $this->success("修改成功！", U('/Home/proInfo/index'));} else { $this->error('修改失败!',U('/Home/proInfo/add', '', false));
                        
           } } } else { $this->error($pntice->getError()); } }
                    
        $id = I('get.id', 0);
        if ($id) {
            $data['action'] = 'edit';
            $data['title'] = "编辑资讯";
            $data['btn'] = "编辑";
            $house = M("ProNotice p");
            $info = $house->field('p.*,y.id as yid,y.pname')
                    ->join('wrt_property AS y ON p.proid=y.id')
                    ->where('p.id=' . $id)
                    ->find();
            $info['add_time'] = date("Y-m-d H:i:s", $info['add_time']);
            $info['pass_time'] = date("Y-m-d H:i:s", $info['pass_time']);
            $this->assign('info', $info);
        }

        if (I('get.nid'))
        {  
            $arr=session('admin'); $arr['table']=base64_encode('ProNotice');  $arr['proid']=I('get.nid'); 
           
            $arr['tag'] = "资讯列表";
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('activein'); 
        }

        $this->assign('data', $data);
        $this->display();
    }

    public function word() {
        $data['action'] = 'add'; $data['title'] = "添加/修改"; $data['btn'] = "提交关键词";
    
        $action = I('post.action');
     
        $word = M('ProKeyword');  $wordfind = $word->find();
     
        if ($wordfind) {
            $data['action'] = 'edit';
            $wordfind['pname'] = implode("|", json_decode($wordfind['pname']));
            $this->assign('word', $wordfind);
        }
        if (IS_POST) {
             $word = D('ProKeyword');
             $data = $word->create();
          if ($data) { 
              $data['pname'] = json_encode(explode("|", $data['pname']));// $dataname = explode(",", $data['pname']);
              
             if ($action == "add") {

                if ($wordfind) {  $this->error('已有关键设置,添加失败!',U('/Home/proInfo/word', '', false));  }   //  $url = U('/Home/proInfo/word', '', false);

                    if ($word->add($data)) { $this->success("用户添加成功！", U('/Home/ProInfo/word')); } else { $this->error("用户添加失败！", U('/Home/ProInfo/word'));}
                
              } elseif ($action == "edit") {
                    
                    if ($word->save($data)) { $this->success("修改成功！", U('/Home/ProInfo/word')); } else { $this->error("用户修改失败！", U('/Home/ProInfo/word'));
                        
          } } } else { $this->error($word->getError());  } }
          
        $this->assign('data', $data);
        $this->display();
    }

    public function examine() {
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "添加调查";$data['name'] = session("admin.true_name");
         //   print_r($data['name']);exit;
        $action = I('post.action');
        if (session("admin.property")!=0) $where['proid'] = session("admin.property");
        if (session("admin.village")!=0) 
          $where['vid'] = array('in', '(0,' . session("admin.village") . ')');
        // $where['pid'] = 0;
        if (IS_POST) {
          
            if (session("admin.property")==0) {$this->error("无权限添加！", U('/Home/proInfo/examine')); };
            $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
            
        //   if (I('post.pid')) {  session('table', base64_encode('ProSurvey')); session('pid', I('post.pid'));$this->redirect('urlAJAX'); }
      
        if (I('post.pid'))
        {  

            $arr=session('admin'); $arr['table']=base64_encode('ProSurvey');  $arr['pid']=I('post.pid'); 
           
            session("admin",$arr);   cookie('admin',$arr);

          
            $this->redirect('urlAJAX'); 
        }
            
           $prosuvey = D('ProSurvey');  $data = $prosuvey->create();
             
          if ($data) {
              
            $_villa = wrt_vialg($data);
            if (!empty($_villa)) {  $this->error($_villa . '  添加失败!',U('/Home/proInfo/examine', '', false)); }
              
               if ($action == "add") { 
                   $data['Release_time'] = time(); $data['add_time'] = time(); $data['proid'] = session("admin.property"); $data['author']=session('admin.true_name');  $data['vid']=session("admin.village");
                   $data['pass_time'] =strtotime("next Month");
                    if ($prosuvey->add($data)) { admin_log("添加社区调查"); $this->success("调查添加成功！", U('/Home/proInfo/examine')); } else { $this->error("调查添加失败！", U('/Home/proInfo/examine')); }
                
               } elseif ($action == "edit") {
                  
                    if ($prosuvey->save($data)) {$this->success('修改成功!', U('/Home/proInfo/examine')); } else { $this->error("修改失败！", U('/Home/proInfo/examine'));
                        
               } } } }
                    
        $prosury = M("ProSurvey"); $count = $prosury->where($where)->count();
      
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $prolist = $prosury->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
       
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('pro', $prolist);
               
        $this->assign('data', $data);
        $this->display();
    }
    public function urlAJAX() {
        $id = session('admin.pid');

        $table = base64_decode(session('admin.table'));
        $pro = M("$table");
        $info = $pro->where("id=" . $id)->find();
        $info['action'] = 'edit';
        $this->ajaxReturn($info);
    }
    //ProSurvey
    public function Nodel() {
        $id = I('get.id', 0);
         $prosuvey = D('ProNotice');
         if(session("admin.property")!=0 && session("admin.village")==0){
           $result = $prosuvey->where("id=$id")->delete();  
         }else{
            // echo 1;exit;
            $find= $prosuvey->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/index'));  
            }else{
                $result = $prosuvey->where("id=$id")->delete();  
            }
         }
 
      
        if ($result) {
            admin_log("删除社区资讯");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
    public function Sudel() {
        $id = I('get.id', 0);
         $prosuvey = D('ProSurvey');
         if(session("admin.property")!=0 && session("admin.village")==0){
           $result = $prosuvey->where("id=$id")->delete();  
         }else {
            $find = $prosuvey->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/examine'));  
            }else{
              $result = $prosuvey->where("id=$id")->delete();  
            }
         }
        D("pro_survey_sign")->where("sid=".$id)->delete();  
         
        if ($result) {
            admin_log("删除社区调查");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    //ProRepair
    public function DecorateDel() {
        $id = I('get.id', 0);
        
         $Pro = D('ProDecorate');
         if(session("admin.property")!=0 && session("admin.village")==0){
     //   echo $id;exit;
           $result = $Pro->where("id=".$id)->delete();  
      //  echo $Pro->getLastSql();exit;
         }else {
               
            $find = $Pro->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/decorate'));  
            }else{
              $result = $Pro->where("id=$id")->delete(); 

            }
         }
         
        if ($result) {
          
            admin_log("删除装修申请");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    } 
    public function repairDel() {
        $id = I('get.id', 0);
        
         $Pro = D('ProComplaints');
         if(session("admin.property")!=0 && session("admin.village")==0){
           $result = $Pro->where("id=".$id)->delete();  
         }else {
               
            $find = $Pro->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/repair'));  
            }else{
              $result = $Pro->where("id=$id")->delete(); 

            }
         }     
        if ($result) {
          
            admin_log("删除投诉建议");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    } 
    
    public function hinderDel() {
        $id = I('get.id', 0);
        
         $Pro = D('ProRepair');
         if(session("admin.property")!=0 && session("admin.village")==0){
           $result = $Pro->where("id=".$id)->delete();  
         }else {
               
            $find = $Pro->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/hinder'));  
            }else{
              $result = $Pro->where("id=$id")->delete(); 

            }
         }     
        if ($result) {
          
            admin_log("删除维修保障");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }//ProActivity  active
    public function activeDel() {
        $id = I('get.id', 0);
        
         $Pro = D('ProActivity');
         if(session("admin.property")!=0 && session("admin.village")==0){
           $result = $Pro->where("id=".$id)->delete();  
         }else {
               
            $find = $Pro->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/active'));  
            }else{
              $result = $Pro->where("id=$id")->delete(); 

            }
         }     
        if ($result) {
          
            admin_log("删除邻里活动");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function carpoolDel() {
        $id = I('get.id', 0);
        
         $Pro = D('ProCar');
         if(session("admin.property")!=0 && session("admin.village")==0){
           $result = $Pro->where("id=".$id)->delete();  
         }else {
               
            $find = $Pro->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/carpool'));  
            }else{
              $result = $Pro->where("id=$id")->delete(); 

            }
         }     
        if ($result) {
          
            admin_log("删除邻里拼车");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function swapDel() {
        $id = I('get.id', 0);
        
         $Pro = D('ProFetch');
         if(session("admin.property")!=0 && session("admin.village")==0){
           $result = $Pro->where("id=".$id)->delete();  
         }else {
               
            $find = $Pro->where("id=".$id)->find();
            if($find['vid']==0){
              $this->error("删除失败！，该信息所属物业", U('/Home/proInfo/swqp'));  
            }else{
              $result = $Pro->where("id=$id")->delete(); 

            }
         }     
        if ($result) {
          
            admin_log("删除邻里拼车");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
    
    public function carpool() {
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "添加调查";

        if (session("admin.property")!=0) 
          $where['proid'] = session("admin.property");
        if (session("admin.village")!=0) 
          $where['vid'] = array('in', '(0,' . session("admin.village") . ')');
        $action = I('post.action');$role=0;
         if ($role!=='')
                $where['pid'] =  $role ;
     
        if (IS_POST) {
            $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
            
            $procar = D("ProCar"); $data = $procar->create();
          if ($data) {
                
              if ($action == "add") { $data['add_time'] = time();
            
                    if ($procar->add($data)) { $this->success("用户添加成功！",  U('/Home/proInfo/carpool')); } else {$this->error("用户添加失败！",  U('/Home/proInfo/carpool'));}
                
               } elseif ($action == "edit") {
              
                    $procar->where(array('id' => I('post.yid')))->setInc('num');
                    $data['pid'] = I('post.yid');$data['add_time'] = time();
              
                if ($procar->add($data)) { $this->success('回复成功!', U('/Home/proInfo/carpool')); } else { $this->error("回复失败！", U('/Home/proInfo/carpool'));
                        
            } } }  }
            
        $procar = M("ProCar"); $count = $procar->where($where)->count();
       // dump($where);die();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $prolist = $procar->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
       
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('pro', $prolist);
            
        $arr=session('admin'); $arr['table']=base64_encode('ProCar'); 
        if (I('get.id'))
        {  
           $arr['proid']=I('get.id'); 
           $arr['tag'] = "邻里拼车";
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('activein'); 
        }       
        if (I('post.pid'))
        {  
           $arr['pid']=I('post.pid'); 
           
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('urlAJAX'); 
        }    
        
        //session('table', base64_encode('ProCar'));
      //  if (I('get.id')) { session('id', I('get.id'));$this->redirect('activein');}
       // if (I('post.pid')) { session('pid', I('post.pid'));$this->redirect('urlAJAX'); }
        $this->assign('data', $data);
        $this->display();
    }

    public function active() {
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "添加调查";
     
        if (session("admin.property")!=0) $where['proid'] =session("admin.property");
        if (session("admin.village")!=0) 
          $where['vid'] = array('in', '(0,' . session("admin.village") . ')');
        $action = I('post.action');
        $role=0;
         if ($role!=='')
                $where['pid'] = $role ;
        
        if (IS_POST) {
            $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
            
            $proactiv = D("ProActivity");  $data = $proactiv->create();
          if ($data) {
              
              if ($action == "add") {$data['add_time'] = time();
                   
                  if ($proactiv->add($data)) { $this->success("用户添加成功！", U('/Home/proInfo/active')); } else { $this->error("用户添加失败！", U('/Home/proInfo/active')); }
                
              } elseif ($action == "edit") {
              
                   $proactiv->where(array('id' => I('post.yid')))->setInc('num');
                    $data['pid'] = I('post.yid');$data['add_time'] = time();
              
                if ($proactiv->add($data)) { $this->success('回复成功!', U('/Home/proInfo/active')); } else { $this->error("回复失败！", U('/Home/proInfo/active'));
                        
            } } }  }
            
        $proactiv = M("ProActivity");  $count = $proactiv->where($where)->count();
        //    session('table', null); // 删除table //   session('id', null); // 删除id
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $prolist = $proactiv->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $proActivitySign=M("ProActivitySign");
        
        foreach ($prolist as $v){
            
            $v['count']=$proActivitySign->where("aid=".$v['id'])->count();
            $arr[]=$v;
        }
        $arrObjectProActivity = $this->getvillage($arr);
     //   print_r($arrObjectProActivity);exit;
        
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('pro', $arrObjectProActivity); 
           
        $arr=session('admin'); $arr['table']=base64_encode('ProActivity'); 
        if (I('get.id'))
        {  
           $arr['proid']=I('get.id'); 
           $arr['tag'] = "邻里活动";
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('activein'); 
        }       
        if (I('post.pid'))
        {  
           $arr['pid']=I('post.pid'); 
           
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('urlAJAX'); 
        }   
        
      //  session('table', base64_encode('ProActivity'));
        
       // if (I('get.id')) { session('id', I('get.id'));$this->redirect('activein');}
      //  if (I('post.pid')) { session('pid', I('post.pid'));$this->redirect('urlAJAX'); }
        
        $this->assign('data', $data);
        $this->display();
    }

    public function hinder() {
        $data['action'] = 'add'; $data['title'] = "添加";  $data['btn'] = "添加调查";
      
        if (session("admin.property")!=0) $where['proid'] = session("admin.property");
        if (session("admin.village")!=0) 
          $where['vid'] = array('in', '(0,' . session("admin.village") . ')');
        $action = I('post.action');  
        $role=0;
         if ($role!=='')
                $where['pid'] =  $role ;
        
        if (IS_POST) {
            $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
            
            $prorepair = D("ProRepair");  $data = $prorepair->create();
         if ($data) {
             
             if ($action == "add") { $data['time'] = time();
            
                 if ($prorepair->add($data)) { $this->success("用户添加成功！", U('/Home/proInfo/hinder'));} else { $this->error("用户添加失败！", U('/Home/proInfo/hinder')); }

             } elseif ($action == "edit") {
                    $prorepair->where(array('id' => I('post.yid')))->setInc('num');
                    $data['pid'] = I('post.yid');$data['time'] = time();
        
                 if ($prorepair->add($data)) { $this->success('回复成功!', U('/Home/proInfo/hinder')); } else { $this->error("回复失败！", U('/Home/proInfo/hinder'));
                        
            } } } else { $this->error($prorepair->getError()); } }
            
        $prorepair = M("ProRepair"); $count = $prorepair->where($where)->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $prolist = $prorepair->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        $prolist = $this->getvillage($prolist);
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('pro', $prolist);
        
        $arr=session('admin'); $arr['table']=base64_encode('ProRepair'); 
        if (I('get.id'))
        {  
           $arr['proid']=I('get.id'); 
           $arr['tag'] = "维修保障";
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('activein'); 
        }       
        if (I('post.pid'))
        {  
           $arr['pid']=I('post.pid'); 
           
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('urlAJAX'); 
        }   
      //  session('table', base64_encode('ProRepair'));
      //  if (I('get.id')) { session('id', I('get.id'));$this->redirect('activein');}
     //   if (I('post.pid')) { session('pid', I('post.pid'));$this->redirect('urlAJAX'); }

        $this->assign('data', $data);
        $this->display();
    }

    public function decorate() {
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "添加调查"; $action = I('post.action');
     
        if (session("admin.property")!=0) $where['proid'] = session("admin.property");
        if (session("admin.village")!=0) 
          $where['vid'] = array('in', '(0,' . session("admin.village") . ')');
         $role=0;
         if ($role!=='')
                $where['pid'] =  $role;

        if (IS_POST) {
            $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
         
            $pro = D("ProDecorate"); $data = $pro->create();
           if ($data) {

               
              if ($action == "add") {$data['time'] = time();
              
                   if ($pro->add($data)) { $this->success("用户添加成功！", U('/Home/proInfo/decorate'));} else { $this->error("用户添加失败！", U('/Home/proInfo/decorate')); }
                
               } elseif ($action == "edit") {
                    $pro->where(array('id' => I('post.yid')))->setInc('num');
                    $data['pid'] = I('post.yid');$data['time'] = time();
                  
                    if ($pro->add($data)) { $this->success('回复成功!', U('/Home/proInfo/decorate'));} else { $this->error("回复失败！", U('/Home/proInfo/decorate'));
                        
             } } }  }
    
        $pro = M("ProDecorate"); $count = $pro->where($where)->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $prolist = $pro->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
    //    print_r($prolist);exit;
        $prolist = $this->getvillage($prolist);

        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages);  $this->assign("page", $show); $this->assign('pro', $prolist);
            
        $arr=session('admin'); $arr['table']=base64_encode('ProDecorate'); 
        if (I('get.id'))
        {  
           $arr['proid']=I('get.id'); 
           $arr['tag'] = "装修申请";
           
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('activein'); 
        }       
        if (I('post.pid'))
        {  
           $arr['pid']=I('post.pid'); 
           
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('urlAJAX'); 
        }   
     //   session('table', base64_encode('ProDecorate'));
      //  if (I('get.id')) { session('id', I('get.id'));$this->redirect('activein');}
     //   if (I('post.pid')) { session('pid', I('post.pid'));$this->redirect('urlAJAX'); }
       
        $this->assign('data', $data);
        $this->display();
    }

    public function repair() {
        $data['action'] = 'add'; $data['title'] = "添加"; $action = I('post.action');
        
        if (session("admin.property")!=0) $where['proid'] = session("admin.property");
        if (session("admin.village")!=0) 
          $where['vid'] = array('in', '(0,' . session("admin.village") . ')');
        $role=0;
         if ($role!=='')
                $where['pid'] =  $role;
        if (IS_POST) {

           $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
         
            $pro = D("ProComplaints"); $data = $pro->create();
          if ($data) {

              if ($action == "add") {   $data['time'] = time();
            
                if ($pro->add($data)) { $this->success("用户添加成功！", U('/Home/proInfo/repair'));} else {$this->error("用户添加失败！", U('/Home/proInfo/repair'));}

              } elseif ($action == "edit") {
                    $pro->where(array('id' => I('post.yid')))->setInc('num');
                    $data['pid'] = I('post.yid'); $data['time'] = time();
                    
                 if ($pro->add($data)) { $this->success('回复成功!', U('/Home/proInfo/repair'));} else {$this->error("回复失败！", U('/Home/proInfo/repair'));
                        
            } } } }
            
        $pro = M("ProComplaints"); $count = $pro->where($where)->count();
      
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $prolist = $pro->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
     //   echo  $pro->getLastSql(); exit;
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('pro', $prolist);
         
        $arr=session('admin'); $arr['table']=base64_encode('ProComplaints'); 
        if (I('get.id'))
        {  
           $arr['proid']=I('get.id'); 
           $arr['tag'] = "投诉建议";
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('activein'); 
        }       
        if (I('post.pid'))
        {  
           $arr['pid']=I('post.pid'); 
           
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('urlAJAX'); 
        }   
 //       session('table', base64_encode('ProComplaints'));
      //  if (I('get.id')) { session('id', I('get.id'));$this->redirect('activein');}
     //   if (I('post.pid')) { session('pid', I('post.pid'));$this->redirect('urlAJAX'); }
        $this->assign('data', $data);
        $this->display();
    }
    public function activein() {
   //  print_r(session("admin.tag"));exit;
        $id = session('admin.proid');
    //   
        $table = base64_decode(session('admin.table'));

        $pro = M("$table");
        $user=M("User");
        
        $info = $pro->where("id=" . $id)->find();  $count = $pro->where("pid=" . $info['id'] . " and sheild=0")->count();
             
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15); $show = $page->show();
      
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        
        $info['list'] = $pro->where("pid=" . $info['id'] . " and sheild=0")->limit($page->firstRow . ',' . $page->listRows)->select();
       
        $pid = I('get.sheild');
      
        if ($pid) {$obj = D("$table");if ($obj->where(array('id' => $pid))->setInc('sheild')) {redirect($_SERVER["HTTP_REFERER"]); } }
        
        $info['content'] =$str= htmlspecialchars_decode($info['content'] );
   
        $pic= json_decode($info['pic'],true);
  //  print_r();exit;
        $info['pic']=$pic[0]['path'];
        $user_info = $user->field("true_name")->where("user_id=" . $info['uid'])->find(); 
        $info['username']=$user_info['true_name'];
        $tag=session("admin.tag");
      //   echo $tag;exit;
        $this->assign("tag",$tag );
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('info', $info);
        $this->display();
    }

    public function swap() {
        $name = session('admin.name');
      
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "闲置交换"; $action = I('post.action');
        
        if (session("admin.property")!=0) $where['proid'] = session("admin.property");
        if (session("admin.village")!=0) 
        $where['vid'] = array('in', '(0,' . session("admin.village") . ')');
         $role=0;
         if ($role!=='')
                $where['pid'] =  $role ;
        if (IS_POST) {
           $title = I('post.title');
            if ($title)
                $where['title'] = array('LIKE', '%' . $title . '%');
            
            $pro = D("ProFetch"); $data = $pro->create();
          if ($data) {
              
              if ($action == "add") {
                    $data['add_time'] = time();
                    if ($pro->add($data)) {$this->success("用户添加成功！",  U('/Home/proInfo/swap'));} else { $this->error("用户添加失败！", 'index');}

               }  elseif ($action == "edit") {
              
                    $pro->where(array('id' => I('post.yid')))->setInc('num');
                    $data['pid'] = I('post.yid');$data['add_time'] = time();
              
                   if ($pro->add($data)) { $this->success('回复成功!', U('/Home/proInfo/swap')); } else { $this->error("回复失败！", U('/Home/proInfo/swap'));
                        
            } } } else {$this->error($pro->getError()); } }
                 
        $pro = M("ProFetch"); $count = $pro->where($where)->count();
        //  session('table', null); // 删除table//  session('id', null); // 删除id
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 15);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $prolist = $pro->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
     
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages);  $this->assign("page", $show); $this->assign('pro', $prolist);
         
        $arr=session('admin'); $arr['table']=base64_encode('ProFetch'); 
        if (I('get.id'))
        {  
           $arr['proid']=I('get.id'); 
           $arr['tag'] = "闲置交换";
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('activein'); 
        }       
        if (I('post.pid'))
        {  
           $arr['pid']=I('post.pid'); 
           
            session("admin",$arr);   cookie('admin',$arr);
        
            $this->redirect('urlAJAX'); 
        }   
       // session('table', base64_encode('ProFetch'));
      //  if (I('get.id')) { session('id', I('get.id'));$this->redirect('activein');}
      //  if (I('post.pid')) { session('pid', I('post.pid'));$this->redirect('urlAJAX'); }
        $this->assign('username', $name);
        $this->assign('data', $data);
        $this->display();
    }

    function getvillage($data){
       //    print_r(session('admin.village'));exit;
      if (session('admin.village') != 0) {
            $village = M('village')->field('name')->where(array('id'=>session('admin.village')))->find();
            $this->assign('village',$village['name']);
                //   print_r($village);exit;
            return $data;
        }else{
          // dump($data);
            //循环查找出用户信息所属的
            foreach ($data as $k => $v) {
              $village = M('village')->field('name')->where(array('id'=>$v['vid']))->find();
              // dump($village);
              $v['vname']=$village['name'];
              $data[$k] = $v;
            }
            return $data;
        }
    }

}

?>
