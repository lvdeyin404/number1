<?php
/**
 * 网站关闭
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-17
 * Time: 13:54
 */
namespace app\index\controller;

use think\Controller;
use Think\Db;
use think\facade\Request;

class Down extends Controller
{
    public function index()
    {
        $isopen = Db::table('systemctl')->field('web_is_open')->find();
        if($isopen['web_is_open']){
            $this->redirect('http://'.Request::host().'/Index');
        }
        return $this->fetch();
    }
}