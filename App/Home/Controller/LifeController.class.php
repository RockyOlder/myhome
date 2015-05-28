<?php
namespace Mobile\Controller;
use Think\Controller;
class LifeController extends Controller {
    /**
     * 输入手机号码
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-21T16:35:15+0800
     * @return [type]                   [description]
     */
    public function register(){
        if (IS_POST) {
            // dump($_POST);die();
        	//获取用户输入的密码是否一致！
        	$pass1 = I('post.passWord');
			$pass2 = I('post.passWordDemo');
			if($pass1 != $pass2){
				$this->error('注册失败，你的密码输入不一致！');
			}
        	$data['salt'] = mt_rand(999, 9999);
        	$data['name'] = I('post.userName');
			$data['password'] = change($data['salt'],md5($pass1));
			$data['mobile'] = I('post.mobile');
			$data['email'] = I('post.email');
            $data['true_name'] = I('post.true_name');
           // dump($data);
           // die();
            cookie('register_admin',$data);
            // dump(cookie('register_admin'));
            if (cookie('register_admin') != null) {
                $this->success('用户注册成功',U('Life/info'),1);
            }else{

                $this->error('注册失败！');
            }
            
              
        }
        $this->display();
    }	
	/**
     * 输入手机号码
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-21T16:35:15+0800
     * @return [type]                   [description]
     */
    public function info(){  
   
        if (IS_POST) {
            // $shop = M('business_shop')->create();
            $business = M('business')->create(); 
            $path = I('post.path');
            $mid = I('post.mid');
            $name = I('post.pic_name');
            foreach ($path as $k => $v) {
                $tem = "";
                $tem['pic'] = $v;
                $tem['mid'] = $mid[$k];
                $tem['name'] = $name[$k];
                $path[$k] = $tem;
            }
            //   exit;
            $more_pic = json_encode($path);
            $business[more_pic] = $more_pic;
            $business['lock'] = 1;
            M()->startTrans();

            $bool=M('business')->data($business)->add();
            // dump($bool);
            // dump(M('business')->getlastSql());
            // $shop['bid'] = $bool;
            // $shop = array(
            //     'bid'     => $bool,
            //     'address' => I('post.address'),
            //     'zone' => I('post.zone'),
            //     'permit' => I('post.permit'),
            //     'tax' => I('post.tax'),
            //     'organize' => I('post.organize'),
            //     'company' => I('post.company'),
            //     'basic' => I('post.basic'),
            //     'agent' => I('post.agent'),
            //     'authz' => I('post.authz')
            //     );
            $sql ="insert into `wrt_business_shop` set `bid`= $bool ,`address`='".I('post.address')
            ."' ,`zone`='".I('post.zone')."' ,`permit`='".I('post.permit')
            ."' ,`tax`='".I('post.tax')."' ,`organize`='".I('post.organize')
            ."' ,`company`='".I('post.company')."' ,`basic`='".I('post.basic')
            ."' ,`owner`='".I('post.owner')
            ."' ,`agent`='".I('post.agent')."' ,`authz`='".I('post.authz')."'";
            
            $bool1 =M('business_shop')->execute($sql);

            // dump($bool1);die;
            $data = cookie('register_admin');
            $data['add_time'] = time();
            $sql = "insert into `wrt_admin` set `salt`=$data[salt],`name`='$data[name]',
                `password`='$data[password]',`true_name`='$data[true_name]'
                ,`mobile`='$data[mobile]',`email`='$data[email]',
                `shop_id`='$bool',`add_time`=$data[add_time],
                `role_id`=5,
                `is_lock`=1,`top_logo`='http://120.24.214.88/Uploads/cate/life.png',
                `top_name`='慧享园-生活服务商家',`statue`=2,`flag`=1";
            $uid=M()->execute($sql);
            if($uid){
                    $uid = M('admin')->field('id')->where(array('shop_id'=>$bool))->find();

                    $bool2 = $uid['id'];
            } 
            $role['group_id'] = 5;
            $role['uid'] = $bool2;
            // dump()
            $bool3 = M('auth_group_access')->add($role);
            // dump(M('auth_group_access')->getlastSql());
            // dump($bool);
            // dump($bool1);
            // dump($bool2);
            // dump($bool3);die();
            if ($bool && $bool1 && $bool2 && $bool3) {
                M()->commit();
                //$this->redirect('Life/success','店铺详细信息，成功输入');
                $this->success('店铺详细信息，成功输入',U('Life/end',array('id'=>$bool2)),1);
            }else{
                M('business')->delete($bool);
                M('business_shop')->delete($bool1);
                M('admin')->delete($bool2);
                M('auth_group_access')->where($role)->delete();
                M()->rollback();
                $this->error('信息提交失败！');
            }
            
        }
        //查询出顶部分类
        $cate = $this->topCate();
        $this->assign('cate',$cate);
        //获取省份
        $pro = $this->getprovence();
        // dump($pro);
        $this->assign('pro',$pro);
        $this->display();
    }
    public function soncate(){
        $id = I('request.id');
        if (!$id) {
            $this->error('你无权访问该页面');
        }
        $w = array('parent_id'=>$id);
        $data = M('type')->field('type_id,type_name')->where($w)->select();
        $this->ajaxReturn($data);
    }
    public function end(){
        $id = I('get.id');
        // dump(session());die();
        // $id = session('admin_aid');
        // $id = 16;
        if (!$id) {
            $this->error('你无权访问该页面');
        }
        $w = array('id'=>$id);
        $data = M('admin')->field('name,mobile,email')->where($w)->find();
        $this->assign('info',$data);
        //将数据清除
        session('admin_aid',null);
        $this->display();
    }
    
