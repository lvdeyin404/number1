<?php
/**
 * 公司大事记
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-02
 * Time: 9:40
 */
namespace app\index\controller;
use think\Db;

class Article extends Common
{
    public function index()
    {
        //获取轮播  cate_id=3 is_status=1
        $picpaths = Db::table('picture')->where(['cate_id'=>3,'is_status'=>1])->select();

        //获取全部(公司新闻和行业新闻) cate_id=1,2  is_status=1
        $news = Db::table('news')->where('cate_id','in',[1,2])->where(['is_status'=>1])->select();

        //获取热门新闻  分页(5)
        $hotList = Db::table('news')->where(['cate_id'=>3,'is_status'=>1])->paginate(5);

        $this->assign('picpath',$picpaths);
        $this->assign('news',$news);
        $this->assign('hotList',$hotList);

        return $this->fetch();
    }
}