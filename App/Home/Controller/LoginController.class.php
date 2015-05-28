<?php

namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller {
    
        /*
     *登陆
     * @return [type]      
     * @time 2015-2-27 10:54:47 
     * @author phper丶li     
    */
	public function index() {

		$this->display ();
	}
	public function Login() {

		if (IS_POST) {

			if(isset($_POST['remeber']) && $_POST['remeber'] == 1){
				cookie('login_passwd',$_POST[password],60*60*24*7);
				cookie('login_name',$_POST[username],60*60*24*7);
			}else{
                            setcookie('PHPSESSID');setcookie('login_passwd');setcookie('login_name');
                        }
			
//			if (session('num') == 1) {
//				$code = I('post.verify');
//				$verify = new \Think\Verify();
//				$bool = $verify->check($code);
//
//				if(!$bool){
//					$this->error('验证码输入错误');
//				}
//			}
			session('num',1);
			// 实例化类
			$model = D ( 'admin' );
			
			$name = I ( 'post.username', '' );
            $w = array('name'=>$name,'statue'=>TYPE);
            // dump($w);die();
			// 查询出来数据库中的数据
			$data = $model->where ($w)->find ();
			//dump()
			//dump($data);die();

			if (empty ( $data )) {
				$this->error ( '不存在该管理员！' );
			}
			if ($data[flag] == 1) {
				$this->error ( '该用户的申请还没有审核，请等待审核' );
			}
			if ($data[flag] == 2) {
				$this->error ( '该用户的申请还没有通过，请联系慧锐通' );
			}
			// 判断用户是否被锁定
			if ($data ['is_lock'] == 1) {
				$this->error ( '用户被管理员锁定，请联系上级主管！' );
			}
			// dump($data);
			// 加密用户密码
			$password = change( $data ['salt']);
			// dump($password);
			// dump($data['password'] == $password);die();
			if ($data ['password'] == $password) {
				$auth = array (
						'uid' => $data ['id'],
						'username' => $data ['name'] 
				);
				$admin = $data;
				// 获取ip
				$ip = get_client_ip();		
				$arr = array('last_ip'=>$ip,'last_login'=>time(),'id'=>$data['id']);
				M('admin')->save($arr);
				// 保存用户的session				
				session ( 'admin', $admin );
				session ( 'user_auth', $auth ); // 用来做权限验证
				cookie ( 'admin', $admin );
				cookie ( 'user_auth', $auth ); 
				                                // dump(session());
				//admin_log('登录');//记录管理员日志
				$this->redirect('Index/index','', 0, '');
				//$this->success ( '登录成功', U ( 'Index/index', '', '' ) );
			} else {
				$this->error ( '用户名或者密码输入错误' );
			}
		}
		$str = $this->logo();
		$this->assign('logo',$str);
		$tpl = 'index';
		if(TYPE == 2)$tpl = 'life';
		if(TYPE == 3)$tpl = 'vip';
		if(TYPE == 4)$tpl = 'property';
		$this->display ();
	}
	// 异步验证验证码是否正确
	public function check() {
		if (! IS_AJAX) {
			E ( '页面不存在' );
		}
		$code = I ( 'post.code' );
		// dump($code);
		// dump($this->check_verify ( $code ));
		$this->ajaxReturn ( $this->check_verify ( $code ) );
	}
	// 验证码方法
	public function verify() {
		$Verify = new \Think\Verify ();
		$Verify->fontSize = 30;
		$Verify->length = 4;
		// $Verify->imageH = 22;
		// $Verify->imageW = 110;
		$Verify->useNoise = false;
		$Verify->useImgBg = true;
		$Verify->entry ();
	}
	// 验证验证码是否输入正确
	function check_verify($code, $id = '') {
		$verify = new \Think\Verify ();
		return $verify->check ( $code, $id );
	}
	/**
	 * 登录顶部不同的欢迎语句
	 * @author xujun
	 * @email  [jun0421@163.com]
	 * @time   2015-01-27T13:43:54+0800
	 * @return [type]                   [description]
	 */
	function logo(){
		switch (TYPE) {
			case '1':
				return '慧享园智慧社区运营管理系统';
				break;
			case '2':
				return '慧享园-生活导航商家管理系统';
				break;
			case '3':
				return ' 慧享园-VIP商家管理系统';
				break;
			case '4':
				return ' 慧享园-小区服务管理系统';
				break;
			
		}
	}
}