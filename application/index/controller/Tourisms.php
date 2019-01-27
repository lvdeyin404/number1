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

class Tourisms extends Common
{
    public function index()
    {
        //获取旅游页轮播  cate_id=6  is_status=1   table--picture
        $picPath = Db::table('picture')->where(['cate_id'=>6,'is_status'=>1])->limit(3)->select();

        //美美西里专线  cate_id=1  is_status=1   table--tourism
        $domTour = Db::table('tourism')->where(['cate_id'=>1,'is_status'=>1])->limit(6)->select();

        //艾尤嘎纳温泉区 cate_id=2 is_status=1 table--tourism
        $perTour = Db::table('tourism')->where(['cate_id'=>2,'is_status'=>1])->limit(6)->select();

        //主题出游 cate_id=12  is_status  table--picture
        $themeTour = Db::table('picture')->where(['cate_id'=>12,'is_status'=>1])->select();

        //攻略 cate_id=3 is_status=1 table--tourism
        $strategy = Db::table('tourism')->where(['cate_id'=>3,'is_status'=>1])->select();

        $this->assign('picpath',$picPath);
        $this->assign('domtour',$domTour);
        $this->assign('pertour',$perTour);
        $this->assign('themetour',$themeTour);
        $this->assign('strategy',$strategy);
        return $this->fetch();
    }
}