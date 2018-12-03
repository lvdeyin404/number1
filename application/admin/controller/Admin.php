<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-28
 * Time: 14:15
 */
namespace app\admin\controller;
use app\common\tools\Util;
use MongoDB\BSON\UTCDateTime;
use Think\Db;
use think\Request;

class Admin extends Common
{
    //首页
    public function index()
    {
        //查询管理员信息
        $admindata = Db::table('admin')->select();
        $this->assign('admin',$admindata);
        return $this->fetch();
    }

    //修改
    public function edit(Request $request)
    {
        if($request->isAjax()){
            //接收参数
            $id = $request->param('id');
            $username = $request->param('username');
            $password = $request->param('password');
            $password2 = $request->param('password2');
            //判断用户名是否为空
            if(!$username){
                return Util::show(0,'请输入用户名');
            }
            $update['username'] = $username;
            //判断是否修改密码
            if($password){
                //判断是否输入了新密码
                if(!$password2){
                    return Util::show(0,'请输入新密码');
                }

                //通过id查询旧密码
                $oldpassword = Db::table('admin')->where(['id'=>$id])->find();

                //判断输入的旧密码是否正确
                if($oldpassword['password'] != $this->password($password,$oldpassword['salt'])){
                    return Util::show(0,'旧密码输入错误');
                }

                //判断新密码与旧密码是否相同
                if($this->password($password2,$oldpassword['salt']) == $oldpassword['password']){
                    return Util::show(0,'新密码不能与旧密码相同');
                }

                //组装数组
                $update['password'] = $this->password($password2,$oldpassword['salt']);
            }

            //修改数据库
            $res = Db::table('admin')->where(['id'=>$id])->update($update);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }elseif($request->isGet()){
            //接收id
            $id = $request->param('id');
            $admindata = Db::table('admin')->where(['id'=>$id])->find();
            $this->assign('admin',$admindata);
            return $this->fetch();
        }
    }
}