    /**
     * 发送短信
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T12:05:23+0800
     * @return [type]                   [description]
     */
    public function sendMsg(){
    	$code = cookie('mobile.time')+60;
    	if($code > time())$this->ajaxReturn('验证码已经发送，如果没有收到请检查你的手机号码刷新页面重新获取');
    	$mobile = I('request.mobile');
        //给客户手机发送验证码
        $code = mt_rand(99999,999999);
		cookie('mobile_code',$code,60);
		cookie('mobile_time',$code,60);
        $msg = C('msg_start').$code.C('msg_end');
        $bool = sendMsg($mobile,$msg);
		if($bool){
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}
    }
	/**
     * 异步验证用户的短信验证码是否正确
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T12:05:23+0800
     * @return [type]                   [description]
     */
    public function ajax_check(){
    	//dump(cookie());
    	$type = I('request.type');//获取是判断手机还是邮箱的验证码1手机2邮箱
    	
        if($type == 1){
        	$verify = I('request.mobile_code');//获取传递过来的验证码
        	$old = cookie('mobile_code');
        }else{
        	$verify = I('request.email_code');//获取传递过来的验证码
        	$old = cookie('email.code');
        }
		if($old == $verify){
            if($type == 1){
            $old = cookie('mobile_code');
            }else{
                $old = cookie('email.code');
            }
			echo "success";
		}else{
			echo '验证码输入不正确';
		}
		
    }
	/**
     * 发送邮件
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T12:05:23+0800
     * @return [type]                   [description]
     */
    public function sendEmail(){
    	$code = cookie('email_time')+60000;
    	if($code > time())$this->ajaxReturn('验证码已经发送，如果没有收到请检查你的手机号码刷新页面重新获取');
//  	$ip = get_client_ip();
    	$mail = I('request.email');
        //给客户手机发送验证码
        $code = mt_rand(99999,999999);
		cookie('email_code',$code,7200);
		cookie('email_time',$code,3600);
        $msg = C('msg_start').$code.C('msg_end');
	 	import('Org.Util.Mail');
        $back = SendMail($mail,'注册验证码',$msg,'慧享园');
		if($back){
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}
    }
    /**
     * 协议的显示内容
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T09:44:01+0800
     * @return [type]                   [description]
     */
    public function server(){
       $this->display();
    }
    /**
     * 验证码的显示框
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T09:44:01+0800
     * @return [type]                   [description]
     */
    public function check($code){
        $verify = new \Think\Verify();
        
        // $code = strtoupper($code);
        // dump($_POST['verify']);die();
        $bool =$verify->check($code);
        if ($bool) {            
            return true;
        }else{
            return false;
        }
    }
	/**
     * 异步验证用户是否存在
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T12:05:23+0800
     * @return [type]                   [description]
     */
    public function ajax_check_name(){
    	$name = I('request.name');
    	$bool = M('admin')->where(array('name'=>$name))->find();        
		if(is_null($bool)){
			echo "success";
		}else{
			echo '用户名已经存在';
		}
		
    }
    /**
     * 异步验证邮箱是否存在
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T12:05:23+0800
     * @return [type]                   [description]
     */
    public function ajax_check_email(){
        $name = I('request.email');
        $bool = M('admin')->where(array('email'=>$name))->find();  
        // dump($name);
        // dump($bool);      
        if(is_null($bool)){
            echo "success";
        }else{
            echo '该邮箱已经注册，不能重复注册';
        }
        
    }
    /**
     * 异步验证用户手机是否存在
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T12:05:23+0800
     * @return [type]                   [description]
     */
    public function ajax_check_mobile(){
        $name = I('request.mobile');
        $bool = M('admin')->where(array('mobile'=>$name))->find();        
        if(is_null($bool)){
            echo "success";
        }else{
            echo '该手机已经注册，不能重复注册';
        }
        
    }
    /**
     * 异步验证vip店铺名称是否存在
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-22T12:05:23+0800
     * @return [type]                   [description]
     */
    public function ajax_check_vip(){
        $name = I('request.name');
        $bool = M('business')->where(array('name'=>$name))->find();        
        if(is_null($bool)){
            echo "success";
        }else{
            echo '该店铺名称已经存在，请起一个特别的名字';
        }
        
    }
     //获取城市省份列表
    function getprovence() {
        //查询出所有的省份
        $sql = "SELECT REGION_ID,REGION_NAME FROM " . C('DB_PREFIX') . "region where parent_id = 1";
        // dump($sql);
        return M()->query($sql);
    }
    //查询出顶部分类
    function topCate() {
        $sql = "select type_id,type_name from " . C('DB_PREFIX') . "type where parent_id = 0";
        // dump($sql);
        $data = M()->query($sql);
        // dump($data);
        return $data;
    }
}