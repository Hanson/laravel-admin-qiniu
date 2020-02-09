# laravel-admin-qiniu

Laravel admin 框架的七牛 qiniu 多图上传扩展，可拖拽，异步上传图片，支持删除

![1_7M_G0VFANP6HK48EEL2QO.png](https://i.loli.net/2020/02/09/Hys9IGfjWloc8Fm.png)

![__FP8P8`VX`LN_Y3__4K762.png](https://i.loli.net/2020/02/09/hMFqysDLK4vZaOx.png)

## 安装

`composer require hanson/laravel-admin-qiniu:dev-master -vvv`

## 配置

在 `config/filesystems.php` 增加一个 disk

```php
<?php

return [
   'disks' => [
        //...
        'qiniu' => [
           'driver'     => 'qiniu',
           'access_key' => env('QINIU_ACCESS_KEY', 'xxxxxxxxxxxxxxxx'),
           'secret_key' => env('QINIU_SECRET_KEY', 'xxxxxxxxxxxxxxxx'),
           'bucket'     => env('QINIU_BUCKET', 'xxx'),
           'domain'     => env('QINIU_DOMAIN', 'xxx.clouddn.com'), // or host: https://xxxx.clouddn.com
        ],
        //...
    ]
];
```

## 使用

```php
<?php

$form = new \Encore\Admin\Form(new Goods);

$form->qiniuImages('column', '商品图')->sortable(); // 普通用法

$form->qiniuImages('column', '商品图')
    ->sortable() // 让图片可以拖拽排序
    ->extraData(['disk' => 'qiniu2', 'path' => 'avatar']) // 假如你有多个七牛配置，可以通过指定此处的 disk 进行上传， path 为文件路径的前缀
    ->value(['http://url.com/a.jpg', 'http://url.com/b.jpg']); // 默认显示的图片数组，必须为 url

$form->saving(function (\Encore\Admin\Form $form) {
    $paths = \Hanson\LaravelAdminQiniu\Qiniu::getPaths(request('qiniu_column')); // 需要 qiniu_ 作为前缀的字段
});
```
