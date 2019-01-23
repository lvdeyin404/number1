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
        $picpaths = Db::table('picture')->where(['cate_id'=>3,'is_status'=>1])->find();

        //公司新闻 cate_id=1  is_status=1
        $newsGs = Db::table('news')->where('cate_id',1)->where(['is_Release'=>1])->paginate(5);

        //公司新闻 cate_id=1  is_status=1
        $newsHy = Db::table('news')->where('cate_id',2)->where(['is_Release'=>1])->paginate(5);

        //获取热门新闻  分页(5)
        $newsRm = Db::table('news')->where(['cate_id'=>3,'is_Release'=>1])->paginate(5);

        $this->assign('picpath',$picpaths);
        $this->assign('newsGs',$newsGs);
        $this->assign('newsHy',$newsHy);
        $this->assign('newsRm',$newsRm);

        return $this->fetch();
    }
}