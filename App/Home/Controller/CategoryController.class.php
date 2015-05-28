<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class CategoryController extends IsloginController {
    /*
     查询分页显示
     * @return [type]  
     * @author phper丶li     
    */
    public function index() {
        $category = M("Category");
        $count = $category->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $list = $category->limit($page->firstRow . ',' . $page->listRows)->select();
        $tree = $this->getCatTree($list, 0);
        $arr = array();
        foreach ($tree as $v) {
            array_push($arr, array('cat_id' => $v['cat_id'],'parent_id'=>$v['parent_id'],'lev'=>$v['lev'],'intro'=>$v['intro'], 'cat_name' => str_repeat('&nbsp', $v['lev']) . $v['cat_name']));//,'obj'=>$obj++
        }
 // print_r($arr);exit;
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $arr);
        $this->display();
    }
    /*
     添加分类
     * @return [type]  
     * @author phper丶li     
    */

    public function add() {
    $data['action'] = 'add';  $data['title'] = "添加";  $data['btn'] = "添加分类";  $action = I('post.action');
 if (IS_POST) 
{
   $cat = D('Category');
       $data = $cat->create();
            if ($data) {
                
                if ($action == "add") {
                    
                //    if ($data["parent_id"] !== 0) {  $data['cat_img'] = I('post.thumb_pic'); }
                    
                  //      if ($data["parent_id"] == 0) { $data['cat_img'] = ''; }
                        
                             $data['add_time'] = time();
                             
                                if ($cat->add($data)) { admin_log("添加分类"); $this->success("分类添加成功！", U('/Home/category/index'));
                                
                                } else {  $this->error("分类添加失败！",U('/Home/category/add')); }
                                
                                 } elseif ($action == "edit") {
                                     
                                  $cat_id = I('get.id', 0);
                                  $trees = $this->getTree($cat, I('get.parent_id', 0));
                                  $flag = true;
                             foreach ($trees as $v) {
                                   
                        if ($v['cat_id'] == $cat_id) {
                            
                       $flag = false;
                       
                    break;
                  }
               }
                
            if (!$flag) { $this->error('父栏目选择错误!', U('/Home/category/add', '', false)); }
             
          if ($data["parent_id"] !== 0) {$data['cat_img'] = I('post.thumb_pic');
           
       } else { $data['cat_img'] = '';}
         
     if ($cat->save($data)) { $this->success("修改成功！", U('/Home/category/index'));
       
   } else { $this->error('修改失败!', U('/Home/category/add', '', false)); } }
     
 } else {  $this->error($cat->getError());
   
}
}
        $id = I('get.id', 0);
        $cat = M("Category"); $catlist = $cat->select();
        if ($id) {
            $data['action'] = 'edit';  $data['title'] = "编辑栏目"; $data['btn'] = "编辑";
           
            $catfind = $cat->where("cat_id=$id")->find();
            $cattree = $this->getCatTree($catlist);
            $arr = array();
            $selected = 'selected';
            foreach ($cattree as $v) {
                if ($v['cat_id'] == $catfind['parent_id']) {
                    array_push($arr, array('cat_id' => $v['cat_id'], 'selected' => $selected, 'cat_name' => str_repeat('&nbsp', $v['lev']) . $v['cat_name']));
                } else {
                    array_push($arr, array('cat_id' => $v['cat_id'], 'cat_name' => str_repeat('&nbsp', $v['lev']) . $v['cat_name']));
                }
            }
            $this->assign('arr', $arr);  $this->assign('info', $catfind);
        } else {
            $tree = $this->getCatTree($catlist);
            $arr = array();
            foreach ($tree as $v) {
                array_push($arr, array('cat_id' => $v['cat_id'], 'cat_name' => str_repeat('&nbsp', $v['lev']) . $v['cat_name']));
            }
            $this->assign('arr', $arr);
        }
        $this->assign('data', $data);
        $this->display();
    }
    /*
     删除分类
     * @return [type]  
     * @author phper丶li     
    */
    public function del() {
        $id = I('get.id', 0);
        $cat = M("Category");
        $catlist = $cat->field('cat_id,cat_name,parent_id')
                ->where('parent_id=' . $id)
                ->select();
        if (!empty($catlist)) {
            $url = U('/Home/category/add', '', false);
            $this->error('有子栏目,删除失败');
        }
        $result = $cat->where("cat_id=$id")->delete();
        if ($result) {
            admin_log("删除分类");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    /*
     删除属性
     * @return [type]  
     * @author phper丶li     
    */
    public function cationDel(){
      
        $id = I('get.id', 0);
        $cat = D("Specification");
        $result = $cat->where("id=$id")->delete();
        if ($result) {
              admin_log("删除属性");
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    /*
     商品规格
     * @return [type]  
     * @author phper丶li     
    */
    public function cation() {
        $data['action'] = 'add'; $data['title'] = "添加"; $data['btn'] = "添加分类";
        $action = I('post.action');
        if (IS_POST) {

            $specification = D("specification");
            $data = $specification->create();
            if ($data) {
                if ($action == "add") {
                  
                    if ($specification->add($data)) { admin_log("添加规格");  $this->success("分类添加成功！", U('/Home/category/cation')); } else { $this->error("分类添加失败！", U('/Home/category/cation'));
                  
                } } elseif ($action == "edit") {
                    
                    if ($specification->save($data)) { $this->success('操作成功!', U('/Home/category/cation')); } else { $this->error("修改失败！",U('/Home/category/cation'));  }  }
           
            } else { $this->error($specification->getError()); } }
            
        $specification = M("Specification s");
        $count = $specification->count();
                
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 10);
        $show = $page->show();

        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $slect = $specification->field('s.*,c.cat_id,c.cat_name')
                ->join('wrt_category AS c ON c.cat_id=s.parent_id')
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        foreach ($slect as $v){
            $v['type']= $v['type'] = json_decode($v['type'], true);
           $arr[]=$v;
        }
        $cat = M("Category");
        $list = $cat->where("parent_id!=0")->select();
        $this->assign("currentPage", $currentPage); $this->assign("totalPage", $page->totalPages); $this->assign("page", $show); $this->assign('info', $arr);  $this->assign('list', $list); $this->assign('data', $data);
        $this->display();
    }
    /*
     商品属性添加
     * @return [type]  
     * @author phper丶li     
    */
    public function cationType() {
        $type = I('post.type');
        $action = I('post.action');
        foreach ($type as $v) {
            if ($v !== '') {
                $arr[] = $v;
            }
        }
        $arrJSon = json_encode($arr);

        $specification = M("Specification");
        $data = $specification->create();
        if (IS_POST) {
   
        if ($data) {
       
             $data['type'] = $arrJSon;
            if ($action == "add") {
                
                   $findObject=$specification->where("parent_id=".$data['parent_id'])->find();
                   
            if($findObject){$this->error("该分类已有属性了！",U('/Home/category/cation')); }
        
                if ($specification->add($data)) { admin_log("添加属性"); $this->success('添加成功!', U('/Home/category/cation'));} else {  $this->error("添加失败！",U('/Home/category/cation')); }
          
         }elseif ($action == "edit") {
             
              if ($specification->save($data)) {
        
                $this->success('修改成功!', U('/Home/category/cation')); } else { $this->error("修改失败！",U('/Home/category/cation')); } }
    
         }else { $this->error($specification->getError());  } }  }

    /*
      商品属性详情ajax
     * @return json  
     * @@param POST ID
     * @author phper丶li     
    */  
    public function url_ajaxhinder() {
        $id = I('post.id', 0);
   
        $specification = M("Specification s");
        $info = $specification->field('s.*,c.cat_id,c.cat_name')
                ->join('wrt_category AS c ON c.cat_id=s.parent_id')
                ->where('s.id=' . $id)
                ->find();
        $info['type'] = json_decode($info['type'], true);
        $info['action'] = 'edit';
        $this->ajaxReturn($info);
    }

}

?>
