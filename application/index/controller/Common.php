<?php
/**
 * 前台公共控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-02
 * Time: 10:45
 */
namespace app\index\controller;
use think\Controller;
use think\Db;

class Common extends Controller
{
    public $footer;

    //获取底部footer  每个页面都有 提取出来
    public function initialize()
    {
        parent::initialize();
        $this->footer = Db::table('contact')->select();
    }

    //获得一维数组分类列表
    public function category_list($data, &$rst, $pid=0, $level=0)
    {
        foreach ($data as $v){
            if($v['pid'] == $pid){
                $v['level'] = $level;   //保存分类级别
                $rst[] = $v;        //保存服务条件的元素
                $this->category_list($data, $rst, $v['id'], $level+1);
            }
        }
    }
}