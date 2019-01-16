<?php
/**
 * 报名方式
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-16
 * Time: 16:15
 */
namespace app\index\controller;
use Think\Db;

class Entry extends Common
{
    public function index()
    {
        //获取大图 table--picture  cate_id = 8
        $pic = Db::table('picture')->where(['cate_id'=>8, 'is_status'=>1])->find();

        $this->assign('pic', $pic);
        $this->assign('footer',$this->footer);
        $this->assign('systemctl',$this->systemctl);
        $this->assign('logo',$this->logo);
        dump($pic);
//        return $this->fetch();
    }
}