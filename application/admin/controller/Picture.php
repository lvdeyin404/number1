<?php
/**
 * 轮播图片管理
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-28
 * Time: 9:16
 */
namespace app\admin\controller;

use Think\Db;
use think\Request;
use app\common\tools\Util;

class Picture extends Common
{
    //轮播图首页
    public function index()
    {
        //取出轮播数据
        $picdata = Db::table('picture')->paginate(5);
        $count = Db::table('picture')->count();
        $this->assign('count',$count);
        $this->assign('pic',$picdata);
        return $this->fetch();
    }

    //查看图片大图
    public function picshow(Request $request)
    {
        //接收pic_path
        $pic_path = $request->param('path');
        $this->assign('pic_path',$pic_path);
        return $this->fetch();
    }

    //添加图片
    public function add(Request $request)
    {
        if($request->isAjax()){
            //接收参数
            $data = $request->param();
            $tags = $data['tags'];
            $is_status = $data['is_status'];
            //图片文件
            $file = $request->file('image');
            //判断是否有文件上传
            if($file != NULL){
                $image = $request->file('image')->getInfo();
                //限制参数
                $maxsize = 3145728;
                $type = ['image/jpg','image/jpeg','image/gif','image/png'];
                if($image['error'] != 0){
                    return Util::show(1,'上传错误,请重试');
                }
                //判断图片大小与类型
                if($image['size'] > $maxsize){
                    return Util::show(2,'图片过大');
                }
                if(!in_array($image['type'],$type)){
                    return Util::show(3,'图片类型不支持');
                }
                //移动文件
                $info = $file->move('../public/uploads/lunbo');
                if($info){
                    //保存数据
                    $imagepath = $info->getSaveName();
                    $add['pic_path'] = $imagepath;
                }
            }
            //修改数据库
            $add['pic_tags'] = $tags;
            $add['is_status'] = $is_status;
            $add['update_time'] = date('Y-m-d H:i:s');
            $res = Db::table('picture')->insert($add);
            if($res){
                return Util::show(1,'添加成功');
            }else{
                return Util::show(0,'添加失败');
            }
        }else{
            return $this->fetch();
        }
    }

    //修改是否发布状态
    public function edit_status(Request $request)
    {
        //接收参数
        $id = $request->param('id');
        //通过id查询当前资讯的状态
        $status = Db::table('picture')->field('is_status')->where(['pic_id'=>$id])->find();
        if($status['is_status'] == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        //修改状态
        $res = Db::table('picture')->where(['pic_id'=>$id])->update(['is_status' => $status]);
        if($res){
            return Util::show(1,'修改成功',$id);
        }else{
            return Util::show(0,'修改失败',$id);
        }
    }

    //图片资料修改
    public function edit(Request $request)
    {
        if($request->isGet()){
            //接收id
            $id = $request->param('id');
            //通过id查询数据
            $picdataone = Db::table('picture')->where(['pic_id'=>$id])->find();
            $this->assign('picdataone',$picdataone);
            return $this->fetch();
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param();
            $id = $data['id'];
            $tags = $data['tags'];
            $is_status = $data['is_status'];
            //修改数据库
            $updata['pic_tags'] = $tags;
            $updata['is_status'] = $is_status;
            $res = Db::table('picture')->where(['pic_id'=>$id])->update($updata);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }
    }

    //图片上传
    public function uploads(Request $request)
    {
        $file = $request->file('image');
        $id = $request->param('id');
        $image = $file->getInfo();
        //获取图片资源
        if($file){
            //限制参数
            $maxsize = 3145728;
            $type = ['image/jpg','image/jpeg','image/gif','image/png'];
            if($image['error'] != 0){
                return Util::show(1,'上传错误,请重试');
            }
            //判断图片大小与类型
            if($image['size'] > $maxsize){
                return Util::show(2,'图片过大');
            }
            if(!in_array($image['type'],$type)){
                return Util::show(3,'图片类型不支持');
            }
            //移动文件
            $info = $file->move('../public/uploads/lunbo');
            if($info){
                //将路径存入数据库
                $imagepath = $info->getSaveName();
                $res = Db::table('picture')->where(['pic_id'=>$id])->update(['pic_path'=>$imagepath]);
                if($res == 1 || $res == 0){
                    return Util::show(0,'上传成功',$imagepath);
                }else{
                    return Util::show(4,'上传失败');
                }
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
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
                $res = Db::table('picture')->where(['pic_id'=>$id])->delete();
                if($res){
                    return Util::show(1,'删除成功');
                }else{
                    return Util::show(0,'删除失败');
                }
            }elseif ($type == 'all'){
                //循环删除
                foreach ($id as $v){
                    Db::table('picture')->where(['pic_id'=>$v])->delete();
                }
                return Util::show(1,'删除成功');
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
        }
    }

}