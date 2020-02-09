<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => 'Hanson\\LaravelAdminQiniu\\Http\\Controllers',
    'middleware'    => config('admin.route.middleware'),
], function () {

    Route::post('qiniu/upload', 'QiniuController@upload');
    Route::match(['put', 'post'], 'qiniu/delete', 'QiniuController@delete');

});
