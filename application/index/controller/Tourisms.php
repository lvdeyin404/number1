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
use think\Request;

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

    //显示旅游详情
    public function showTravel(Request $request)
    {
        if($request->isGet()){
            $id = $request->get('id');
            //是否存在上一页下一页
            $is_pre_page = false;
            $is_next_page = false;

            //查询最后一条记录的id
            $last_id = Db::table('tourism')->where(['cate_id'=>3, 'is_status'=>1])->order('id desc')->find();
            $last_id = $last_id['id'];

            //判断是否有上一页
            $pre_id = $id - 1;
            //查询该id是否存在   该文章是否启用
            while ($pre_id > 0){
                $preData = Db::table('tourism')->where(['id'=>$pre_id, 'is_status'=>1, 'cate_id'=>3])->find();
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
                $nextData = Db::table('tourism')->where(['id'=>$next_id, 'is_status'=>1, 'cate_id'=>3])->find();
                if(empty($nextData)){
                    $next_id++;
                }else{
                    $is_next_page = true;
                    break;
                }
            }

            //如果存在上一页 查询上一页文章title
            if($is_pre_page){
                $pre_title = Db::table('tourism')->field('title')->where(['id'=>$pre_id])->find()['title'];
                $this->assign('pre_title', $pre_title);
            }
            //下一页同理
            if($is_next_page){
                $next_title = Db::table('tourism')->field('title')->where(['id'=>$next_id])->find()['title'];
                $this->assign('next_title', $next_title);
            }

            //查询数据
            $newData = Db::table('tourism')->where(['id'=>$id, 'is_status'=>1, 'cate_id'=>3])->find();
            if(empty($newData)){
                $this->error('查询错误');
            }

            //获取新闻分类
            switch ($newData['cate_id']){
                case '1':
                    $newsCate = "国内游";
                    break;
                case '2':
                    $newsCate = "周边游";
                    break;
                case '3':
                    $newsCate = "热门攻略";
                    break;
            }

            $this->assign('pre_id', $pre_id);
            $this->assign('next_id', $next_id);
            $this->assign('is_pre', $is_pre_page);
            $this->assign('is_next', $is_next_page);
            $this->assign('newData', $newData);
            $this->assign('newscate', $newsCate);
            return $this->fetch();
        }
    }
}