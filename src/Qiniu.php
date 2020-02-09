<?php


namespace Hanson\LaravelAdminQiniu;


use Encore\Admin\Extension;

class Qiniu extends Extension
{
    public $views = __DIR__ . '/../resources/views';

    public $assets = __DIR__ . '/../resources/assets';

    public static function getPaths($request)
    {
        return array_filter(explode(',', $request));
    }
}
