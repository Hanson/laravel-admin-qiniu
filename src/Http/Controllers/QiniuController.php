<?php


namespace Hanson\LaravelAdminQiniu\Http\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QiniuController extends Controller
{
    public function upload()
    {
        $file = request()->file();

        if (!$file) {
            return ['error' => '没有需要上传的图片'];
        }

        $file = array_values($file)[0][0];

        $disk = request('disk', 'qiniu');

        $domain = config('filesystems.disks.'.$disk.'.domain');

        $domain = Str::endsWith($domain, '/') ? $domain : $domain . '/';

        try {
            $path = Storage::disk($disk)->put(request('path', ''), $file);
        } catch (\Exception $exception) {
            return ['error' => '网络错误，错误信息：'.$exception->getMessage()];
        }

        return [
            'initialPreview' => [
                "$domain$path"
            ],
            'initialPreviewConfig' => [
                ['caption' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'width' => '120px', 'url' => "/admin/qiniu/delete", 'key' => $path],
            ],
            'append' => true // 是否把这些配置加入`initialPreview`。
        ];
    }

    public function delete()
    {
        return ['has_delete' => Storage::disk(request('disk'))->delete(request('key'))];
    }
}
