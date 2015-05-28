<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class GoodsController extends IsloginController {
    /*
      查询分页显示
     * @return [type]  
     * @author phper丶li     
     */

    public function index() {
        $id = session("admin.shop_id");///localhost/myhome/Uploads/photo/20150511/5550708b3222a.jpg

        $goods = M("Goods g");
        if (IS_POST) {
            //  print_r($_REQUEST);exit;
            $name = I('post.name');
            $cat_id = I('post.cat_id');
            $parent_id = I('post.parent_id');
            if ($name)
                $where['goods_name'] = array('LIKE', $name . '%');

            if ($cat_id !== '请选择')
                $where['g.cat_id'] = array('LIKE', $cat_id . '%');

            if ($parent_id !== '请选择')
                $where['g.parent_id'] = array('LIKE', $parent_id . '%');
        }
//        if ($id != 0)
//            $where['store_id'] = array('LIKE', $id . '%');
        $category = M("category");
        $categoryObjectSelect = $category->where("parent_id=0")->select();

        //  print_r($categoryObjectSelect);exit;

        $count = $goods->where($where)
            //    ->join('wrt_category AS c ON g.cat_id=c.cat_id')
                ->count();
        $page = initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 8);
        $show = $page->show();
        $currentPage = empty($_GET['p']) ? 1 : intval($_GET['p']);
        $data = $goods//->field('goods_id,goods_name,list_img,description,if_show,price,number,inventory,goods_img,num,suolie_img')
              //  ->join('wrt_category AS c ON g.cat_id=c.cat_id')
                ->where($where)
                ->order("g.add_time desc")
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
       // print_r($data);exit;
        $this->assign("currentPage", $currentPage);
        $this->assign("totalPage", $page->totalPages);
        $this->assign("page", $show);
        $this->assign('data', $data);
        $this->assign('type', $categoryObjectSelect);
        $this->display();
    }

    /*
      商品添加
     * @author phper丶li     
     * @return [type]  
     * @TABLE SpecificationData
     * @TABLE GOODS
     * @TBALE CATEGORY
     * @validation wrt_vialg（parameter[array()] ）
     * @Train of thought  把表达的值过滤并验证，然后循环拼接数组，然后验证分类，接着ADD，接着最新插入的ID 传入SpecificationData表
     */

    public function add() {
        $data['action'] = 'add';
        $data['title'] = "添加";
        $data['btn'] = "添加商品";
        $action = I('post.action');

        $cagy = M("Category");
        $vip = M("Vip v");

        $vipList = $vip->select();
        $cagyList = $cagy->select();

        //   $tree = $this->getCatTree($cagyList);

        $vipFind = $vip->field('v.*,r.REGION_ID,r.REGION_NAME')
                ->join('wrt_region AS r ON v.province=r.REGION_ID')
                ->where('v.store_id=' . session("admin.shop_id"))
                ->find();

        $tree = $this->getCatTree($cagyList);
        $arr = array();
        $obj = 0;
        foreach ($tree as $v) {
            array_push($arr, array('cat_id' => $v['cat_id'], 'parent_id' => $v['parent_id'], 'cat_name' => str_repeat('&nbsp&nbsp&nbsp', $v['lev']) . $v['cat_name'])); //,'obj'=>$obj++
        }
        ///   print_r($tree);exit;
        $pro = $this->getprovence();

        $this->assign('vip', $vipList);
        $this->assign('vipFind', $vipFind);
        $this->assign('list', $arr);
        $this->assign('pro', $pro);
        if (IS_POST) {
        //    print_r($_REQUEST);exit;
            $type = I('post.type');
            $path = I('post.path');
            $cat_id = I('post.cat_id');
            $mid = I('post.mid');
            $did = I('post.did', 0);
            $name = I('post.pic_name');

            foreach ($path as $k => $v) {

                $tem = "";
                $tem['pic'] = $v;
                $tem['mid'] = $mid[$k];
                $tem['name'] = $name[$k];

                $path[$k] = $tem;
            }
            $goods_img = json_encode($path);

            foreach ($type as $key => $v) {
                if ($v !== '') {
                    $arr[$key] = $v;
                }
            }
            $cat = M("Category");

            $catfind = $cat->where("cat_id=$cat_id")->find();
            $restut = $cat->where("cat_id=" . $catfind['parent_id'])->find();
            //     print_r($catfind['parent_id']);exit;
            //   if (!$restut) { $this->error('父栏目不允许选择!',U('/Home/goods/add', '', false));  }

            if ($action == "add") {

                $goods = D('Goods');
                if ($data = $goods->create()) {
                    $data["add_time"] = time();
                    $data["goods_img"] = $goods_img;
                    $data['store_id'] = session("admin.shop_id");
                 //   $data['intro'] = $_POST['intro'];

                    $data['parent_id'] = $catfind['parent_id'];
                    $data["markdown"] = $data['price'];
                    if ($add = $goods->add($data)) {
                        $this->success("商品添加成功！", U('/Home/goods/index'));
                    } else {
                        $this->error("商品添加失败！", U('/Home/goods/index'));
                    }
                }
            } elseif ($action == "edit") {
                $goods = D("Goods");

                if ($data = $goods->create()) {
                    $data["goods_img"] = $goods_img;
                  //  $specificationData = D("SpecificationData");
                    $data['parent_id'] = $catfind['parent_id'];
                //        $_villa = wrt_vialg($obj);
                        if ($goods->save($data)) {
                            $this->success("商品修改成功！", U('/Home/goods/index'));
                        } else {
                            $this->error("商品修改失败！", U('/Home/goods/index'));
                        }
                   
                } else {
                    $this->error($goods->getError());
                }
            }
        }

        $id = I('get.id', 0);
        if (!empty($_POST['goods_id'])) {
            $id = $_POST['goods_id'];
        }
        if ($id) {
            $data['action'] = 'edit';
            $data['title'] = "编辑商品";
            $data['btn'] = "编辑";
            $goods = M("Goods g");
            $goodsFind = $goods//->field('g.*,c.cat_id,v.store_id,v.store_name,c.cat_name')//,r.REGION_ID,r.REGION_NAME
                  //  ->join('wrt_category AS c ON g.cat_id=c.cat_id')
                  //  ->join('wrt_vip AS v ON v.store_id=g.store_id')
                    //      ->join('wrt_region AS r ON g.province=r.REGION_ID')
                    ->where('g.goods_id=' . $id)
                    ->find();
            $goods_img = $goodsFind['goods_img'];
            $goodsFind['goods_img'] = json_decode($goods_img, true);

            $this->assign('info', $goodsFind);
        }
        $this->assign('data', $data);
        $this->display();
    }

    /*
     * 删除商品
     * @author phper丶li     
     * @return [type]       
     */

    public function del() {
        $id = I('get.id', 0);
        $goods = D("Goods");
        $VipActGood = D("VipActGood");
        $info = $VipActGood->where("gid=" . $id)->find();

        if ($info) {
            $this->error('该商品有促销！ 删除失败!', U('/Home/goods/index', '', false));
        } else {
            admin_log("删除商品");
            $result = $goods->where("goods_id=$id")->delete();
        }

        if ($result) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    /*
     * @ajax商品详情
     * @author phper丶li     
     * @return json
     */

    public function url_ajaxhinder() {
        $id = I('post.id', 0);
        $goods_id = I('post.goods_id', 0);
        // echo $goods_id;exit;
        $specification = M("Specification s");
        $specificationData = M("SpecificationData");
        $info = $specification->field('s.*,c.cat_id,c.cat_name')
                //   ->join('wrt_category AS c ON c.parent_id=s.parent_id')
                //    ->where('c.cat_id=' . $id)
                ->join('wrt_category AS c ON c.cat_id=s.parent_id')
                ->where('c.cat_id=' . $id)
                ->find();
        $result = $specificationData->where("goods_id=$goods_id")->find();
        if ($result) {
            $info['type'] = json_decode($result['type'], true);
            $info['dname'] = explode('|', $result['name']);
            $info['did'] = $result['did'];
        } else {
            $info['type'] = json_decode($info['type'], true);
        }
        $info['name'] = explode('|', $info['name']);

        $info['action'] = 'edit';
        $this->ajaxReturn($info);
    }

    /*
     * @ajax商品详情
     * @author phper丶li     
     * @return json
     */

    public function details() {
        $id = I('post.id', 0);
        if ($id) {

            $data['title'] = "编辑商品";
            $data['btn'] = "编辑";
            $goods = M("Goods g");
            $specification = M("specificationData");

            $goodsFind = $goods->field('g.*,c.cat_id,v.store_id,v.store_name,c.cat_name')//,r.REGION_ID,r.REGION_NAME //  ->join('wrt_region AS r ON g.province=r.REGION_ID')
                    ->join('wrt_category AS c ON g.cat_id=c.cat_id')
                    ->join('wrt_vip AS v ON v.store_id=g.store_id')
                    //  ->join('wrt_specification_data AS d ON d.goods_id=g.goods_id')
                    ->where('g.goods_id=' . $id)
                    ->find();
            $result = $specification->where("goods_id=" . $id)->find();
            $goodsFind['info'] = json_decode($result['type'], true);
        } else {
            $this->error($goods->getError());
        }
        $this->ajaxReturn($goodsFind);

        //    $this->display();
    }

    /*
     * @促销商品
     * @author phper丶li     
     * @return json
     */

    public function activity() {
        $action = I('post.action');
        if (IS_POST) {
            if ($action == "edit") {
                $gid = I('post.gid');
                $goods = D("VipActGood");
                $vipGoods = D("Goods");

                $vipList = $goods->where("gid=$gid")->find();
                if (isset($vipList)) {
                    $this->error('该商品已有活动!', U('/Home/Goods/index', '', false));
                }
                $data = $goods->create();
                if ($data) {

                    if ($goods->add($data)) {
                        admin_log("添加促销活动商品");
                        $vipGoods->where("goods_id=" . $gid)->setInc('num');
                        $this->success("添加成功！", U('/Home/Activity/index'));
                    } else {
                        $this->error("用户添加失败！", U('/Home/Activity/index'));
                    }
                } else {
                    $this->error($goods->getError());
                }
            }
        }

        $id = I('get.id', 0);
        if ($id) {

            $data['action'] = 'edit';
            $data['title'] = "编辑商品";
            $data['btn'] = "编辑";

            $goods = M("Goods");
            $vipList = $goods->where("goods_id=$id")->find();
            //   print_r($vipList);exit;
        }
        $vip = D('VipActivity');
        $vipdata = $vip->select();
        $this->assign('vip', $vipdata);
        $this->assign('info', $vipList);
        $this->assign('data', $data);
        $this->display();
    }

    /*
     * @子分类查询
     * @author phper丶li     
     * @return json
     */

    public function CategoryClassification() {

        $id = I('post.id', 0);
        $category = M("category");

        $categoryObjectSelect = $category->where("parent_id=" . $id)->select();
        $this->ajaxReturn($categoryObjectSelect);
    }

}

?>
