<?php


namespace Hanson\LaravelAdminQiniu;


use Encore\Admin\Form\Field\MultipleFile;

class QiniuImages extends MultipleFile
{
    protected $view = 'qiniu::qiniuimage';

    protected $rules = 'image';

    /**
     * 把 fileinput 的一些默认值修改一下
     */
    public function setupDefaultOptions()
    {
        parent::setupDefaultOptions();

        $this->options['uploadLabel'] = '上传';
        $this->options['uploadAsync'] = true; // 异步上传
        $this->options['showUpload'] = true; // 显示上传按钮
        $this->options['dropZoneEnabled'] = true; // 允许拖拽上传
        $this->options['fileActionSettings']['showRemove'] = true; // 允许单个图片删除
        $this->options['uploadExtraData']['_token'] = csrf_token();
        $this->options['deleteExtraData']['_token'] = csrf_token();
        $this->options['deleteUrl'] = '/admin/qiniu/delete';
        $this->options['uploadUrl'] = '/admin/qiniu/upload';
    }

    /**
     * js 层面逻辑修改为 hidden 传值为字符串，通过逗号分隔图片的 key
     *
     * @param string $options
     */
    protected function setupScripts($options)
    {
        $this->script = <<<EOT
$("input{$this->getElementClassSelector()}").fileinput({$options});
EOT;

        if ($this->fileActionSettings['showRemove']) {
            $text = [
                'title'   => trans('admin.delete_confirm'),
                'confirm' => trans('admin.confirm'),
                'cancel'  => trans('admin.cancel'),
            ];

            $this->script .= <<<EOT
$("input{$this->getElementClassSelector()}").on('filebeforedelete', function() {
    
    return new Promise(function(resolve, reject) {
    
        var remove = resolve;
    
        swal({
            title: "{$text['title']}",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "{$text['confirm']}",
            showLoaderOnConfirm: true,
            cancelButtonText: "{$text['cancel']}",
            preConfirm: function() {
                return new Promise(function(resolve) {
                    resolve(remove());
                });
            }
        });
    });
});
EOT;
        }

        if ($this->fileActionSettings['showDrag']) {
            $this->addVariables([
                'sortable'  => true,
                'sort_flag' => static::FILE_SORT_FLAG,
            ]);

            $this->script .= <<<EOT
$("input{$this->getElementClassSelector()}").on('filesorted', function(event, params) {
    
    var order = "";
    
    params.stack.forEach(function (item) {
        order += item.key + ","
    });
    
    $("input.qiniu_{$this->formatName($this->column)}").val(order)
});
EOT;
        }

        $keys = collect($this->options['initialPreviewConfig'] ?? [])->map(function ($item) {
            return $item['key'];
        })->implode(',');

        $this->script .= <<<EOT
$("input{$this->getElementClassSelector()}").on('fileuploaded', function(event, data, previewId, index) {
  var key = data.response.initialPreviewConfig[0].key;
    var value = $("input.qiniu_{$this->formatName($this->column)}").val()
    
    $("input.qiniu_{$this->formatName($this->column)}").val(value+","+key);
});
$("input.qiniu_{$this->formatName($this->column)}").val("{$keys}");
EOT;
    }

    /**
     * 由于修改了下面方法的 value ，这里需要重新取出 URL，去掉 key
     *
     * @return array
     */
    protected function preview()
    {
        return array_values($this->value ?? []);
    }

    /**
     * 可以设置参数定义上传与删除时的传参，控制器会获取其中的 disk 与 path 值
     *
     * @param array $data
     * @return $this
     */
    public function extraData(array $data)
    {
        $this->options['uploadExtraData'] = $data;
        $this->options['deleteExtraData'] = $data;

        return $this;
    }

    /**
     * 为了让 value 传值纯为 URL，这里需要修改 key 值 为 path
     *
     * @param null $value
     * @return $this|mixed
     */
    public function value($value = null)
    {
        if ($value === null) {
            return $this->value ?? $this->getDefault();
        }

        if (is_array($value)) {
            foreach ($value as $url) {
                $key = substr(parse_url($url)['path'], 1);
                $this->value[$key] = $url;
            }
        }

        return $this;
    }
}
