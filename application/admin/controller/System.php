<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-28
 * Time: 16:58
 */
namespace app\admin\controller;
use app\common\tools\Util;
use Think\Db;
use think\Request;

class System extends Common
{
    //首页
    public function index()
    {
        //获取数据
        $systemdata = Db::table('systemctl')->find();
        $this->assign('system',$systemdata);
        return $this->fetch();
    }

    //修改数据
    public function edit(Request $request)
    {
        //判断get请求还是post   get:改变按钮开关  post:修改数据
        if($request->isGet()){
            //获取参数
            $status = $request->param('status');
            $id = $request->param('id');
            if($status == 1){
                $status = 0;
            }else{
                $status = 1;
            }
            //修改数据库
            $res = Db::table('systemctl')->where(['system_id'=>$id])->update(['web_is_open'=>$status]);
            if($res){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param();
            $title = $data['title'];
            $keywords = $data['keywords'];
            $desc = $data['desc'];
            $copyright = $data['copyright'];
            $icp = $data['icp'];
            $id = $data['id'];
            //验证数据
            if(!$title){
                return Util::show(0,'网站名称不能为空');
            }
            if(!$keywords){
                return Util::show(0,'关键字不能为空');
            }
            if(!$desc){
                return Util::show(0,'网站描述不能为空');
            }
            if(!$copyright){
                return Util::show(0,'底部版权信息不能为空');
            }
            //修改数据库
            $update['web_title'] = $title;
            $update['web_keywords'] = $keywords;
            $update['web_desc'] = $desc;
            $update['web_copyright'] = $copyright;
            $update['web_icp'] = $icp;
            $res = Db::table('systemctl')->where(['system_id'=>$id])->update($update);
            if($res == 0 || $res == 1){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(0,'修改失败');
            }
        }
    }

    //联系我们
    public function contact(Request $request)
    {
        if($request->isPost()){
            //获取数据
            $data = $request->param();
            $phone = $data['phone'];
            $email = $data['email'];
            $fax = $data['fax'];
            $address = $data['address'];
            $name = $data['name'];
            $id = $data['id'];
            //验证数据
            if(!$phone){
                return Util::show(0,'请输入公司电话');
            }
            if(!$email){
                return Util::show(0,'请输入公司邮箱');
            }
            if(!$fax){
                return Util::show(0,'请输入公司qq');
            }
            if(!$address){
                return Util::show(0,'请输入公司地址');
            }
            if(!$name){
                return Util::show(0,'请输入公司名称');
            }
            //修改数据库
            $update['name'] = $name;
            $update['phone'] = $phone;
            $update['email'] = $email;
            $update['fax'] = $fax;
            $update['address'] = $address;
            $res = Db::table('contact')->where(['id'=>$id])->update($update);
            if($res == 1 || $res == 0){
                return Util::show(1,'修改成功');
            }else{
                return Util::show(1,'修改成功');
            }
        }else{
            //获取数据
            $contactdata = Db::table('contact')->find();
            $this->assign('contact',$contactdata);
            return $this->fetch();
        }
    }
}