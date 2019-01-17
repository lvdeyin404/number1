<?php
/**
 * 公司简介
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-29
 * Time: 16:24
 */
namespace app\index\controller;
use think\Controller;
use think\Db;

class Profile extends Common
{
    public function index()
    {
        //获取简介页轮播  cate_id=2  is_status=1
        $proImg = Db::table('picture')->where(['cate_id'=>2,'is_status'=>1])->find();

        //获取简介及文化
        $profileList = Db::table('profile')->select();

        //获取员工风貌 is_status=1 cate_id=14
        $staff = Db::table('picture')->where(['cate_id'=>14,'is_status'=>1])->limit(3)->select();
        $this->assign('proImg',$proImg);
        $this->assign('profileList',$profileList);
        $this->assign('staff', $staff);
        return $this->fetch();
    }
}