<?php
/**
 * 返回前端的json格式数组
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-10-24
 * Time: 23:17
 */
namespace app\common\tools;
class Util{

    /**
     * API输出格式
     * @param $status
     * @param string $message
     * @param array $data
     */
    public static function show($status, $message='', $data=[]){
        $config = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
        return json_encode($config);
    }
}