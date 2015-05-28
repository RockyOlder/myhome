<?php

namespace Home\Model;

use Think\Model\RelationModel;

class DeveloperSumModel extends RelationModel{

  //  public $tableName = 'admin';
//  public static $ins = NULL;
 // public $coon;
//  public $db = NULL; // 是引入的mysql对象


  public function __construct() {
       parent::__construct();
            //    $this->db = \Think\Model::getIns();
  //     $this->coon= new DeveloperSumModel;
      //  $this->getCreate();
  //      $this->coon=  self::getIns();
//        if(self::$ins instanceof self){
//          self::$ins = new DeveloperSumModel();
//        }

    }
    
    protected $_link = array(
        'Admin' => array(
            'mapping_type' => self::HAS_ONE ,
        ///    'class_name' => 'Admin',
            // 定义更多的关联属性
            'foreign_key' => 'developer',
       //     'mapping_name' => 'name',
      //     'condition'=>'developer',
            'mapping_fields'=>'developer as qq,name as dname,id',
        ),
        //// print_r(D("developerSum")->relation(true)->Select());exit;  //调用实例化
    );

    public function SELECT_DEVELOPERSUM_OBJECT($where){
        
  //      $devop = new DeveloperSumModel();

        $admin=M("admin");

        $page=$this->SelectCount($where);
        
        $data = $this->where($where)->order('addtime desc')->limit($page->firstRow . ',' . $page->listRows)->select();

        foreach ($data as $v){  $arr=$admin->field('name,developer')->where("developer=".$v['id'])->find();   if($v['id']==$arr['developer']){ $v['adminUser']=$arr['name']; } $add[]=$v; }  
        
        return $add;
    }
    public function page($count){
        
        return initPage($count, $_COOKIE['n'] ? $_COOKIE['n'] : 5);
       
    }
    public function SelectCount($where){
      
        //->join('wrt_admin AS a ON a.developer=s.id')->field('s.*,a.name as adminUser')->join('wrt_admin AS a ON a.developer=s.id')
       return  $this->page($this->where($where)->count());
     
    }

}

?>
