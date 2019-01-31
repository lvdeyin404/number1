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
Route::get('showNews', 'index/Article/showNews');
Route::get('showLists', 'index/Article/showLists');
Route::get('Entry', 'index/Entry/index');
Route::get('Overstudy', 'index/Overstudy/index');
Route::get('showSchool', 'index/Overstudy/showSchool');
Route::get('showPlan', 'index/Overstudy/showPlan');
Route::get('showHots', 'index/Overstudy/showHots');
Route::get('showTeacher', 'index/Overstudy/showTeacher');
Route::get('showTeacherList', 'index/Overstudy/showTeacherList');
Route::get('showFlow', 'index/Overstudy/showFlow');
Route::get('showOverLists', 'index/Overstudy/showOverLists');
Route::get('showSchoolList', 'index/Overstudy/showSchoolList');
Route::get('Trade', 'index/Trade/index');
Route::post('Regist/message', 'index/Regist/message');
Route::post('Regist/sign', 'index/Regist/sign');
Route::post('Regist/getSchool', 'index/Regist/getSchool');
Route::post('Regist/getArticles', 'index/Regist/getArticles');
Route::get('Studytour', 'index/Studytour/index');
Route::get('showTourList', 'index/Studytour/showTourList');
Route::get('showTour', 'index/Studytour/showTour');
Route::get('Tourisms', 'index/Tourisms/index');
Route::get('showTourismList', 'index/Tourisms/showTourismList');
Route::get('showTravel', 'index/Tourisms/showTravel');
return [

];
