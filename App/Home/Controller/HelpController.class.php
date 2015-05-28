<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 关于和帮助页面
 */
class HelpController extends Controller {
    /**
     * 帮助页面
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-27T14:41:40+0800
     * @return [type]                   [description]
     */
    function index(){       
        $this->display();
    }
	/**
     * 登录页面帮助页面
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-27T14:41:40+0800
     * @return [type]                   [description]
     */
    function login(){       
        $this->display();
    }
    /**
     * 关于页面
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-01-27T14:41:40+0800
     * @return [type]                   [description]
     */
    function about(){
        $this->display();
    }

}