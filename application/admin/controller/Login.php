<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-20
 * Time: 10:41
 */
namespace app\admin\controller;
use app\admin\model\admin;
use think\captcha\Captcha;
use think\Controller;
use think\Request;
use think\Session;

class Login extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if($request->isPost()){
            //接收用户名密码 验证码
            $username = $request->param('username');
            $password = $request->param('password');
            $verify = $request->param('verify');
            //判断数据是否为空
            if(empty($username)){
                $this->error('用户名不能为空');
            }
            if(empty($password)){
                $this->error('密码不能为空');
            }
            if(empty($verify)){
                $this->error('验证码不能为空');
            }

            //验证验证码
            if(!$this->checkverify($verify)){
                $this->error('验证码输入错误');
            }
            //验证用户名和密码
            $adminModel = new admin();
            if($userinfo = $adminModel->checkUser($username,$password)){
                //修改登录次数
                $data['login_count'] = $userinfo['login_count'] + 1;
                if($adminModel->save($data,['id'=>$userinfo['id']])){
                    session('userinfo',$userinfo);
                    $this->redirect('Index/index');
                }
            }
            $this->error('用户名或密码错误',url('Login/index'));
        }else{
            return $this->fetch();
        }
    }

    //生成验证码
    public function verify()
    {
        $config = array(
            'fontsize' => 30,
            'length' => 4,
            'useNoise' => false,
        );
        $Verify = new Captcha($config);
        return $Verify->entry();
    }

    //验证验证码是否正确
    private function checkverify($code)
    {
        $Verify = new Captcha();
        return $Verify->check($code);
    }

    //退出登录
    public function logout()
    {
        session(null);
        $this->redirect('Login/index');
    }
}