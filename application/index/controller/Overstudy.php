<?php
/**
 * 留学模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-02
 * Time: 10:15
 */
namespace app\index\controller;
use think\Db;

class Overstudy extends Common
{
    public function index()
    {
        //留学页轮播 cate_id=4 is_status=1  table--picture
        $picPath = Db::table('picture')->where(['cate_id'=>4,'is_status'=>1])->select();

        //留学计划 cate_id=1 is_status=1  table--overstudy
        $plan = Db::table('overstudy')->where(['cate_id'=>1,'is_status'=>1])->select();

        //语言学校大图 cate_id=9 is_status=1  table--picture
        $langPic = Db::table('picture')->where(['cate_id'=>9,'is_status'=>1])->select();

        //考试等级  cate_id=2 is_status=1  table--overstudy
        $grade = Db::table('overstudy')->where(['cate_id'=>2,'is_status'=>1])->select();

        //校园风采  cate_id=3 is_status=1 table--overstudy
        $campus = Db::table('overstudy')->where(['cate_id'=>3,'is_status'=>1])->select();

        //热点资讯 cate_id=4 is_status=1 table--news
        $hotNews = Db::table('news')->where(['cate_id'=>4,'is_Release'=>1])->select();

        //游学 cate_id=10 is_status=1  table--picture
        $tourPic = Db::table('picture')->where(['cate_id'=>10,'is_status'=>1])->select();

        //语言课程 cate_id=11 is_status=1  table--picture
        $lang = Db::table('picture')->where(['cate_id'=>11,'is_status'=>1])->select();

        //院校推荐分类 树形分类 table--cate_school
        $cateSchoole = Db::table('cate_school')->where(['pid'=>0])->select();
        foreach ($cateSchoole as $k=>$v) {
            $cateSchoole2 = Db::table('cate_school')->where(['pid'=>$v['id']])->select();
            $data[$k]['id'] = $v['id'];
            $data[$k]['name'] = $v['school_cate'];
            $data[$k]['pid'] = $v['pid'];
            $data[$k]['child'] = $cateSchoole2;
        }

        //默认获取第一个分类下的学校信息
        $schoolList = Db::table('school')->where('pid',$cateSchoole[0]['id'])->select();

        //团队顾问 cate_id=4 is_status=1  table--overstudy
        $team = Db::table('overstudy')->where(['cate_id'=>4,'is_status'=>1])->select();

        //成功案例 cate_id=5  is_status=1  table--overstudy
        $succAnli = Db::table('overstudy')->where(['cate_id'=>5,'is_status'=>1])->select();

        $this->assign('picpath',$picPath);
        $this->assign('plan', $plan);
        $this->assign('langpic',$langPic);
        $this->assign('grade', $grade);
        $this->assign('campus', $campus);
        $this->assign('hotnews', $hotNews);
        $this->assign('tourpic', $tourPic);
        $this->assign('lang', $lang);
        $this->assign('cateschool', $cateSchoole2);
        $this->assign('schoollist', $schoolList);
        $this->assign('team', $team);
        $this->assign('succanli', $succAnli);
        return $this->fetch();
    }
}