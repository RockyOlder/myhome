<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 城市管理页面
 */
class UploadsController extends Controller {

    function index() {
        // dump($_SERVER['HTTP_HOST']);
// 		$data=$this->fullcity();
        //dump($data);
        $this->display();
    }

    public function upload() {
        if (!empty($_FILES)) {
            // dump($_FILES);
            //图片上传设置
            $config = array(
                'maxSize' => 3145728,
                'rootPath' => './Uploads/',
                'savePath' => '',
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd'),
            );
            $upload = new \Think\Upload($config); // 实例化上传类
            $more = $upload->upload();
            //判断是否有图
            if ($more) {
                // $h =700;
                // $image = new \Think\Image();
                // $thumb_file = './Uploads/' . $file['savepath'] . $file['savename'];//图片的保存路径
                // $save_path = './Uploads/' .$file['savepath']. $h."_" . $file['savename'];
                // $image->open( $thumb_file )->thumb( $h, $h )->save( $save_path ); //将图片压缩并保存
                // dump($images);die();
                $info = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $more['Filedata']['savepath'] . $more['Filedata']['savename'];
                //返回文件地址和名给JS作回调用
                // $this->ajaxReturn($info);
                // $info='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/Uploads/'.$file['savepath']. $v.'_' .$file['savename'];
                echo $info;
                die();
            } else {
                $this->error($upload->getError()); //获取失败信息
            }
        }
    }

    public function listUpload() {
        if (!empty($_FILES)) {
//session_start(); 
            // dump($_FILES);
            //图片上传设置
            $config = array(
                'maxSize' => 3145728,
                'rootPath' => './Uploads/',
                'savePath' => 'list/',
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd'),
            );
            $upload = new \Think\Upload($config); // 实例化上传类
            $info = $upload->upload();
            //判断是否有图
            if ($info) {
                // 实例化图片处理类
                $image = new \Think\Image();
                foreach ($info as $file) {
                    $thumbWidth = array('mid' => 700, 'min' => 200); //多张图片压缩的大小限制
                    $thumb_file = './Uploads/' . $file['savepath'] . $file['savename']; //图片的保存路径
                    foreach ($thumbWidth as $k => $v) {
                        $save_path = './Uploads/' . $file['savepath'] . $v . "_" . $file['savename'];
                        $image->open($thumb_file)->thumb($v, $v)->save($save_path); //将图片压缩并保存
                        $out[$k] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $file['savepath'] . $v . '_' . $file['savename']; //保存图片压缩House的路径
                    }
                    $out['path'] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $file['savepath'] . $file['savename']; //组合出原图片的路径
                    // json返回数据
                    $this->ajaxReturn($out);
                    info;
                    die();
                }
            } else {
                $this->error($upload->getError()); //获取失败信息
            }
        }
    }

    public function photo() {
        if (!empty($_FILES)) {

            // dump($_FILES);
            //图片上传设置
            $config = array(
                'maxSize' => 3145728,
                'rootPath' => './Uploads/',
                'savePath' => 'photo/',
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd'),
            );

            $upload = new \Think\Upload($config); // 实例化上传类                  
            $info = $upload->upload();
            if ($info) {
                $out['path'] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $info['file_upload']['savepath'] . $info['file_upload']['savename'];
                $image = new \Think\Image();

                foreach ($info as $file) {
                    $thumbWidth = array('mid' => 700);
                    $thumb_file = './Uploads/' . $file['savepath'] . $file['savename'];
                    foreach ($thumbWidth as $k => $v) {
                        $save_path = './Uploads/' . $file['savepath'] . $v . "_" . $file['savename'];
                        $image->open($thumb_file)->thumb($v, $v)->save($save_path);
                        $out[$k] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $file['savepath'] . $v . '_' . $file['savename'];
                    }
                    $out['path'] = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $file['savepath'] . $file['savename'];
                    $data = current($_FILES);
                    $arr = explode('.', $data['name']);
                    $out['name'] = $arr[0];
                    $out['suolie'] = ROOT . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $file['savepath'] . $v . '_' . $file['savename'];
                }

                $out['list'] = ROOT . dirname($_SERVER['PHP_SELF']) . '/Uploads/' . $info['Filedata']['savepath'] . $info['Filedata']['savename'];
                $this->ajaxReturn($out);
            } else {
                echo $this->error($upload->getError());
            }
        }
    }

