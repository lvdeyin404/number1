<?php
/**
 * 公司简介模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-12-26
 * Time: 14:31
 */
namespace app\admin\controller;

use app\common\tools\Util;
use PhpParser\Node\Scalar\DNumber;
use Think\Db;
use think\Request;

class Profile extends Common
{
    //首页
    public function index(Request $request)
    {
        //查询数据
        $profileData = Db::table('profile')->select();
        $this->assign('profileList',$profileData);
        return $this->fetch();
    }

    /**
     * 修改
     * @param Request $request
     */
    public function edit(Request $request)
    {
        if($request->isGet()){
            //接收id
            $id = $request->param('id');
            $profileData = Db::table('profile')->where(['id'=>$id])->find();
            $this->assign('editList',$profileData);
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param('');
            $id = $data['id'];
            $title = $data['title'];
            $content = $data['content'];
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $content = html_entity_decode($content);
            $content = strip_tags($content,"<p>");
            //添加到数据库
            $update['title'] = $title;
            $update['content'] = $content;
            $res = Db::table('profile')->where(['id'=>$id])->update($update);
            if($res == 1 || $res == 0){
                return Util::show('1','修改成功');
            }else{
                return Util::show('0','修改失败');
            }
        }
        return $this->fetch();
    }
}