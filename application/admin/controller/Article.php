<?php
/**
 * 公司大事记
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-27
 * Time: 10:17
 */
namespace app\admin\controller;
use app\common\tools\Util;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\Null_;
use think\config\driver\Json;
use Think\Db;
use Think\Exception;
use think\Request;

class Article extends Common
{
    //资讯
    public function index(Request $request)
    {
        if($request->isPost()){
            //分类id
            $cate_id = $request->param('cate_id');
            //判断是否为全部信息
            if($cate_id == 0){
                //查询数据
                $newsData = Db::table('news')->order('new_id desc')->paginate(10);
                $count = Db::table('news')->count();
            }else{
                //查询数据
                $newsData = Db::table('news')->where(['cate_id'=>$cate_id])->order('new_id desc')->paginate(10);
                $count = Db::table('news')->where(['cate_id'=>$cate_id])->count();
            }
            $this->assign('cate_id',$cate_id);
            $this->assign('news',$newsData);
            $this->assign('count',$count);
        }else{
            //获取资讯
            $newsData = Db::table('news')->order('new_id desc')->paginate(10);
            $count = Db::table('news')->count();
            $this->assign('cate_id',0);
            $this->assign('news',$newsData);
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
        $status = Db::table('news')->field('is_Release')->where(['new_id'=>$id])->find();
        if($status['is_Release'] == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        //修改状态
        $res = Db::table('news')->where(['new_id'=>$id])->update(['is_Release' => $status]);
        if($res == 1 || $res == 0){
            return Util::show(1,'修改成功',$id);
        }else{
            return Util::show(0,'修改失败',$id);
        }
    }

    //修改文章
    public function edit(Request $request)
    {
        if($request->isGet()){
            //接收参数
            $id = $request->param('id');
            $index = $request->param('index');
            //通过id获取文章信息
            $newData = Db::table('news')->where(['new_id'=>$id])->find();
            $this->assign('news',$newData);
            return $this->fetch();
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param();
            $id = $data['id'];
            $title = $data['title'];
//            $author = $data['author'];
//            $source = $data['source'];
            $cate_id = $data['cate_id'];
            $is_Release = $data['is_Release'];
            $content = $data['content'];
            $two_content = $data['two_content'];
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 content只保留p
            $title = html_entity_decode($title);
//            $title = strip_tags($title);
            $content = html_entity_decode($content);
//            $content = strip_tags($content,"<p><span><img>");
            //判断参数是否为为空
            if(!$title){
                return Util::show(0,'请输入标题');
            }
            //修改数据库
            $updata['new_title'] = $title;
            $updata['new_content'] = $content;
//            $updata['source'] = $source;
            $updata['cate_id'] = $cate_id;
//            $updata['author'] = $author;
            $updata['is_Release'] = $is_Release;
            $updata['two_content'] = $two_content;
            $res = Db::table('news')->where(['new_id'=>$id])->update($updata);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }
    }

    //添加文章
    public function add(Request $request)
    {
        if($request->isAjax()){
            //接收参数
            $data = $request->param();
            $title = $data['title'];
//            $author = $data['author'];
//            $source = $data['source'];
            $cate_id = $data['cate_id'];
            $is_Release = $data['is_Release'];
            $content = $data['content'];
            $two_content = $data['two_content'];
            //判断是否输入title
            if(empty($title)){
                return Util::show('0','请输入标题');
            }
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $title = html_entity_decode($title);
//            $title = strip_tags($title);
            $content = html_entity_decode($content);
//            $content = strip_tags($content,"<p><span><img>");
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
                $info = $file->move('../public/uploads/Article');
                if($info){
                    //保存数据
                    $imagepath = $info->getSaveName();
                    $add['pic_path'] = $imagepath;
                }
            }
            //修改数据库
            $add['new_title'] = $title;
            $add['new_content'] = $content;
//            $add['source'] = $source;
//            $add['author'] = $author;
            $add['cate_id'] = $cate_id;
            $add['is_Release'] = $is_Release;
            $add['two_content'] = $two_content;
            $add['add_time'] = date('Y-m-d');
            $res = Db::table('news')->insert($add);
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
                $res = Db::table('news')->where(['new_id'=>$id])->delete();
                if($res){
                    return Util::show(1,'删除成功');
                }else{
                    return Util::show(0,'删除失败');
                }
            }elseif ($type == 'all'){
                //循环删除
                foreach ($id as $v){
                    Db::table('news')->where(['new_id'=>$v])->delete();
                }
                return Util::show(1,'删除成功');
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
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
            //判断当前文章是否有图片 如果有先把旧图片删除
            $picPath = Db::table('news')->where(['new_id'=>$id])->field('pic_path')->find();
            if (!empty($picPath['pic_path'])){
                //删除文件
                $filePath = "../public/uploads/Article/{$picPath['pic_path']}";
                try{
                    unlink($filePath);
                }catch (Exception $e){

                }
            }
            //移动文件
            $info = $file->move('../public/uploads/Article');
            if($info){
                //将路径存入数据库
                $imagepath = $info->getSaveName();
                $res = Db::table('news')->where(['new_id'=>$id])->update(['pic_path'=>$imagepath]);
                if($res == 1 || $res == 0){
                    return Util::show(0,'上传成功');
                }else{
                    return Util::show(4,'上传失败');
                }
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
        }
    }
}