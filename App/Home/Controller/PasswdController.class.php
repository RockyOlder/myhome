<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 找回密码控制器
 */
class PasswdController extends Controller {
    /**
     * [index 输入手机或者邮箱的页面]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:33:07+0800
     * @return [type]                   [description]
     */
    function index(){
        $title = $this->myType();
        $this->assign('title',$title);
        // dump($_SERVER);die();
        session('url',$_SERVER[HTTP_REFERER]);
        $this->display();
    }
    /**
     * [ajax_username 异步检查用户输入的信息]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:33:24+0800
     * @return [type]                   [description]
     */
    function ajax_username(){
        $get = I('post.username');
        $code = I('post.authCode');
        $verify = new \Think\Verify();
        $bool = $verify->check($code);
        if(!$bool){
            $out['statue'] = 0;
            $out['msg'] = '验证码输入错误';
            $this->ajaxReturn($out);
        }
        $bool = preg_match('/@/', $get);
        $bool1 = preg_match('/1\d{10}/', $get);
        //查询是否是手机用户名查询找回密码
        if (!$bool && !$bool1) {
            $admin = M('admin')->field('mobile,email')->where(array('name'=>$get))->find();
            // dump($admin);
            if ($admin) {
                if ($admin['mobile'] != '') {
                    $data['name'] = $get;
                    $data['mobile'] = $admin['mobile'];
                    $data['bind'] = substr($data['mobile'], 0,3).'****'.substr($data['mobile'], 7);
                    $data['type'] = '手机';
                    $data['id'] =$data[id].time();
                    $data['method'] = 2;
                    cookie('data',$data);
                }else if($admin['email'] != ''){
                    $data['name'] = $get;
                    $data['email'] = $admin['email'];
                    $data['bind'] = substr($data['email'], 0,3).'****'.substr($data['email'], 7);
                    // dump($data);die();
                   
                    $data['id'] =$data[id].time();
                    $data['type'] = '邮箱';
                    $data['method'] = 1;
                    cookie('data',$data);

                }else{
                    $out['statue'] = 0;
                    $out['msg'] = '你输入的用户名没有绑定邮箱或者手机不能找回，请联系管理员修改密码';
                    $this->ajaxReturn($out);
                }
            }else{
                $out['statue'] = 0;
                $out['msg'] = '你输入的用户名不存在！';
                $this->ajaxReturn($out);
            }
        }


        if ($bool) {
            $w = array('email'=>$get,'statue'=>TYPE);            
            $data = M('admin')->field('id,name,email,true_name')->where($w)->find();
            if (is_null($data)) {
                $out['statue'] = 0;
                $out['msg'] = '你输入的邮箱不存在！';
                $this->ajaxReturn($out);
            }
            $data['bind'] = substr($data[email], 0,3).'****'.substr($data[email], 7);
            // dump($data);die();
           
            $data['id'] =$data[id].time();
            $data['type'] = '邮箱';
            $data['method'] = 1;
            cookie('data',$data);
            
        }
        if ($bool1) {
            $w = array('mobile'=>$get,'statue'=>TYPE);
            $data = M('admin')->field('id,name,mobile')->where($w)->find();
            if (is_null($data)) {
                $out['statue'] = 0;
                $out['msg'] = '你输入的手机不存在！';
                $this->ajaxReturn($out);
            }
            $data['bind'] = substr($data[mobile], 0,3).'****'.substr($data[mobile], 7);
            $data['type'] = '手机';
            $data['id'] =$data[id].time();
            $data['method'] = 2;
            cookie('data',$data);
           
        }
        $this->ajaxReturn(1);
    }
    /**
     * [about 发送验证码页面]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:34:15+0800
     * @return [type]                   [description]
     */
    function about(){
        $data = cookie('data');

        $this->assign('data',$data);
        // dump($data);
        if ($data['method'] == 1) {
            $this->display('email');
        }else{
            $this->display('phone');
        }
        
        
    }
    /**
     * [sendEmail 发送邮件]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:34:25+0800
     * @return [type]                   [description]
     */
    function sendEmail(){

        // dump($_SERVER);die();
        // dump(I('post.email'));
        $email = I('post.email');
        $name = I('post.name');
        $true_name = I('post.true_name');
        $id = I('post.id');
        $code = cookie('email_time')+60;
        if($code > time())$this->ajaxReturn('验证码已经发送，如果没有收到请检查你的手机号码刷新页面重新获取');
//      $ip = get_client_ip();
        $mail = I('request.email');
        //给客户手机发送验证码
        $time = date('Y年m月d日 H点i分',time());
        $date = date('Y年m月d日',time());
        // var_dump($id);die();
        $bool = M('change_pass')->add(array('info'=>$id));
        // dump($bool);die();
        $url = 'http://'.$_SERVER['HTTP_HOST']. U('changeP',array('id'=>$id),'');

        // dump($url);die();
        // dump($time);die();
        $msg = <<<here
<p>尊敬的用户： </p>
        <div style="text-indent: 2em;">$true_name,您好！ </div>
        <div style="text-indent: 2em;"> 您在{$time}提交找回密码请求，请点击下面的链接修改用户{$name}的密码: </div>
        <a href="$url">$url</a>
        <div style="text-indent: 2em;">(如果您无法点击这个链接，请将此链接复制到浏览器地址栏后访问)
           <p> 为了保证您帐号的安全性，该链接有效期为24小时，并且点击一次后将失效!</p>
            <p>设置并牢记密码保护问题将更好地保障您的帐号安全。</p>
            <p>如果您误收到此电子邮件，则可能是其他用户在尝试帐号设置时的误操作，如果您并未发起该请求，则无需再进行任何操作，并可以放心地忽略此电子邮件。</p>
          </div>
         <div style="text-align: right;">慧享园智慧社区服务中心</div>
         <div style="text-align: right;">{$date}</div>
         <div style="text-align: left;">此邮件为自动发送，请勿回复！ </div> 
here;
        // dump($msg);die();
        import('Org.Util.Mail');
        $title = $this->myType();
        // dump($mail);die();
        $back = SendMail($mail,$title,$msg,'慧享园');
        if ($back) {
            $num = strstr( $email,'@');
            $mail = array('@outlook.com','@263.net','@foxmail.com','@yeah.com','@188.com');
            // dump($mail);
            $bool = in_array($num, $mail);
            // dump($bool);die();
            if (in_array($num, $mail)) {
                 $url = 'http://www.'.substr($num, 1);
            }else{
                 $url = 'http://mail.'.substr($num, 1);
            }
           // dump($url);die();
            $this->assign('url',$url);
            $this->display();
        }else{
            $this->error('邮件发送失败，请检查你的邮件地址');
        }
        
    }
    /**
     * [check_code 异步验证用户的短信验证码是否正确]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:34:38+0800
     * @return [type]                   [description]
     */
    public function check_code(){
        //dump(cookie());
        $type = I('request.type');//获取是判断手机还是邮箱的验证码1手机2邮箱
        
        if($type == 1){
            $verify = I('request.mobile_code');//获取传递过来的验证码
            $old = cookie('mobile_code');
            cookie('mobile_code',null);
        }else{
            $verify = I('request.email_code');//获取传递过来的验证码
            $old = cookie('email.code');
            cookie('mobile_code',null);
        }
        if($old == $verify){
            echo "success";
        }else{
            echo '验证码输入不正确';
        }
        
    }
   /**
    * [changeP 修改密码页面]
    * @author xujun
    * @email  [jun0421@163.com]
    * @time   2015-03-26T15:34:51+0800
    * @return [type]                   [description]
    */
    public function changeP(){
        // dump(session());die();
        // dump($_SERVER);die();
        // dump(time());
        if(IS_POST){
            $id = I('post.id');
            $id = substr($id, 0,-10);
            $password = I('post.passord');
            $new = I('post.newword');
            if ($password != $new) {
                $this->error('两次密码输入不一直');
            }
            $data[salt] = mt_rand(999,9999);
            // dump($id);
            $data[id] = $id;
            $data[password] = change($data[salt],md5($password));
            $bool = M('admin')->save($data);
            // dump(M('admin')->getlastSql());die();
            // dump($data);die();
            if ($bool) {
                $url = session('url');
                session('url',$url);
                $this->success("你的密码设置成功",U('Login/index'));
            }else{
                $this->error('修改失败');
            }
            // $password = 
        }
        $type = isset($_GET[type])?$_GET[type]:0;

        if ($type == 0) {
            //判断从邮箱过来的用户是否超过24小时
            $old = I('get.id');  
            $id = M('change_pass')->field('statue,id')->where(array('info'=>$old))->find();
            $statue = $id['statue'];
            //M('change_pass')->save(array('id'=>$id['id'],'statue'=>1));
            // dump($old)   ;   
            $oldTime = substr($old, -10);
            // dump($oldTime);

            $time = time() - 24*60*60;
            // dump($time);
            // 查看是否是第一次链接
             
            // dump(M('change_pass')->getlastSql());
            //         dump($oldTime < $time);
            //         dump($statue);

                    //dump($oldTime < $time || $statue);//die();
            if($oldTime < $time || $statue){
                $this->error('该链接已经失效',U('index'));
            }else{
                // M('change_pass')->delete(current($id));
                 M('change_pass')->save(array('id'=>$id['id'],'statue'=>1));
            }
            
            
        }        
        $this->display();
    }
    /**
     * [sendMsg 发送手机短信验证码]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:35:11+0800
     * @return [type]                   [description]
     */
    public function sendMsg(){
        $code = cookie('mobile.time')+60000;
        if($code > time())$this->ajaxReturn('验证码已经发送，如果没有收到请检查你的手机号码刷新页面重新获取');
        $mobile = I('request.mobile');
        //给客户手机发送验证码
        $code = mt_rand(99999,999999);
        cookie('mobile_code',$code,360);
        cookie('mobile.time',$code,3600);
        $msg = C('msg_start').$code.C('msg_end');
        $bool = sendMsg($mobile,$msg);
        if($bool){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }
    /**
     * [verify 验证码显示]
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-03-26T15:35:38+0800
     * @return [type]                   [description]
     */
    function verify(){
        $Verify = new \Think\Verify ();
        $Verify->fontSize = 30;
        $Verify->length = 4;
        // $Verify->imageH = 22;
        // $Verify->imageW = 110;
        $Verify->useNoise = false;
        $Verify->useImgBg = true;
        $Verify->entry ();
    }
    function myType(){
        switch (TYPE) {
            case '1':
                return "慧享园智慧社区-找回密码";

                break;
            case '2':
                return "慧享园生活导航商家-找回密码";

                break;
            case '3':
                return "慧享园特享商家-找回密码";

                break;
            case '4':
                return "慧享园小区服务管理-找回密码";

                break;
        }
    }

}