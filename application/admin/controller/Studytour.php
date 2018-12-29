<?php
/**
 * 游学栏目
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-28
 * Time: 14:04
 */
namespace app\admin\controller;
use app\common\tools\Util;
use think\Db;
use think\Request;

class Studytour extends Common
{
    public function index(Request $request)
    {
        if($request->isPost()){
            //获取分类id
            $cate_id = $request->param('cate_id');
            if($cate_id == 'all'){
                //获取数据
                $studytourData = Db::table('studytour')->select();
                //查询数量
                $count = Db::table('studytour')->count();
            }else{
                //获取数据
                $studytourData = Db::table('studytour')->where(['cate_id'=>$cate_id])->select();
                //查询数量
                $count = Db::table('studytour')->where(['cate_id'=>$cate_id])->count();
            }
            $this->assign('studyList',$studytourData);
            $this->assign('cate_id',$cate_id);
            $this->assign('count',$count);
        }else{
            //获取数据
            $studytourData = Db::table('studytour')->select();
            //查询数量
            $count = Db::table('studytour')->count();
            $this->assign('studyList',$studytourData);
            $this->assign('cate_id',0);
            $this->assign('count',$count);
        }
        return $this->fetch();
    }

    //修改是否发布状态
    public function edit_status(Request $request)
    {
        //接收参数
        $id = $request->param('id');
        //通过id查询当前资讯的状态
        $status = Db::table('studytour')->field('is_status')->where(['id'=>$id])->find();
        if($status['is_status'] == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        //修改状态
        $res = Db::table('studytour')->where(['id'=>$id])->update(['is_status' => $status]);
        if($res == 1 || $res == 0){
            return Util::show(1,'修改成功',$id);
        }else{
            return Util::show(0,'修改失败',$id);
        }
    }

    //修改
    public function edit(Request $request)
    {
        if($request->isGet()){
            //接收参数
            $id = $request->param('id');
            //通过id获取文章信息
            $studyData = Db::table('studytour')->where(['id'=>$id])->find();
            //通过id查询cate_id
            $cate_id = Db::table('studytour')->field('cate_id')->where(['id'=>$id])->find();
            $cate_id = $cate_id['cate_id'];
            $this->assign('cate_id',$cate_id);
            $this->assign('studyList',$studyData);
            return $this->fetch();
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param();
            $id = $data['id'];
            $title = $data['title'];
            $cate_id = $data['cate_id'];
            $is_status = $data['is_status'];
            $content = $data['content'];
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $content = html_entity_decode($content);
            $content = strip_tags($content,"<p>");
            //判断参数是否为为空
            if(!$title){
                return Util::show(0,'请输入标题');
            }
            //修改数据库
            $updata['title'] = $title;
            $updata['content'] = $content;
            $updata['cate_id'] = $cate_id;
            $updata['is_status'] = $is_status;
            $res = Db::table('studytour')->where(['id'=>$id])->update($updata);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }
    }

    //添加
    public function add(Request $request)
    {
        if($request->isAjax()){
            //接收参数
            $data = $request->param();
            $title = $data['title'];
            $cate_id = $data['cate_id'];
            $is_status = $data['is_status'];
            $content = $data['content'];
            //判断是否输入title
            if(empty($title)){
                return Util::show('0','请输入标题');
            }
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $content = html_entity_decode($content);
            $content = strip_tags($content,"<p>");
            //修改数据库
            $add['title'] = $title;
            $add['content'] = $content;
            $add['cate_id'] = $cate_id;
            $add['is_status'] = $is_status;
            $res = Db::table('studytour')->insert($add);
            if($res){
                return Util::show(1,'添加成功');
            }else{
                return Util::show(0,'添加失败');
            }
        }else{
            return $this->fetch();
        }
    }

    //删除
    public function delete(Request $request)
    {
        if($request->isAjax()){
            //接收id type
            $id = $request->param('id');
            $type = $request->param('type');
            //判断为单个删除还是批量删除
            if($type == 'one'){
                //删除
                $res = Db::table('studytour')->where(['id'=>$id])->delete();
                if($res){
                    return Util::show(1,'删除成功');
                }else{
                    return Util::show(0,'删除失败');
                }
            }elseif ($type == 'all'){
                //循环删除
                foreach ($id as $v){
                    Db::table('studytour')->where(['id'=>$v])->delete();
                }
                return Util::show(1,'删除成功');
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
        }
    }

}