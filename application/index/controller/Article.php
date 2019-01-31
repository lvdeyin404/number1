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
use think\Request;

class Article extends Common
{
    public function index()
    {
        //获取轮播  cate_id=3 is_status=1
        $picpaths = Db::table('picture')->where(['cate_id'=>3,'is_status'=>1])->find();

        //公司新闻 cate_id=1  is_status=1
        $newsGs = Db::table('news')->where('cate_id',1)->where(['is_Release'=>1])->order('new_id desc')->paginate(5);

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

    //显示新闻详情
    public function showNews(Request $request)
    {
        if($request->isGet()){
            $id = intval($request->get('id'));
            $cate_id = intval($request->get('cate_id'));
            //是否存在上一页下一页
            $is_pre_page = false;
            $is_next_page = false;

            //查询最后一条记录的id
            $last_id = Db::table('news')->where(['cate_id'=>$cate_id,'is_Release'=>1])->order('new_id desc')->find();
            $last_id = $last_id['new_id'];

            //判断是否有上一页
            $pre_id = $id - 1;
            //查询该id是否存在   该文章是否启用
            while ($pre_id > 0){
                $preData = Db::table('news')->where(['new_id'=>$pre_id, 'is_Release'=>1, 'cate_id'=>$cate_id])->find();
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
                $nextData = Db::table('news')->where(['new_id'=>$next_id, 'is_Release'=>1, 'cate_id'=>$cate_id])->find();
                if(empty($nextData)){
                    $next_id++;
                }else{
                    $is_next_page = true;
                    break;
                }
            }

            //如果存在上一页 查询上一页文章title
            if($is_pre_page){
                $pre_title = Db::table('news')->field('new_title')->where(['new_id'=>$pre_id])->find()['new_title'];
                $this->assign('pre_title', $pre_title);
            }
            //下一页同理
            if($is_next_page){
                $next_title = Db::table('news')->field('new_title')->where(['new_id'=>$next_id])->find()['new_title'];
                $this->assign('next_title', $next_title);
            }

            //查询数据
            $newData = Db::table('news')->where(['new_id'=>$id, 'is_Release'=>1])->find();
            if(empty($newData)){
                $this->error('查询错误');
            }

            //查询成功 增加浏览次数
            Db::table('news')->where(['new_id'=>$id])->setInc('browse_count');

            //获取新闻分类
            switch ($newData['cate_id']){
                case '1':
                    $newsCate = "公司新闻";
                    $titleCate = "公司大事记";
                    $titleUrl = "Article";
                    break;
                case '2':
                    $newsCate = "行业新闻";
                    $titleCate = "公司大事记";
                    $titleUrl = "Article";
                    break;
                case '3':
                    $newsCate = "热门新闻";
                    $titleCate = "公司大事记";
                    $titleUrl = "Article";
                    break;
                case '4':
                    $newsCate = "热点资讯";
                    $titleCate = "留学";
                    $titleUrl = "Overstudy";
                    break;
                case '5':
                    $newsCate = "行业资讯";
                    $titleCate = "经贸";
                    $titleUrl = "Trade";
                    break;
            }

            $this->assign('pre_id', $pre_id);
            $this->assign('next_id', $next_id);
            $this->assign('is_pre', $is_pre_page);
            $this->assign('is_next', $is_next_page);
            $this->assign('newData', $newData);
            $this->assign('newscate', $newsCate);
            $this->assign('titleCate', $titleCate);
            $this->assign('titleUrl', $titleUrl);
            $this->assign('cate_id', $newData['cate_id']);
            return $this->fetch();
        }
    }

    //新闻列表页
    public function showLists(Request $request)
    {
        //获取分类id
        if($request->isGet()){
            $cate_id = intval($request->get('cate_id'));
            //查询数据
            $cateList = Db::table('news')->where(['cate_id'=>$cate_id, 'is_Release'=>1])->paginate(6,false,['query' => request()->param()]);

            //获取新闻分类
            switch ($cate_id){
                case '1':
                    $newsCate = "公司新闻";
                    $titleCate = "公司大事记";
                    $titleUrl = "Article";
                    break;
                case '2':
                    $newsCate = "行业新闻";
                    $titleCate = "公司大事记";
                    $titleUrl = "Article";
                    break;
                case '3':
                    $newsCate = "热门新闻";
                    $titleCate = "公司大事记";
                    $titleUrl = "Article";
                    break;
                case '4':
                    $newsCate = "热点资讯";
                    $titleCate = "留学";
                    $titleUrl = "Overstudy";
                    break;
                case '5':
                    $newsCate = "行业资讯";
                    $titleCate = "经贸";
                    $titleUrl = "Trade";
                    break;
            }

            $this->assign('newscate', $newsCate);
            $this->assign('titlecate', $titleCate);
            $this->assign('titleurl', $titleUrl);
            $this->assign('catelist', $cateList);
            return $this->fetch();
        }
    }
}