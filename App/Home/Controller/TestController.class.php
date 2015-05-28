<?php
namespace Home\Controller;
use Home\Controller\IsloginController;
/**
* 城市管理页面
*/
class TestController extends IsloginController
{
	function index(){
        if(IS_POST){
            $path = I('post.path');
            $mid = I('post.mid');
            $name = I('post.pic_name');
            foreach ($path as $k => $v) {
                $tem = "";
                $tem['pic'] = $v;
                $tem['mid'] = $mid[$k];
                $tem['name'] = $name[$k];
                // dump
                $path[$k] =$tem;
            }
            $path = json_encode($path);
            dump(strlen($path));
            $path = json_decode($path);
            // for
            dump($path);die();
        }
		// dump($_SERVER['HTTP_HOST']);
// 		$data=$this->fullcity();
		//dump($data);
		$this->display();
	}
	
   public function upload(){
     if (!empty($_FILES)) {
     	// dump($_FILES);
            //图片上传设置
            $config = array(
				'maxSize' => 3145728,
				'rootPath' => './Uploads/list/',
				'savePath' => '',
				'saveName' => array('uniqid',''),
				'exts' => array('jpg', 'gif', 'png', 'jpeg'),
				'autoSub' => true,
				'subName' => array('date','Ymd'),
				);

            $upload = new \Think\Upload($config);// 实例化上传类                  
            $info = $upload->upload();
            // dump($images);
            //判断是否有图
            if($info){
                // dump($info);die();
                $out['path']='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/Uploads/'.$info['file_upload']['savepath'].$info['file_upload']['savename'];
                $image = new \Think\Image();

                foreach($info as $file) {
                    $thumbWidth = array('mid'=>700,'min'=>200);
                    $thumb_file = './Uploads/' . $file['savepath'] . $file['savename'];
                    foreach ($thumbWidth as $k=> $v) {
                       $save_path = './Uploads/' .$file['savepath']. $v."_" . $file['savename'];
                        // $image->open( $thumb_file )->save( $thumb_file );
                    // $data1=$image->open( $thumb_file )->thumb( $thumbWidth, $thumbHeight )->save( $save_path );
                        $image->open( $thumb_file )->thumb( $v, $v )->save( $save_path );
                        $out[$k]='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/Uploads/'.$file['savepath']. $v.'_' .$file['savename'];
                        //  $arr[]= array(
                        // 'status' => 1,
                        // 'savepath' => $file['savepath'],
                        // 'savename' => $file['savename'],
                        // 'pic_path' => $file['savepath'] . $file['savename'],
                        // 'mini_pic' => $file['savepath']. $v.'_' .$file['savename']
                        // ); 
                    }
                    $out['path']='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/Uploads/'.$file['savepath'] .$file['savename'];
                    // dump($data);
                   // echo $out;die();
                    // dump($arr); 
            	// dump($images);die();
                    // dump($out);die();
                // return  $out;die();
                //返回文件地址和名给JS作回调用
                // dump($out);
                $this->ajaxReturn($out);
                // dump($info);die();
            }
            
        }else{
                echo $this->error($upload->getError());//获取失败信息
            }
    }
    }
    public function photo(){
     if (!empty($_FILES)) {
        // dump($_FILES);
            //图片上传设置
            $config = array(
                'maxSize' => 3145728,
                'rootPath' => './Uploads/',
                'savePath' => 'photo/',
                'saveName' => array('uniqid',''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date','Ymd'),
                );

            $upload = new \Think\Upload($config);// 实例化上传类                  
            $info = $upload->upload();
            // dump($images);
            //判断是否有图
            if($info){
                // dump($info);die();
                $out['path']='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/Uploads/'.$info['file_upload']['savepath'].$info['file_upload']['savename'];
                $image = new \Think\Image();

                foreach($info as $file) {
                    $thumbWidth = array('mid'=>700);
                    $thumb_file = './Uploads/' . $file['savepath'] . $file['savename'];
                    foreach ($thumbWidth as $k=> $v) {
                       $save_path = './Uploads/' .$file['savepath']. $v."_" . $file['savename'];
                        // $image->open( $thumb_file )->save( $thumb_file );
                    // $data1=$image->open( $thumb_file )->thumb( $thumbWidth, $thumbHeight )->save( $save_path );
                        $image->open( $thumb_file )->thumb( $v, $v )->save( $save_path );
                        $out[$k]='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/Uploads/'.$file['savepath']. $v.'_' .$file['savename'];
                        //  $arr[]= array(
                        // 'status' => 1,
                        // 'savepath' => $file['savepath'],
                        // 'savename' => $file['savename'],
                        // 'pic_path' => $file['savepath'] . $file['savename'],
                        // 'mini_pic' => $file['savepath']. $v.'_' .$file['savename']
                        // ); 
                    }
                    $out['path']='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/Uploads/'.$file['savepath'] .$file['savename'];
                    $data = current($_FILES);
                    $arr = explode('.', $data['name']);
                    $out['name'] = $arr[0];// dump($data);
                   // echo $out;die();
                    // dump($arr); 
                // dump($images);die();
                    // dump($out);die();
                // return  $out;die();
                //返回文件地址和名给JS作回调用
                // dump($out);
                $this->ajaxReturn($out);
                // dump($info);die();
            }
            
        }else{
                echo $this->error($upload->getError());//获取失败信息
            }
    }
    }
    //上传多个文件
    public function moreup(){
        dump($_FILES);
            $config = array(
    'maxSize' => 3145728,
    'rootPath' => './Uploads/apk/',
    'savePath' => '',
    'saveName' => array('uniqid',''),
    'exts' => array('jpg', 'gif', 'png', 'jpeg'),
    'autoSub' => true,
    'subName' => array('date','Ymd'),
    );
   
    $upload = new \Think\Upload($config);// 实例化上传类
    $info = $upload->upload();
    dump($info);

    }
}
