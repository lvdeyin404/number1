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
use think\Request;

class Overstudy extends Common
{
    public function index()
    {
        //留学页轮播 cate_id=4 is_status=1  table--picture
        $picPath = Db::table('picture')->where(['cate_id'=>4,'is_status'=>1])->find();

        //留学计划 cate_id=1 is_status=1  table--overstudy
        $plan = Db::table('overstudy')->where(['cate_id'=>1,'is_status'=>1])->limit(3)->select();

        //语言学校大图 cate_id=9 is_status=1  table--picture
        $langPic = Db::table('picture')->where(['cate_id'=>9,'is_status'=>1])->find();

        //考试等级  cate_id=2 is_status=1  table--overstudy
        $grade = Db::table('overstudy')->where(['cate_id'=>2,'is_status'=>1])->limit(2)->select();

        //校园风采  cate_id=3 is_status=1 table--overstudy
        $campus = Db::table('overstudy')->where(['cate_id'=>3,'is_status'=>1])->limit(3)->select();

        //热点资讯 cate_id=4 is_status=1 table--news
        $hotNews = Db::table('news')->where(['cate_id'=>4,'is_Release'=>1])->limit(8)->select();

        //游学 cate_id=10 is_status=1  table--picture
        $tourPic = Db::table('picture')->where(['cate_id'=>10,'is_status'=>1])->select();

        //语言课程 cate_id=11 is_status=1  table--picture
        $lang = Db::table('picture')->where(['cate_id'=>11,'is_status'=>1])->limit(6)->select();

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
        $schoolList = Db::table('school')->where('pid',$cateSchoole[0]['id'])->limit(8)->select();

        //团队顾问 cate_id=4 is_status=1  table--overstudy
        $team = Db::table('overstudy')->where(['cate_id'=>4,'is_status'=>1])->limit(5)->select();

        //成功案例 cate_id=5  is_status=1  table--overstudy
        $succAnli = Db::table('overstudy')->where(['cate_id'=>5,'is_status'=>1])->limit(6)->select();

        $this->assign('picpath',$picPath);
        $this->assign('plan', $plan);
        $this->assign('langpic',$langPic);
        $this->assign('grade', $grade);
        $this->assign('campus', $campus);
        $this->assign('hotnews', $hotNews);
        $this->assign('tourpic', $tourPic);
        $this->assign('lang', $lang);
        $this->assign('data', $data);
        $this->assign('schoollist', $schoolList);
        $this->assign('team', $team);
        $this->assign('succanli', $succAnli);
        return $this->fetch();
    }

