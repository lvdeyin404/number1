<?php
/**
 * 旅游模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-02
 * Time: 13:27
 */
namespace app\index\controller;
use think\Db;

class Tourism extends Common
{
    public function index()
    {
        //国内出游  cate_id=1  is_status=1   table--tourism
        $domTour = Db::table('tourism')->where(['cate_id'=>1,'is_status'=>1])->select();

        //周边游 cate_id=2 is_status=1 table--tourism
        $perTour = Db::table('tourism')->where(['cate_id'=>2,'is_status'=>1])->select();

        //主题出游 cate_id=12  is_status  table--picture
        $themeTour = Db::table('picture')->where(['cate_id'=>12,'is_status'=>1])->select();

        //攻略 cate_id=3 is_status=1 table--tourism
        $strategy = Db::table('tourism')->where(['cate_id'=>3,'is_status'=>1])->select();

        $this->assign('domtour',$domTour);
        $this->assign('pertour',$perTour);
        $this->assign('themetour',$themeTour);
        $this->assign('strategy',$strategy);
        return $this->fetch();
    }
}