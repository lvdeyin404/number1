<?php
/**
 * 公共控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-27
 * Time: 9:43
 */
namespace app\admin\controller;
use think\Controller;

class Common extends Controller
{
    //构造方法
    public function __construct()
    {
        parent::__construct(); //先执行父类构造方法
        $this->checkUser();    //登录检查
        //已登录.分配变量
        $this->assign('username',session('userinfo.username'));
    }

    private function checkUser()
    {
        if(!session('?userinfo')){
            //未登录，请先登录
            $this->error('非法用户,请先登录','Login/index');
        }
    }

    //密码验证
    public function password($password,$salt)
    {
        return md5(md5($password).$salt);
    }
}