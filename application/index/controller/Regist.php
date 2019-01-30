<?php
/**
 * 留言板 报名  学校---接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-16
 * Time: 12:58
 */
namespace app\index\controller;
use app\common\tools\Util;
use Think\Db;
use think\Request;

class Regist extends Common
{
    //提交报名
    public function sign(Request $request)
    {
        //接收数据
        $data = $request->post();
        $name = $data['name'];
        $phone = $data['phone'];
        $address = $data['address'];
        $cate_id = $data['cate'];
        if(!$name){
            return Util::show(0,'请输入名称');
        }
        if(!$phone){
            return Util::show(0,'请输入电话号码');
        }
        if(!$address){
            return Util::show(0,'请输入地址');
        }
        if(!$cate_id){
            return Util::show(0,'请选择报名项');
        }
        // 验证联系电话
        $isMob="/^1[34578]{1}\d{9}$/";
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if(!preg_match($isMob,$phone) && !preg_match($isTel,$phone)){
            return Util::show(0,'请输入正确的电话号码');
        }
        //存入数据库
        $add['name'] = $name;
        $add['phone'] = $phone;
        $add['address'] = $address;
        $add['create_at'] = date("Y-m-d H:m:s");
        $add['cate_id'] = $cate_id;
        $res = Db::table('entry')->insert($add);
        if($res){
            return Util::show('2','报名成功');
        }else{
            return Util::show('1','报名失败,请稍后再试');
        }
    }


    //留言板提交
    public function message(Request $request)
    {
        //接收参数
        $data = $request->post();
        $name = $data['name'];
        $phone = $data['phone'];
        $content = $data['content'];

        //验证数据完整性
        if(!$name){
            return Util::show('0','请输入用户名');
        }
        if(!$phone){
            return Util::show('0','请输入电话号码');
        }
        if(!$content){
            return Util::show('0','请输入留言内容');
        }
        // 验证联系电话
        $isMob="/^1[34578]{1}\d{9}$/";
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if(!preg_match($isMob,$phone) && !preg_match($isTel,$phone)){
            return Util::show(0,'请输入正确的电话号码');
        }
        //存入数据库
        $add['name'] = $name;
        $add['phone'] = $phone;
        $add['content'] = $content;
        $add['create_at'] = date('Y-m-d H:i:s');
        $res = Db::table('message')->insert($add);
        if($res){
            return Util::show('2','报名成功');
        }else{
            return Util::show('1','报名失败,请稍后再试');
        }
    }

    //获取学校
    public function getSchool(Request $request)
    {
        //获取id
        $data = $request->post();
        if(!isset($data['id'])){
            return Util::show('0','not id');
        }
        $id = intval($data['id']);

        $cateData = Db::table('cate_school')->select();
        //获得当前分类下的子分类
        $ids = [$id];
        $this->category_subId($cateData,$ids, $id);

        //查询学校数据
        $schoolList = Db::table('school')->where('pid','in',$ids)->limit(8)->select();
        if($schoolList){
            return Util::show('2', 'OK', $schoolList);
        }else{
            return Util::show('1','查询失败');
        }
    }

    //公司大事记新闻接口
    public function getArticles(Request $request)
    {
//        if($request->isAjax()){
            //获取分类id
            $data = $request->post();
            $cate_id = $data['cate_id'];
            //查询数据
            $news = Db::table('news')->where(['cate_id'=>$cate_id, 'is_Release'=>1])->paginate(5);
            //获取数据 和 分页数据
            $currentPage = $news->currentPage();  //当前页数
            $allPage = $news->lastPage();   //总页数
            $newsData = $news->items();
            return Util::show('2','OK', ["news"=>$newsData,"currentPage"=>$currentPage, "allPage"=>$allPage]);
//        }
    }
}