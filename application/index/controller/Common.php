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
use think\facade\Route;
use think\Request;

class Common extends Controller
{
    public $footer;
    public $systemctl;
    public $logo;

    //获取底部footer  每个页面都有 提取出来
    public function initialize()
    {
        $host = \think\facade\Request::host();
        parent::initialize();
        //判断网站是否打开
        $isopen = Db::table('systemctl')->field('web_is_open')->find();
        if(!$isopen['web_is_open']){
            $this->redirect('http://'.$host.'/Down');
        }
        $this->logo = Db::table('picture')->where(['is_status'=>1,'cate_id'=>15])->find();
        $this->footer = Db::table('contact')->find();
        $this->systemctl = Db::table('systemctl')->find();
        $this->assign('footer',$this->footer);
        $this->assign('logo',$this->logo);
        $this->assign('systemctl',$this->systemctl);
        $this->assign('host',$host);
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

    //通过id查找所有子分类id
    public function category_subId($data, &$rst, $id = 0)
    {
        foreach ($data as $v){
            if($v['pid'] == $id){
                $rst[] = $v['id'];
            }
//            $this->category_subId($data, $rst, $v['id']);
        }
    }
}