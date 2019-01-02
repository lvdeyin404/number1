<?php
/**
 * 游学模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-02
 * Time: 11:39
 */
namespace app\index\controller;
use think\Db;

class Studytour extends Common
{
    public function index()
    {
        //获取游学页轮播  cate_id=5  is_status=1   table--picture
        $picPath = Db::table('picpath')->where(['cate_id'=>5,'is_status'=>1])->select();

        //行程特色 cate_id=1  is_status=1   table--studytour
        $tripChar = Db::table('studytour')->where(['cate_id'=>1,'is_status'=>1])->select();

        //游学意义  cate_id=2  is_status=1   table--studytour
        $tour = Db::table('studytour')->where(['cate_id'=>2,'is_status'=>1])->select();

        $this->assign('picpath',$picPath);
        $this->assign('tripchar', $tripChar);
        $this->assign('tour', $tour);
        return $this->fetch();
    }
}