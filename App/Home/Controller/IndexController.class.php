<?php

namespace Home\Controller;

use Home\Controller\IsloginController;

class IndexController extends IsloginController {

    public function index() {
        //dump(session());
        // $data = sendMsg('18691988421','come on baby!');//调用发送短信方法
        // dump($data);
        // 验证用户的登录权限
        // $this->checkself();
        // dump(session());
        //	echo 1;exit;

        $this->display();

        // 查询用户所有的权限
    }

    // 首页开始页面
    public function start() {
        switch (TYPE) {
            case '1':
                $this->startDate();
                $tpl = 'start';
                break;
            case '2':
                $this->lifeData();
                $tpl = 'life';
                break;
            case '3':
                $this->vipData();
                $tpl = 'vip';
                break;
            case '4':
                $this->developer();
                $tpl = 'server';
                break;
        }
        // dump($tpl);die();
        $this->display($tpl);
    }

    // 首页顶部
    public function top() {
        $ip = get_client_ip();
        $area = get_address_from_local_ip($ip);
        $area = mb_substr($area, 0,-1,'utf8');
        $where = "city like '$area%'";
        $code = M('weather')->field('code')->where($where)->find();
        if ($code) {
            $code = $code['code'];
        }
        // http://www.weather.com.cn/data/sk/101010100.html
        // http://www.weather.com.cn/data/cityinfo/101010100.html
        // http://m.weather.com.cn/data/101010100.html 
  //      $url = 'http://www.weather.com.cn/data/cityinfo/'.$code.'.html';
   //     $weather = file_get_contents($url);
    //    $weather = json_decode($weather,true);
//        $this->assign('weather',$weather['weatherinfo']);
        // dump($weather);die();
        // dump($url);
        $this->assign('time', time());
        $this->display();
    }

    // 首页左边
    public function left() {
        $data = $this->getleft();
        // dump($data);
        $this->assign('menu', $data);
        $this->display();
    }

    // 首页下面中间分割框
    public function drag() {
        $this->display();
    }

    // 用户退出方法
    public function loginout() {
        //admin_log('退出登录');
        session('admin', null);
        session('user_auth', null);
        cookie('admin', null);
        cookie('user_auth', null);
        redirect(U('Login/index'), 0, '');
    }

    /**
     * 慧锐通总后台统计数据
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-02-05T18:15:36+0800
     * @return [type]                   [description]
     */
    public function startDate() {
        //计算会员总数
   
        $count[user] = M('user')->count();
        //计算住户总数
        $count['owner'] = M('pro_owner')->count();
        //计算小区数量
        $count[village] = M('village')->count();
        //服务商家数量
        $count[life] = M('business')->count();
        //计算VIP商家数量
        $count[vip] = M('vip')->count();
  
        // dump(time());
        $data = date('Y-m-d', time());
        // dump($data);
        $start = strtotime($data);
        $end = $start + 60 * 60 * 24;
        // dump($time);
        $w = 'time > ' . $start . ' and time <' . $end;
        //计算今天的订单数量
        $count[order] = M('order')->where($w)->count();
        // dump(M('order')->getlastSql());
        // dump($user);die();

        $w = array('statue' => 2, 'flag' => 1);
        $admin = M('admin');
        $count[lifeApp] = $admin->where($w)->count();
        // dump($admin->getlastSql());
        $w[statue] = 3;
        $count[vipApp] = $admin->where($w)->count();
        $w[statue] = 4;
        $count[developer] = $admin->where($w)->count();
        $count[date] = date('m-d', time());
        $this->assign('count', $count);
    }

    /**
     * 生活导航的数据
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-02-05T19:13:29+0800
     * @return [type]                   [description]
     */
    
    public function lifeData() {
        $w = array('shopid' => session('admin.shop_id'));
        
        $count['collect'] = M('life_co_shop')->where($w)->count();
        // dump(M('life_co_shop')->getlastSql());
        // dump($count);
       $w = array('o.statue' => 0, 'o.shop_id' => session('admin.shop_id'));
       $count['new'] = M('order o')->join('wrt_user AS u ON u.user_id=o.user_id')->where($w)->count();
        $w = array('o.statue' => 1, 'o.shop_id' => session('admin.shop_id'),'o.check_statue'=>0);
        $count[pay] = M('order o')->join('wrt_user AS u ON u.user_id=o.user_id')
        ->where($w)->count();
        // dump($count);
        //echo  M('order o')->getLastSql(); exit;
        $this->assign('count', $count);
    }

    /**
     * vip商家的数据
     * @author xujun
     * @email  [jun0421@163.com]
     * @time   2015-02-05T19:13:29+0800
     * @return [type]                   [description]
     */
    public function vipData() {
        $w = array('user_rank' => 2);
        $count['sum'] = M('user')->where($w)->count();
        $w = array('statue' => 0, 'shop_id' => session('admin.shop_id'));
        $count['new'] = M('order')->where($w)->count();
        $w = array('statue' => 1, 'shop_id' => session('admin.shop_id'));
        $count[pay] = M('order')->where($w)->count();
        $this->assign('count', $count);
    }

    /**
     * 开发商数据
     * @author phper丶Li
     * @time   2015年2月26日15:11:21
     * @return [type]                   [description]
     */
    public function developer() {
        if (session("admin.developer") != 0) {
            $where[] = array('p.property_id'=> session("admin.developer"));
        } else {
            $count['property'] = session("admin.property");
        }
        if (session("admin.village") != 0)
            $where[] = array('w.property_id'=> session("admin.village") );
        if (session("admin.property") != 0)
            $where[] = array('v.property_id'=> session("admin.property"));
    
        $w = array('property_id' => session('admin.developer'));
        
        $role = array('flag' => 1,'village_id'=>session('admin.village'));
        
        $data['property']=session("admin.property");
        $data['village']=session("admin.village");
        $data['developer']=session("admin.developer");
       
        $count['v_count'] = M("user")->where($role)->count();
        
        $count['sum'] = M('property')->where($w)->count();
        //print_r($count['v_count']);exit;
        if (session('admin.property') == 0) {

            $id = M('property')->field('id')->where($w)->select();
            foreach ($id as $k => $v) {
                $str[] = $v['id'];
            }
            $w = array('property_id' => $str);
        } else {
            $w = array('property_id' => session('admin.property'));
        }
        $count['new'] = M("village v")
                ->join('wrt_property AS p ON v.property_id=p.id')
                ->where($where)
                ->count();

        $count['collect'] = M("proOwner w")
                ->join('wrt_village AS v ON w.property_id=v.id')
                ->join('wrt_property AS p ON p.id=v.property_id')
                ->where($where)
                ->count();
        $count['pro'] = session("admin.developer");

        //   print_r($count['collect']);exit;
        //    print_r($count['new']);exit;
        //$w= array('statue'=>0,'shop_id'=>session('admin.shop_id'));
        //	$count['new'] = M('order')->where($w)->count();
        //	$w= array('statue'=>1,'shop_id'=>session('admin.shop_id'));
        //	$count[pay] = M('order')->where($w)->count();
         $this->assign('data', $data);
        $this->assign('count', $count);
    }

}