    function cate() {
        if ($_FILES) {
            $config = array(
                'maxSize' => 3145728,
                'rootPath' => './default/Uploads/',
                'savePath' => '',
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd')
            );
            $ftpConfig = array(
                'host' => '120.24.214.88', //服务器
                'port' => 21, //端口
                'timeout' => 90, //超时时间
                'username' => 'www', //用户名
                'password' => '4398eea99' //密码 
            );
            $upload = new \Think\Upload($config, 'Ftp', $ftpConfig); // 实例化上传类

            $upload->maxSize = 3145728; // 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->rootPath = './default/Uploads/cate/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            // dump($info);
            // die();
            if (!$info) {// 上传错误提示错误信息
                echo $upload->getError();
            } else {// 上传成功       
                $path = 'http://120.24.214.88/Uploads/cate/' . $info['Filedata']['savepath'] . $info['Filedata']['savename'];
                $this->ajaxReturn($path);
            }
        }
    }

    function imgDelete() {
        $path = I("post.path");
        $suolie = I("post.suolie");

        if (unlink($path) && unlink($suolie)) {
            echo 1;
        } else {
            echo 2;
        }
    }

    function apk() {
        if ($_FILES) {
            $config = array(
                'maxSize' => 31045728,
                'rootPath' => './default/Uploads/',
                'savePath' => '',
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd')
            );
            $ftpConfig = array(
                'host' => '120.24.214.88', //服务器
                'port' => 21, //端口
                'timeout' => 90, //超时时间
                'username' => 'www', //用户名
                'password' => '4398eea99' //密码 
            );
            $upload = new \Think\Upload($config, 'Ftp', $ftpConfig); // 实例化上传类

            $upload->maxSize = 31457280; // 设置附件上传大小
            $upload->exts = array('apk'); // 设置附件上传类型
            $upload->rootPath = './default/Uploads/apk/'; // 设置附件上传根目录
            $upload->savePath = ''; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            // dump($info);
            // die();
            if (!$info) {// 上传错误提示错误信息
                echo $upload->getError();
            } else {// 上传成功       
                $path = 'http://120.24.214.88/Uploads/apk/' . $info['Filedata']['savepath'] . $info['Filedata']['savename'];
                $this->ajaxReturn($path);
            }
        }
    }

    // 上传文件
    function file() {
        if ($_FILES) {
            $config = array(
                'maxSize' => 314572800,
                'rootPath' => './Data/',
                'savePath' => '',
                'saveName' => array('uniqid', ''),
                'exts' => array('xlsx', 'xls', 'xlsm', 'ods', 'slk', 'xml', 'gnumeric'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd')
            );
            $upload = new \Think\Upload($config); // 实例化上传类
            // 上传文件
            $info = $upload->upload();
            // dump($info);
            // $this->ajaxReturn($info);
            // die();
            if (!$info) {// 上传错误提示错误信息
                echo $upload->getError();
            } else {// 上传成功       
                //$path='http://'.$_SERVER['HTTP_HOST'].$config['rootPath'].$info['Filedata']['savepath'].$info['Filedata']['savename'];  
                $path = $config['rootPath'] . $info['Filedata']['savepath'] . $info['Filedata']['savename'];
                //echo($path);
                $this->ajaxReturn($path);
            }
        }
    }

    /**
     * 上传单张图片的方法
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-11T17:16:14+0800
     * @return [type]                   [description]
     */
    public function picture() {
        if ($_FILES) {
            $config = array(
                'maxSize' => 3145728,
                'rootPath' => './default/Uploads/admin/',
                'savePath' => '',
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd')
            );
            $ftpConfig = array(
                'host' => '120.24.214.88', //服务器
                'port' => 21, //端口
                'timeout' => 90, //超时时间
                'username' => 'www', //用户名
                'password' => '4398eea99' //密码 
            );
            $upload = new \Think\Upload($config, 'Ftp', $ftpConfig); // 实例化上传类
            // $upload->maxSize = 3145728 ;// 设置附件上传大小
            // $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            // $upload->rootPath = './default/Uploads/admin/'; // 设置附件上传根目录
            // $upload->savePath = ''; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            // dump($info);
            // die();
            if (!$info) {// 上传错误提示错误信息
                echo $upload->getError();
            } else {// 上传成功       
                $path = 'http://120.24.214.88/Uploads/admin/' . $info['Filedata']['savepath'] . $info['Filedata']['savename'];
                $this->ajaxReturn($path);
            }
        }
    }

}