    //查看计划详情页
    public function showPlan(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->get('id');
            //是否存在上一页下一页
            $is_pre_page = false;
            $is_next_page = false;

            //查询最后一条记录的id
            $last_id = Db::table('overstudy')->where(['cate_id' => 1, 'is_status'=>1])->order('id desc')->find();
            $last_id = $last_id['id'];

            //判断是否有上一页
            $pre_id = $id - 1;
            //查询该id是否存在   该文章是否启用
            while ($pre_id > 0) {
                $preData = Db::table('overstudy')->where(['id' => $pre_id, 'is_status' => 1, 'cate_id'=>1])->find();
                if (empty($preData)) {
                    $pre_id--;
                } else {
                    $is_pre_page = true;
                    break;
                }
            }

            //判断是否有下一页
            $next_id = $id + 1;
            //查询该id是否存在   该文章是否启用
            while ($next_id < $last_id + 1) {
                $nextData = Db::table('overstudy')->where(['id' => $next_id, 'is_status' => 1, 'cate_id'=>1])->find();
                if (empty($nextData)) {
                    $next_id++;
                } else {
                    $is_next_page = true;
                    break;
                }
            }

            //如果存在上一页 查询上一页文章title
            if ($is_pre_page) {
                $pre_title = Db::table('overstudy')->field('title')->where(['id' => $pre_id])->find()['title'];
                $this->assign('pre_title', $pre_title);
            }
            //下一页同理
            if ($is_next_page) {
                $next_title = Db::table('overstudy')->field('title')->where(['id' => $next_id])->find()['title'];
                $this->assign('next_title', $next_title);
            }

            //查询数据
            $newData = Db::table('overstudy')->where(['id' => $id, 'is_status' => 1])->find();
            if (empty($newData)) {
                $this->error('查询错误');
            }

            //获取新闻分类
            switch ($newData['cate_id']) {
                case '1':
                    $newsCate = "留学计划";
                    break;
                case '2':
                    $newsCate = "考试等级";
                    break;
                case '3':
                    $newsCate = "校园风采";
                    break;
                case '4':
                    $newsCate = "团队顾问";
                    break;
                case '5':
                    $newsCate = "成功案例";
                    break;
            }

            $this->assign('cate_id', 1);
            $this->assign('pre_id', $pre_id);
            $this->assign('next_id', $next_id);
            $this->assign('is_pre', $is_pre_page);
            $this->assign('is_next', $is_next_page);
            $this->assign('newData', $newData);
            $this->assign('newscate', $newsCate);
            return $this->fetch();
        }
    }

    //列表页
    public function showOverLists(Request $request)
    {
        if($request->isGet()){
            $cate_id = $request->get('cate_id');
            $planLists = Db::table('overstudy')->where(['is_status'=>1, 'cate_id'=>$cate_id])->paginate(3,false,['query' => request()->param()]);

            //获取新闻分类
            switch ($cate_id){
                case '1':
                    $newsCate = "留学计划";
                    $titleCate = "留学";
                    $titleUrl = "Overstudy";
                    $showUrl = "showPlan";
                    break;
                case '4':
                    $newsCate = "师资团队";
                    $titleCate = "留学";
                    $titleUrl = "Overstudy";
                    $showUrl = "showTeacher";
                    break;
                case '5':
                    $newsCate = "成功案例";
                    $titleCate = "留学";
                    $titleUrl = "Overstudy";
                    $showUrl = "showTeacher";
                    break;
            }

            $this->assign('newscate', $newsCate);
            $this->assign('titleCate', $titleCate);
            $this->assign('titleUrl', $titleUrl);
            $this->assign('cate_id', $cate_id);
            $this->assign('planlist', $planLists);
            $this->assign('showurl', $showUrl);
            return $this->fetch();
        }
    }

    //院校列表页
    public function showSchoolList(Request $request)
    {
        //院校推荐分类 树形分类 table--cate_school
        $cateSchoole = Db::table('cate_school')->where(['pid'=>0])->select();
        foreach ($cateSchoole as $k=>$v) {
            $cateSchoole2 = Db::table('cate_school')->where(['pid'=>$v['id']])->select();
            $data[$k]['id'] = $v['id'];
            $data[$k]['name'] = $v['school_cate'];
            $data[$k]['pid'] = $v['pid'];
            $data[$k]['child'] = $cateSchoole2;
        }

        //判断是否是点击了分类
        $cate_id = $request->get('cate_id');
        if(isset($cate_id)){
            $cate_id = $request->get('cate_id');

            //获取学校信息
            $schoolList = Db::table('school')->where('pid',$cate_id)->limit(8)->paginate(8);
        }else{
            //默认获取第一个分类下的学校信息
            $schoolList = Db::table('school')->where('pid',$cateSchoole[0]['id'])->limit(8)->paginate(8);
        }

        $this->assign('data', $data);
        $this->assign('schoollist', $schoolList);
        return $this->fetch();
    }

    //显示学院详情
    public function showSchool(Request $request)
    {
        if($request->isGet()){
            $id = $request->get('id');
            //是否存在上一页下一页
            $is_pre_page = false;
            $is_next_page = false;

            //查询最后一条记录的id
            $last_id = Db::table('school')->where(['is_status'=>1])->order('id desc')->find();
            $last_id = $last_id['id'];

            //判断是否有上一页
            $pre_id = $id - 1;
            //查询该id是否存在   该文章是否启用
            while ($pre_id > 0){
                $preData = Db::table('school')->where(['id'=>$pre_id, 'is_status'=>1])->find();
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
            while ($next_id < $last_id){
                $nextData = Db::table('school')->where(['id'=>$next_id, 'is_status'=>1])->find();
                if(empty($nextData)){
                    $next_id++;
                }else{
                    $is_next_page = true;
                    break;
                }
            }

            //如果存在上一页 查询上一页文章title
            if($is_pre_page){
                $pre_title = Db::table('school')->field('school_name')->where(['id'=>$pre_id])->find()['school_name'];
                $this->assign('pre_title', $pre_title);
            }
            //下一页同理
            if($is_next_page){
                $next_title = Db::table('school')->field('school_name')->where(['id'=>$next_id])->find()['school_name'];
                $this->assign('next_title', $next_title);
            }

            //查询数据
            $newData = Db::table('school')->where(['id'=>$id, 'is_status'=>1])->find();
            if(empty($newData)){
                $this->error('查询错误');
            }

            //获取院校分类
            $cateList = Db::table('cate_school')->select();
            foreach ($cateList as $vo){
                if($newData['pid'] == $vo['id']){
                    $newsCate = $vo['school_cate'];
                }
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

    //显示热点资讯详情
    public function showHots(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->get('id');
            //是否存在上一页下一页
            $is_pre_page = false;
            $is_next_page = false;

            //查询最后一条记录的id
            $last_id = Db::table('news')->where(['cate_id' => 4, 'is_Release' => 1])->order('new_id desc')->find();
            $last_id = $last_id['new_id'];

            //判断是否有上一页
            $pre_id = $id - 1;
            //查询该id是否存在   该文章是否启用
            while ($pre_id > 0) {
                $preData = Db::table('news')->where(['new_id' => $pre_id, 'is_Release' => 1, 'cate_id'=>4])->find();
                if (empty($preData)) {
                    $pre_id--;
                } else {
                    $is_pre_page = true;
                    break;
                }
            }

            //判断是否有下一页
            $next_id = $id + 1;
            //查询该id是否存在   该文章是否启用
            while ($next_id < $last_id + 1) {
                $nextData = Db::table('news')->where(['new_id' => $next_id, 'is_Release' => 1, 'cate_id'=>4])->find();
                if (empty($nextData)) {
                    $next_id++;
                } else {
                    $is_next_page = true;
                    break;
                }
            }

            //如果存在上一页 查询上一页文章title
            if ($is_pre_page) {
                $pre_title = Db::table('news')->field('new_title')->where(['new_id' => $pre_id])->find()['new_title'];
                $this->assign('pre_title', $pre_title);
            }
            //下一页同理
            if ($is_next_page) {
                $next_title = Db::table('news')->field('new_title')->where(['new_id' => $next_id])->find()['new_title'];
                $this->assign('next_title', $next_title);
            }

            //查询数据
            $newData = Db::table('news')->where(['new_id' => $id, 'is_Release' => 1])->find();
            if (empty($newData)) {
                $this->error('查询错误');
            }

            //查询成功 增加浏览次数
            Db::table('news')->where(['new_id'=>$id])->setInc('browse_count');

            //获取新闻分类
            switch ($newData['cate_id']) {
                case '1':
                    $newsCate = "公司新闻";
                    break;
                case '2':
                    $newsCate = "行业新闻";
                    break;
                case '3':
                    $newsCate = "热门新闻";
                    break;
                case '4':
                    $newsCate = "热点资讯";
                    break;
                case '5':
                    $newsCate = "行业资讯";
                    break;
            }

            $this->assign('pre_id', $pre_id);
            $this->assign('next_id', $next_id);
            $this->assign('is_pre', $is_pre_page);
            $this->assign('is_next', $is_next_page);
            $this->assign('newData', $newData);
            $this->assign('newscate', $newsCate);
            $this->assign('cate_id', $newData['cate_id']);
            return $this->fetch();
        }
    }

    //显示老师详情
    public function showTeacher(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->get('id');
            //是否存在上一页下一页
            $is_pre_page = false;
            $is_next_page = false;

            //查询最后一条记录的id
            $last_id = Db::table('overstudy')->where(['cate_id' => 4,'is_status'=>1])->order('id desc')->find();
            $last_id = $last_id['id'];

            //判断是否有上一页
            $pre_id = $id - 1;
            //查询该id是否存在   该文章是否启用
            while ($pre_id > 0) {
                $preData = Db::table('overstudy')->where(['id' => $pre_id, 'is_status' => 1, 'cate_id'=>4])->find();
                if (empty($preData)) {
                    $pre_id--;
                } else {
                    $is_pre_page = true;
                    break;
                }
            }

            //判断是否有下一页
            $next_id = $id + 1;
            //查询该id是否存在   该文章是否启用
            while ($next_id < $last_id + 1) {
                $nextData = Db::table('overstudy')->where(['id' => $next_id, 'is_status' => 1, 'cate_id'=>4])->find();
                if (empty($nextData)) {
                    $next_id++;
                } else {
                    $is_next_page = true;
                    break;
                }
            }

            //如果存在上一页 查询上一页文章title
            if ($is_pre_page) {
                $pre_title = Db::table('overstudy')->field('title')->where(['id' => $pre_id])->find()['title'];
                $this->assign('pre_title', $pre_title);
            }
            //下一页同理
            if ($is_next_page) {
                $next_title = Db::table('overstudy')->field('title')->where(['id' => $next_id])->find()['title'];
                $this->assign('next_title', $next_title);
            }

            //查询数据
            $newData = Db::table('overstudy')->where(['id' => $id, 'is_status' => 1])->find();
            if (empty($newData)) {
                $this->error('查询错误');
            }


            $this->assign('pre_id', $pre_id);
            $this->assign('next_id', $next_id);
            $this->assign('is_pre', $is_pre_page);
            $this->assign('is_next', $is_next_page);
            $this->assign('newData', $newData);
            return $this->fetch();
        }
    }

    //显示办理流程详情
    public function showFlow(Request $request)
    {
        if ($request->isGet()) {
            $id = $request->get('id');

            //查询数据
            $newData = Db::table('flow')->where(['id' => $id])->find();
            if (empty($newData)) {
                $this->error('查询错误');
            }

            $this->assign('newData', $newData);
            return $this->fetch();
        }
    }
}