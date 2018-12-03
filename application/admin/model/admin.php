<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-25
 * Time: 11:04
 */
namespace app\admin\model;
use think\Model;

class admin extends Model
{
    //验证码用户名和密码
    public function checkUser($username,$password)
    {
        //根据用户名查询信息
        $adminData = $this->field('id,password,salt,login_count')->where(['username'=>$username])->find();
        if(!$adminData){
            return false;
        }
        if($adminData['password'] == $this->password($password,$adminData['salt'])){
            return ['id'=>$adminData['id'],'login_count'=>$adminData['login_count']];
        }
    }

    //密码验证
    private function password($password,$salt)
    {
        return md5(md5($password).$salt);
    }
}