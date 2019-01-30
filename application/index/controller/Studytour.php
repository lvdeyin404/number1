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
use think\Request;

class Studytour extends Common
{
    public function index()
    {
        //获取游学页轮播  cate_id=5  is_status=1   table--picture
        $picPath = Db::table('picture')->where(['cate_id'=>5,'is_status'=>1])->select();

        //行程特色 cate_id=1  is_status=1   table--studytour
        $tripChar = Db::table('studytour')->where(['cate_id'=>1,'is_status'=>1])->select();

        //游学意义  cate_id=2  is_status=1   table--studytour
        $tour = Db::table('studytour')->where(['cate_id'=>2,'is_status'=>1])->select();

        $this->assign('picpath',$picPath);
        $this->assign('tripchar', $tripChar);
        $this->assign('tour', $tour);
        return $this->fetch();
    }

    //列表页
    public function showTourList(Request $request)
    {
        //是否点击分类
        $cate_id = $request->get('cate_id');
        if(isset($cate_id)){
            //获取数据
            $tourList = Db::table('studytour')->where('cate_id',$cate_id)->select();
            $this->assign('cate_id', $cate_id);
        }else{
            //默认获取冬令营数据  cate_id=4
            $tourList = Db::table('studytour')->where('cate_id',4)->select();
            $this->assign('cate_id', 4);
        }

        switch ($cate_id){
            case '3':
                $catetitle = "夏令营";
                break;
            case '4':
                $catetitle = "冬令营";
                break;
            default:
                $catetitle = "冬令营";
        }

        $this->assign('catetitle', $catetitle);
        $this->assign('tourList', $tourList);
        return $this->fetch();
    }

    //详情页
    public function showTour(Request $request)
    {
        if($request->isGet()){
            $id = intval($request->get('id'));
            $cate_id = intval($request->get('cate_id'));

            //是否存在上一页下一页
            $is_pre_page = false;
            $is_next_page = false;

            //查询最后一条记录的id
            $last_id = Db::table('studytour')->where(['cate_id'=>$cate_id,'is_status'=>1])->order('id desc')->find();
            $last_id = $last_id['id'];

            //判断是否有上一页
            $pre_id = $id - 1;
            //查询该id是否存在   该文章是否启用
            while ($pre_id > 0){
                $preData = Db::table('studytour')->where(['id'=>$pre_id, 'is_status'=>1, 'cate_id'=>$cate_id])->find();
                if(empty($preData)){
                    $pre_id--;
                }else{
                    $is_pre_page = true;
                    break;
                }
            }

            //判断是否有下一页
            $next_id = $id + 1;
            //查询该id是否存在   该文章是否启用
            while ($next_id < $last_id + 1){
                $nextData = Db::table('studytour')->where(['id'=>$next_id, 'is_status'=>1, 'cate_id'=>$cate_id])->find();
                if(empty($nextData)){
                    $next_id++;
                }else{
                    $is_next_page = true;
                    break;
                }
            }

            //如果存在上一页 查询上一页文章title
            if($is_pre_page){
                $pre_title = Db::table('studytour')->field('title')->where(['id'=>$pre_id])->find()['title'];
                $this->assign('pre_title', $pre_title);
            }
            //下一页同理
            if($is_next_page){
                $next_title = Db::table('studytour')->field('title')->where(['id'=>$next_id])->find()['title'];
                $this->assign('next_title', $next_title);
            }

            $tourData = Db::table('studytour')->where(['id'=>$id])->find();

            switch ($cate_id){
                case '3':
                    $catetitle = "夏令营";
                    break;
                case '4':
                    $catetitle = "冬令营";
                    break;
            }

            $this->assign('pre_id', $pre_id);
            $this->assign('next_id', $next_id);
            $this->assign('is_pre', $is_pre_page);
            $this->assign('is_next', $is_next_page);
            $this->assign('tourdata', $tourData);
            $this->assign('catetitle', $catetitle);
            $this->assign('cate_id', $cate_id);

            return $this->fetch();
        }
    }
}