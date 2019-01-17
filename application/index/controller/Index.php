<?php
/**
 * 首页
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-29
 * Time: 16:04
 */
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends Common
{
    public function index()
    {
        //获取首页轮播图 cate_id=1  is_status=1
        $indexImg = Db::table('picture')->where(['cate_id'=>1,'is_status'=>1])->select();

        //公司简介
        $profile = Db::table('profile')->find();

        //我们的服务 table--news  cate_id=6  is_status=1
        $news = Db::table('news')->where(['cate_id'=>6,'is_Release'=>1])->limit(3)->select();

        //重要合作(关于)
        $about = Db::table('trade')->limit(1)->find();

        //友情链接  cate_id=13 is_status=1
        $links = Db::table('picture')->where(['cate_id'=>13,'is_status'=>1])->select();
        $this->assign('indexImg',$indexImg);
        $this->assign('profile',$profile);
        $this->assign('news',$news);
        $this->assign('about',$about);
        $this->assign('links',$links);
        return $this->fetch();
    }
}