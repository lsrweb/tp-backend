<?php

use think\facade\Route;

Route::group('upload', function () {
    Route::post('file_local', 'glob/Upload/uploadLocal');
    Route::post('file_qiniu', 'glob/Upload/uploadQN');
    Route::post('file_delete', 'glob/Upload/deleteQN');
    Route::get('file_list', 'glob/Upload/listQN');
    Route::put('file_update', 'glob/Upload/updateQN');

    Route::post('upload_xls', 'glob/Upload/uploadXls');
});

Route::group('auth', function () {
    Route::post('login', 'glob/Auth/login');
});

Route::get('captcha', 'glob/Captchas/generate');

Route::group('email', function () {
    Route::post("send",'app\glob\controller\Email@sendCodeToEmail');
});
