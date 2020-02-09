<?php


namespace Hanson\LaravelAdminQiniu;


use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class QiniuServiceProvider extends ServiceProvider
{
    public function boot(Qiniu $extension)
    {
        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'qiniu');
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        Admin::booting(function () {
            Form::extend('qiniuImages', QiniuImages::class);
        });
    }
}
