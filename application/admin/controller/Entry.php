<?php
/**
 * 报名审查
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-07
 * Time: 9:14
 */
namespace app\admin\controller;
use app\common\tools\Util;
use think\Controller;
use Think\Db;
use think\Request;

class Entry extends Common
{
    public function index(Request $request)
    {
        //分类检索
        if ($request->isPost()) {
            //获取参数
            $id = $request->param('cate');
            if ($id == 3) {
                //获取用户报名列表
                $entryData = Db::table('entry')->paginate(10);
                //获取count
                $count = Db::table('entry')->count();
            } else {
                //获取用户报名列表
                $entryData = Db::table('entry')->where(['status' => $id])->paginate(10);
                //获取count
                $count = Db::table('entry')->where(['status' => $id])->count();
            }
        } else {
            $id = 3;
            //获取用户报名列表
            $entryData = Db::table('entry')->paginate(10);
            //获取count
            $count = Db::table('entry')->count();
        }
        $entryArr = $entryData->toArray()['data'];
        foreach ($entryArr as $k => $v) {
            $entryArr[$k]['id'] = $v['id'];
            $entryArr[$k]['name'] = $v['name'];
            $entryArr[$k]['phone'] = $v['phone'];
            $entryArr[$k]['address'] = $v['address'];
            $entryArr[$k]['create_at'] = $v['create_at'];
            $entryArr[$k]['status'] = $v['status'];
            $entryArr[$k]['cate']['cate_name'] = Db::table('entry_cate')->field('cate_name')->where(['id' => $v['cate_id']])->find()['cate_name'];
        }
        $this->assign('entryArr', $entryArr);
        $this->assign('entrylist', $entryData);
        $this->assign('count', $count);
        $this->assign('status', $id);
        return $this->fetch();
    }

    /**
     * 修改
     */
    public function edit(Request $request)
    {
        if ($request->isGet()) {
            //获取id
            $id = $request->param('id');
            //通过id获取资料
            $entryData = Db::table('entry')->where(['id' => $id])->find();
            //获取报名项目分类表
            $entryCateData = Db::table('entry_cate')->select();

            $this->assign('entrydata', $entryData);
            $this->assign('entrycatelist', $entryCateData);
            return $this->fetch();
        } elseif ($request->isPost()) {
            //获取上传数据
            $data = $request->param();
            $id = $data['id'];
            $name = $data['name'];
            $phone = $data['phone'];
            $cate = $data['cate'];
            $address = $data['address'];
            $status = $data['status'];
            //组装数据
            $update['name'] = $name;
            $update['phone'] = $phone;
            $update['cate_id'] = $cate;
            $update['address'] = $address;
            $update['status'] = $status;
            //修改
            $res = Db::table('entry')->where(['id' => $id])->update($update);
            if ($res) {
                return Util::show(1, '修改成功');
            } else {
                return Util::show(0, '修改失败');
            }
        }
    }

    /**
     * 修改状态码
     * @param Request $request
     */
    public function edit_status(Request $request)
    {
        if ($request->isAjax()) {
            //接收id
            $id = intval($request->param('id'));
            //通过id查找status
            $status = Db::table('entry')->field('status')->where(['id' => $id])->find();
            $status = $status['status'];
            if ($status == 1) {
                $status = 0;
            } else {
                $status = 1;
            }
            $res = Db::table('entry')->where(['id' => $id])->update(['status' => $status]);
            if ($res == 1 || $res == 0) {
                return Util::show(1, '审核通过');
            } else {
                return Util::show(0, '操作失败');
            }
        }
    }

    /**
     * 删除
     * @param Request $request
     */
    //删除
    public function delete(Request $request)
    {
        if ($request->isAjax()) {
            //接收id type
            $id = $request->param('id');
            $type = $request->param('type');
            //判断为单个删除还是批量删除
            if ($type == 'one') {
                //删除
                $res = Db::table('entry')->where(['id' => $id])->delete();
                if ($res) {
                    return Util::show(1, '删除成功');
                } else {
                    return Util::show(0, '删除失败');
                }
            } elseif ($type == 'all') {
                //循环删除
                foreach ($id as $v) {
                    Db::table('entry')->where(['id' => $v])->delete();
                }
                return Util::show(1, '删除成功');
            }
        } else {
            $this->error('非法操作', 'http://www.baidu.com');
        }
    }
}