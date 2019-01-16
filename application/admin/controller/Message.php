<?php
/**
 * 留言板
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-16
 * Time: 15:25
 */
namespace app\admin\controller;
use app\common\tools\Util;
use Think\Db;
use think\Request;

class Message extends Common
{
    public function index()
    {
        //获取用户报名列表
        $messageData = Db::table('message')->order('create_at','desc')->paginate(10);
        //获取count
        $count = Db::table('message')->count();

        $this->assign('message', $messageData);
        $this->assign('count', $count);
        return $this->fetch();
    }

    public function delete(Request $request)
    {
        if ($request->isAjax()) {
            //接收id type
            $id = $request->param('id');
            $type = $request->param('type');
            //判断为单个删除还是批量删除
            if ($type == 'one') {
                //删除
                $res = Db::table('message')->where(['id' => $id])->delete();
                if ($res) {
                    return Util::show(1, '删除成功');
                } else {
                    return Util::show(0, '删除失败');
                }
            } elseif ($type == 'all') {
                //循环删除
                foreach ($id as $v) {
                    Db::table('message')->where(['id' => $v])->delete();
                }
                return Util::show(1, '删除成功');
            }
        } else {
            $this->error('非法操作', 'http://www.baidu.com');
        }
    }
}