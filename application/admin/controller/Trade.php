<?php
/**
 * 经贸
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-02
 * Time: 13:53
 */
namespace app\admin\controller;
use app\common\tools\Util;
use think\Db;
use think\Request;

class Trade extends Common
{
    public function index()
    {
        $tradeData = Db::table('trade')->select();
        $this->assign('tradeList',$tradeData);
        return $this->fetch();
    }

    /**
     * 修改
     * @param Request $request
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit(Request $request)
    {
        if($request->isGet()){
            //接收id
            $id = $request->param('id');
            $tradeData = Db::table('trade')->where(['id'=>$id])->find();
            $this->assign('editList',$tradeData);
        }elseif ($request->isPost()){
            //接收参数
            $data = $request->param('');
            $id = $data['id'];
            $content = $data['content'];
            //全局过滤有转码  这里转换回来 否则前端不显示样式 防止xss 使用函数删除标签 只保留p
            $content = html_entity_decode($content);
            $content = strip_tags($content,"<p><span>");
            //添加到数据库
            $update['content'] = $content;
            $res = Db::table('trade')->where(['id'=>$id])->update($update);
            if($res == 1 || $res == 0){
                return Util::show('1','修改成功');
            }else{
                return Util::show('0','修改失败');
            }
        }
        return $this->fetch();
    }
}