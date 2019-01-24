<?php
/**
 * 留学栏目
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-27
 * Time: 14:18
 */
namespace app\admin\controller;
use app\common\tools\Util;
use App\Utility\Redis;
use Think\Db;
use Think\Exception;
use think\Request;

class Overstudy extends Common
{
    //留学计划 显示数据
    public function play()
    {
        //获取数据
        $playdata = Db::table('overstudy')->where(['cate_id'=>1])->paginate(10);
        $count = Db::table('overstudy')->where(['cate_id'=>1])->count();
        $this->assign('playList',$playdata);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //考试等级 显示数据
    public function grade()
    {
        //获取数据
        $gradedata = Db::table('overstudy')->where(['cate_id'=>2])->paginate(10);
        $count = Db::table('overstudy')->where(['cate_id'=>2])->count();
        $this->assign('gradeList',$gradedata);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //校园风采  显示数据
    public function campus()
    {
        //获取数据
        $campusdata = Db::table('overstudy')->where(['cate_id'=>3])->paginate(10);
        $count = Db::table('overstudy')->where(['cate_id'=>3])->count();
        $this->assign('campusList',$campusdata);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //团队顾问 显示数据
    public function team()
    {
        //获取数据
        $teamData = Db::table('overstudy')->where(['cate_id'=>4])->paginate(10);
        $count = Db::table('overstudy')->where(['cate_id'=>4])->count();
        $this->assign('teamList',$teamData);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //成功案例 显示数据
    public function successanli()
    {
        //获取数据
        $successdata = Db::table('overstudy')->where(['cate_id'=>5])->paginate(10);
        $count = Db::table('overstudy')->where(['cate_id'=>5])->count();
        $this->assign('successList',$successdata);
        $this->assign('count',$count);
        return $this->fetch();
    }

    //院校推荐
    public function school(Request $request)
    {
        if($request->isPost()){
            //分类id
            $id = $request->param('id');
            //判断是否为全部信息
            if($id == 'all'){
                //查询数据
                $schooldata = Db::table('school')->select();
                $count = Db::table('school')->count();
            }else{
                $data = Db::table('cate_school')->select();
                //查询该分类下是否有子孙分类(如果有 查询结果全部显示)
                $rst[] = intval($id);
                $this->categort_subid($data, $rst, $id);
                //查询数据
                $schooldata = Db::table('school')->where(['pid'=>$rst])->select();
                $count = Db::table('school')->where(['pid'=>$rst])->count();
            }
            //通过id查询分类名称
            $this->assign('cate_id',$id);
            $this->assign('schoolList',$schooldata);
            $this->assign('count',$count);
        }else{
            //获取院校分类数据
            $schooldata = Db::table('school')->select();
            $count = Db::table('school')->count();
            $this->assign('count',$count);
            $this->assign('schoolList',$schooldata);
            $this->assign('cate_id',0);
        }
        //获取分类等级
        $cate_school = Db::table('cate_school')->select();
        $this->category_list($cate_school, $cateList);
        $this->assign('cateList',$cateList);
        return $this->fetch();
    }

    /**
     * 修改是否上架状态
     * @param Request $request
     */
    public function edit_status(Request $request)
    {
        //接收参数
        $id = $request->param('id');
        //通过id查询当前资讯的状态
        $status = Db::table('overstudy')->field('is_status')->where(['id'=>$id])->find();
        if($status['is_status'] == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        //修改状态
        $res = Db::table('overstudy')->where(['id'=>$id])->update(['is_status' => $status]);
        if($res == 1 || $res == 0){
            return Util::show(1,'修改成功',$id);
        }else{
            return Util::show(0,'修改失败',$id);
        }
    }

    /**
     * 修改是否上架状态  院校推荐栏目
     * @param Request $request
     */
    public function edit_status2(Request $request)
    {
        //接收参数
        $id = $request->param('id');
        //通过id查询当前资讯的状态
        $status = Db::table('school')->field('is_status')->where(['id'=>$id])->find();
        if($status['is_status'] == 0){
            $status = 1;
        }else{
            $status = 0;
        }
        //修改状态
        $res = Db::table('school')->where(['id'=>$id])->update(['is_status' => $status]);
        if($res == 1 || $res == 0){
            return Util::show(1,'修改成功',$id);
        }else{
            return Util::show(0,'修改失败',$id);
        }
    }

    /**
     * 添加
     * @param Request $request
     * @return mixed|string
     */
    public function add(Request $request)
    {
        if($request->isAjax()){
            //接收参数
            $data = $request->param();
            $title = $data['title'];
            $is_Release = $data['is_status'];
            $cate_id = $data['cate_id'];
            $content = $data['content'];
            //判断是否输入title
            if(empty($title)){
                return Util::show('0','请输入标题');
            }
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
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
                $info = $file->move('../public/uploads/Overstudy');
                if($info){
                    //保存数据
                    $imagepath = $info->getSaveName();
                    $add['img'] = $imagepath;
                }
            }
            //修改数据库
            $add['title'] = $title;
            $add['content'] = $content;
            $add['is_status'] = $is_Release;
            $add['cate_id'] = $cate_id;
            $res = Db::table('overstudy')->insert($add);
            if($res){
                return Util::show(1,'添加成功');
            }else{
                return Util::show(0,'添加失败');
            }
        }else{
            $cate_id = $request->param('cate_id');
            $this->assign('cate_id',$cate_id);
            return $this->fetch();
        }
    }

    /**
     * 添加  院校推荐栏目
     * @param Request $request
     * @return mixed|string
     */
    public function add2(Request $request)
    {
        if($request->isAjax()){
            //接收参数
            $data = $request->param();
            $name = $data['name'];
            $cate_id = $data['cate_id'];
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
                $info = $file->move('../public/uploads/Overstudy');
                if($info){
                    //保存数据
                    $imagepath = $info->getSaveName();
                    $add['img'] = $imagepath;
                }
            }
            //修改数据库
            $add['school_name'] = $name;
            $add['pid'] = $cate_id;
            $add['is_status'] = $is_status;
            $res = Db::table('school')->insert($add);
            if($res){
                return Util::show(1,'添加成功');
            }else{
                return Util::show(0,'添加失败');
            }
        }else{
            //获取分类等级
            $cate_school = Db::table('cate_school')->select();
            $this->category_list($cate_school, $cateList);
            $this->assign('cateList',$cateList);
            return $this->fetch();
        }
    }

    /**
     * 添加院校分类
     * @param Request $request
     */
    public function addSchoolCate(Request $request)
    {
        if($request->isPost()){
            //获取分类名称以及pid
            $cateName = $request->param('cateName');
            $pid = intval($request->param('pid'));
            $add['school_cate'] = $cateName;
            $add['pid'] = $pid;
            $res = Db::table('cate_school')->insert($add);
            if($res){
                return Util::show('1','添加成功');
            }
        }else{
            //获取院校分类
            $cateList = Db::table('cate_school')->where(['pid'=>0])->select();
//            $this->category_list($schoolCate,$cateList);
            $this->assign('cateList',$cateList);
            return $this->fetch();
        }
    }

    /**
     * 修改
     * @param Request $request
     * @return mixed|string
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
            $overstudyData = Db::table('overstudy')->where(['id'=>$id])->find();
            $this->assign('overstudyList',$overstudyData);
            return $this->fetch();
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param();
            $id = $data['id'];
            $title = $data['title'];
            $is_status = $data['is_status'];
            $content = $data['content'];
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $content = html_entity_decode($content);
            $content = strip_tags($content,"<p><span>");
            //判断参数是否为为空
            if(!$title){
                return Util::show(0,'请输入标题');
            }
            //修改数据库
            $updata['title'] = $title;
            $updata['content'] = $content;
            $updata['is_status'] = $is_status;
            $res = Db::table('overstudy')->where(['id'=>$id])->update($updata);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }
    }

    /**
     * 修改  院校推荐栏目
     * @param Request $request
     * @return mixed|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit2(Request $request)
    {
        if($request->isGet()){
            //接收参数
            $id = $request->param('id');
            //通过id获取文章信息
            $schoolData = Db::table('school')->where(['id'=>$id])->find();
            //获取分类等级
            $cate_school = Db::table('cate_school')->select();
            $this->category_list($cate_school, $cateList);
            //通过id获取pid
            $pid = Db::table('school')->field('pid')->where(['id'=>$id])->find();
            $pid = $pid['pid'];
            $this->assign('cateList',$cateList);
            $this->assign('pid',$pid);
            $this->assign('schoolList',$schoolData);
            return $this->fetch();
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param();
            $id = $data['id'];
            $name = $data['name'];
            $cate_id = $data['cate_id'];
            $is_status = $data['is_status'];
            //判断参数是否为为空
            if(!$name){
                return Util::show(0,'请输入院校名称');
            }
            //修改数据库
            $updata['school_name'] = $name;
            $updata['is_status'] = $is_status;
            $updata['pid'] = $cate_id;
            $res = Db::table('school')->where(['id'=>$id])->update($updata);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }
    }

    /**
     * 删除  (单个删除 批量删除)
     * @param Request $request
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
                $res = Db::table('overstudy')->where(['id'=>$id])->delete();
                if($res){
                    return Util::show(1,'删除成功');
                }else{
                    return Util::show(0,'删除失败');
                }
            }elseif ($type == 'all'){
                //循环删除
                foreach ($id as $v){
                    Db::table('overstudy')->where(['id'=>$v])->delete();
                }
                return Util::show(1,'删除成功');
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
        }
    }

    /**
     * 删除  (单个删除 批量删除)  院校栏目
     * @param Request $request
     */
    public function delete2(Request $request)
    {
        if($request->isAjax()){
            //接收id type
            $id = $request->param('id');
            $type = $request->param('type');
            //判断为单个删除还是批量删除
            if($type == 'one'){
                //删除
                $res = Db::table('school')->where(['id'=>$id])->delete();
                if($res){
                    return Util::show(1,'删除成功');
                }else{
                    return Util::show(0,'删除失败');
                }
            }elseif ($type == 'all'){
                //循环删除
                foreach ($id as $v){
                    Db::table('school')->where(['id'=>$v])->delete();
                }
                return Util::show(1,'删除成功');
            }
        }else{
            $this->error('非法操作','http://www.baidu.com');
        }
    }

    /**
     * 删除院校分类
     * @param Request $request
     */
    public function delSchoolCate(Request $request)
    {
        if($request->isAjax()){
            //获取id
            $id = intval($request->param('id'));
            //判断该分类下是否有子分类
            $cateData = Db::table('cate_school')->select();
            $this->categort_subid($cateData,$ids, $id);
            if($ids){
                //该分类下有子分类  提醒用户不能删除
                return Util::show('0','该分类下还有子分类，不能删除');
            }else{
                //判断分类下是否有数据
                $schoolData = Db::table('school')->where(['pid'=>$id])->select();
                if($schoolData){
                    //有数据   返回用户不能删除
                    return Util::show('0','该分类下有数据，不能删除');
                }else{
                    //可以删除
                    $res = Db::table('cate_school')->where(['id'=>$id])->delete();
                    if($res){
                        return Util::show('1','删除成功');
                    }else{
                        return Util::show('0','删除失败');
                    }
                }
            }
        }else{
            //获取所有分类
            $schoolData = Db::table('cate_school')->select();
            $this->category_list($schoolData, $cateList);
            $this->assign('catelist', $cateList);
            return $this->fetch();
        }
    }

    /**
     * 图片上传
     * @param Request $request
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
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
            $picPath = Db::table('overstudy')->where(['id'=>$id])->field('img')->find();
            if (!empty($picPath['img'])){
                //删除文件
                $filePath = "../public/uploads/Overstudy/{$picPath['img']}";
                try{
                    unlink($filePath);
                }catch (\Exception $e){
//                    echo $e->getMessage();
                }
            }
            //移动文件
            $info = $file->move('../public/uploads/Overstudy');
            if($info){
                //将路径存入数据库
                $imagepath = $info->getSaveName();
                $res = Db::table('overstudy')->where(['id'=>$id])->update(['img'=>$imagepath]);
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

    /**
     * 图片上传  院校推荐栏目
     * @param Request $request
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function uploads2(Request $request)
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
            $picPath = Db::table('school')->where(['id'=>$id])->field('img')->find();
            if (!empty($picPath['img'])){
                //删除文件
                $filePath = "../public/uploads/Overstudy/{$picPath['img']}";
                try{
                    unlink($filePath);
                }catch (Exception $e){

                }
            }
            //移动文件
            $info = $file->move('../public/uploads/Overstudy');
            if($info){
                //将路径存入数据库
                $imagepath = $info->getSaveName();
                $res = Db::table('school')->where(['id'=>$id])->update(['img'=>$imagepath]);
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

    //查看图片大图
    public function picshow(Request $request)
    {
        //接收pic_path
        $pic_path = $request->param('path');
        $this->assign('pic_path',$pic_path);
        return $this->fetch();
    }
}