<?php

namespace Poppy\System\Http\Request\ApiV1\Web;

use Illuminate\Http\UploadedFile;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Helper\UtilHelper;
use Poppy\System\Classes\Contracts\UploadContract;
use Request;
use Throwable;
use Validator;

/**
 * 图片处理控制器
 */
class UploadController extends WebApiController
{

    /**
     * @api                 {post} api_v1/system/upload/image 图片上传
     * @apiDescription      图片上传
     * @apiVersion          1.0.0
     * @apiName             SystemUploadImage
     * @apiGroup            System
     * @apiParam   {string} image         图片内容(支持多张/单张上传)
     * @apiParam   {string} [type]        上传图片的类型(form,base64) [form|表单(默认)]
     * @apiParam   {string} [image_type]  图片图片存储类型(default)默认default
     */
    public function image()
    {
        $type       = input('type', 'form');
        $image_type = input('image_type', 'default');

        $all               = Request::all();
        $all['image_type'] = $image_type ?: 'default';
        $all['type']       = $type;

        if (!isset($all['image']) || !$all['image']) {
            return Resp::web(Resp::ERROR, '图片内容必须');
        }

        $validator = Validator::make($all, [
            'type' => 'required|in:form,base64',
        ], [], [
            'type' => '上传图片的类型',
        ]);
        if ($validator->fails()) {
            return Resp::web(Resp::ERROR, $validator->messages());
        }

        if (sys_is_demo()) {
            return $this->demo();
        }

        $Image = app(UploadContract::class);
        $Image->setFolder($image_type);

        /* 图片上传大小限制,过大则需要手都进行缩放
         * ---------------------------------------- */
        $district = config('poppy.system.upload_image_district');
        if (isset($district[$image_type]) && (int) $district[$image_type] > 0) {
            $Image->setResizeDistrict((int) $district[$image_type]);
        }

        $urls = [];
        if ($type === 'form') {
            $Image->setExtension(['jpg', 'png', 'gif', 'jpeg', 'bmp', 'mp4', 'rm', 'rmvb', 'wmv']);
            $image = Request::file('image');
            if (!is_array($image)) {
                $image = [$image];
            }

            foreach ($image as $_img) {

                if ($_img instanceof UploadedFile) {
                    $mime_info = $_img->getMimeType();

                    $slashes_index = strpos($mime_info, '/');
                    $mime_type     = substr($mime_info, $slashes_index + 1);

                    $Image->setMimeType($mime_type);
                }

                if ($Image->saveFile($_img)) {
                    $urls[] = $Image->getUrl();
                }
            }
        }
        elseif ($type === 'base64') {
            $image = input('image');

            if (!is_array($image) && UtilHelper::isJson($image)) {
                $image = json_decode($image, true);
            }
            if (!is_array($image)) {
                $image = [$image];
            }
            $Image->setQuality(85);
            foreach ($image as $_img) {
                $data = array_filter(explode(',', $_img));
                if (count($data) >= 2) {
                    [$mime_info, $_img] = $data;

                    $slashes_index   = strpos($mime_info, '/');
                    $semicolon_index = strpos($mime_info, ';');

                    $length    = $semicolon_index - $slashes_index - 1;
                    $mime_type = substr($mime_info, $slashes_index + 1, $length);
                    $Image->setMimeType($mime_type);
                }
                else if (count($data) === 1) {
                    $_img = $data[0];
                    $Image->setMimeType('');
                }
                else {
                    continue;
                }

                $content = base64_decode($_img);
                try {
                    if ($Image->saveInput($content)) {
                        $urls[] = $Image->getUrl();
                    }
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        // 上传图
        if (count($urls)) {
            return Resp::web(Resp::SUCCESS, '上传成功', [
                'url' => $urls,
            ]);
        }

        return Resp::web(Resp::ERROR, $Image->getError());
    }

    /**
     * @api                 {post} api_v1/system/upload/file 文件上传
     * @apiDescription      上传文件, 这里的文件上传支持音视频, 不支持图片
     * @apiVersion          1.0.0
     * @apiName             SystemUploadFile
     * @apiGroup            System
     * @apiParam   {string} file        内容
     * @apiParam   {string} type        上传类型[audio|音频;video|视频;images|图片]
     */
    public function file()
    {
        $type = input('type', 'audio');

        $input = input();

        $validator = Validator::make($input, [
            'file' => 'required',
            'type' => 'required|in:audio,video,images,file',
        ], [], [
            'file' => '上传文件',
            'type' => '类型',
        ]);
        if ($validator->fails()) {
            return Resp::web(Resp::ERROR, $validator->messages());
        }

        if (sys_is_demo()) {
            return $this->demo();
        }

        $Uploader = app(UploadContract::class);
        $Uploader->setFolder($type);

        $urls = [];
        if ($type === 'video') {
            $Uploader->setExtension(['mp4', 'rm', 'rmvb', 'wmv']);
        }
        if ($type === 'audio') {
            $Uploader->setExtension(['mp3', 'm4a', 'wav', 'aac']);
        }
        if ($type === 'file') {
            $Uploader->setExtension(['zip', 'svga']);
        }
        if ($type === 'images') {
            $Uploader->setExtension(['jpg', 'png', 'gif', 'jpeg', 'bmp']);
            $Uploader->setResizeDistrict(1440);
        }
        $file = Request::file('file');
        if (!is_array($file)) {
            $file = [$file];
        }

        foreach ($file as $_file) {
            if ($Uploader->saveFile($_file)) {
                $urls[] = $Uploader->getUrl();
            }
        }

        // 上传图
        if (count($urls)) {
            return Resp::success('上传成功', [
                'url' => $urls,
            ]);
        }

        return Resp::web(Resp::ERROR, $Uploader->getError());
    }


    private function demo()
    {
        return Resp::web(Resp::SUCCESS, '上传成功', [
            'url' => [
                'https://oss.wulicode.com/demo/480x640/0' . random_int(0, 6) . '.jpg',
            ],
        ]);
    }
}