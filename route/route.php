<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::get('Index', 'index/Index/index');
Route::get('Profile', 'index/Profile/index');
Route::get('Down', 'index/Down/index');
Route::get('Article', 'index/Article/index');
Route::get('Entry', 'index/Entry/index');
Route::get('Overstudy', 'index/Overstudy/index');
Route::get('Trade', 'index/Trade/index');
Route::post('Regist/message', 'index/Regist/message');
Route::post('Regist/sign', 'index/Regist/sign');
Route::get('Studytour', 'index/Studytour/index');
Route::get('Tourisms', 'index/Tourisms/index');
return [

];
