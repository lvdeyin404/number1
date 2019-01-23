<?php
/**
 * 旅游板块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-29
 * Time: 10:53
 */
namespace app\admin\controller;
use app\common\tools\Util;
use think\Db;
use think\Request;

class Tourism extends Common
{
    /**
     * 显示数据
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        if($request->isPost()){
            //接收cate_id
            $cate_id = $request->param('cate_id');
            if($cate_id == 'all'){
                $tourData = Db::table('tourism')->paginate(10);
                $count = Db::table('tourism')->count();
            }else{
                $tourData = Db::table('tourism')->where(['cate_id'=>$cate_id])->paginate(10);
                $count = Db::table('tourism')->where(['cate_id'=>$cate_id])->count();
            }
            $this->assign('tourList',$tourData);
            $this->assign('count',$count);
            $this->assign('cate_id',$cate_id);
        }else{
            //获取旅游资料
            $tourData = Db::table('tourism')->paginate(10);
            $count = Db::table('tourism')->count();
            $this->assign('tourList',$tourData);
            $this->assign('count',$count);
            $this->assign('cate_id',0);
        }
        return $this->fetch();
    }

    //修改是否发布状态
    public function edit_status(Request $request)
    {
        //接收参数
        $id = $request->param('id');
        //通过id查询当前资讯的状态
        $status = Db::table('tourism')->field('is_status')->where(['id'=>$id])->find();
        if($status['is_status'] == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        //修改状态
        $res = Db::table('tourism')->where(['id'=>$id])->update(['is_status' => $status]);
        if($res == 1 || $res == 0){
            return Util::show(1,'修改成功',$id);
        }else{
            return Util::show(0,'修改失败',$id);
        }
    }

    /**
     * 添加
     * @param Request $request
     * @return false|mixed|string
     */
    public function add(Request $request)
    {
        if($request->isAjax()){
            //接收参数
            $data = $request->param();
            $title = $data['title'];
            $author = $data['tag'];
            $source = $data['aux_tag'];
            $cate_id = $data['cate_id'];
            $is_Release = $data['is_status'];
            $content = $data['content'];
            //判断是否输入title
            if(empty($title)){
                return Util::show('0','请输入标题');
            }
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $title = html_entity_decode($title);
            $title = strip_tags($title);
            $content = html_entity_decode($content);
            $content = strip_tags($content,"<p><span>");
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
                $info = $file->move('../public/uploads');
                if($info){
                    //保存数据
                    $imagepath = $info->getSaveName();
                    $add['img'] = $imagepath;
                }
            }
            //修改数据库
            $add['title'] = $title;
            $add['content'] = $content;
            $add['tag'] = $source;
            $add['aux_tag'] = $author;
            $add['cate_id'] = $cate_id;
            $add['is_status'] = $is_Release;
            $res = Db::table('tourism')->insert($add);
            if($res){
                return Util::show(1,'添加成功');
            }else{
                return Util::show(0,'添加失败');
            }
        }else{
            return $this->fetch();
        }
    }

    /**
     * 修改
     * @param Request $request
     * @return false|mixed|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit(Request $request)
    {
        if($request->isGet()){
            //接收参数
            $id = $request->param('id');
            //通过id获取文章信息
            $tourList = Db::table('tourism')->where(['id'=>$id])->find();
            $this->assign('tourList',$tourList);
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param();
            $id = $data['id'];
            $title = $data['title'];
            $tag = $data['tag'];
            $aux_tag = $data['aux_tag'];
            $is_status = $data['is_status'];
            $content = $data['content'];
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $content = html_entity_decode($content);
            $content = strip_tags($content,"<p><span>");
            //判断参数是否为空
            if(!$title){
                return Util::show(0,'请输入标题');
            }
            //修改数据库
            $updata['title'] = $title;
            $updata['content'] = $content;
            $updata['tag'] = $tag;
            $updata['aux_tag'] = $aux_tag;
            $updata['is_status'] = $is_status;
            $res = Db::table('tourism')->where(['id'=>$id])->update($updata);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }
        return $this->fetch();
    }

    /**
     * 删除
     * @param Request $request
     * @return false|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete(Request $request)
    {
        if($request->isAjax()){
            //接收id type
            $id = $request->param('id');
            $type = $request->param('type');
            //判断为单个删除还是批量删除
            if($type == 'one'){
                //删除
                $res = Db::table('tourism')->where(['id'=>$id])->delete();
                if($res){
                    return Util::show(1,'删除成功');
                }else{
                    return Util::show(0,'删除失败');
                }
            }elseif ($type == 'all'){
                //循环删除
                foreach ($id as $v){
                    Db::table('tourism')->where(['id'=>$v])->delete();
                }
                return Util::show(1,'删除成功');
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
        }
    }
}