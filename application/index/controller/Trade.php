<?php
/**
 * 经贸模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-02
 * Time: 14:23
 */
namespace app\index\controller;
use think\Db;

class Trade extends Common
{
    public function index()
    {
        //获取轮播 cate_id=7  is_status=1  table--picture
        $picPath = Db::table('picpath')->where(['cate_id'=>7,'is_status'=>1])->select();

        //获取关于 table--trade
        $trade = Db::table('trade')->select();

        //合作单位 cate_id=13 is_status=1  table--picture
        $coop = Db::table('picture')->where(['cate_id'=>13,'is_status'=>1])->select();

        //行业资讯 cate_id=5  is_Release=1  table--news
        $news = Db::table('news')->where(['cate_id'=>5,'is_Release'=>1])->select();

        $this->assign('picpath', $picPath);
        $this->assign('trade',$trade);
        $this->assign('coop',$coop);
        $this->assign('news',$news);
        return $this->fetch();
    }
}