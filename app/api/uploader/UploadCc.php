<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 *
 * ======================================*/


namespace app\api\uploader;

use core\lib\cache\File;
use extend\Helper;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Response;

class UploadCc
{
    public $uploadUrl='https://upload.cc/image_upload';
    protected $error=[
        1=>'微博要求图片最大为20M，你上传的图片超过了20M!',
        2=>'上传的文件格式不对，微博只能上传图片',
        3=>'http获取结果失败',
        4=>'返回的结果格式不对',
        5=>'无法获取文件信息',
    ];
    protected $http;

    /**
     * Upload constructor.
     */
    protected function __construct()
    {

    }

    /** ------------------------------------------------------------------
     * 实例化一个Weibo类，唯一实例化方法
     * @return UploadCc
     *---------------------------------------------------------------------*/
    static public function create(){
        return new UploadCc();
    }

    /**
     * 上传图片到微博图床
     * @param string $uploadFilePath 相对于ROOT的文件地址
     * @param string $size 图片尺寸 0-7(数字越大尺寸越小)分别对应： 'large', 'mw1024', 'mw690', 'bmiddle', 'small', 'thumb180', 'thumbnail', 'square'
     * @param bool $https 是否为https
     * @return int|string int为不正常，string为正常
     */
    public function upload($uploadFilePath, $size=0,$https=true){
        $fileInfo=File::getFileInfo(ROOT.$uploadFilePath);
        if(!$fileInfo){//文件不存在
            return 5;
        }
        if( $fileInfo['size'] > 20*1024*1024){
            $this->error[1].='当前文件大小为：'.round($fileInfo['size']/1024/1024,2).'M';
            return 1;
        }
        if($fileInfo['isimg'] !== 1){
            $this->error[2].='当前文件mime：'.$fileInfo['mime'];
            return 2;
        }
        try{
            //实例化GuzzleHttp
            $client= new Client([
                'base_uri' => $this->uploadUrl,
                'timeout'  => 100.0,
                'verify'=>false
            ]);
            $response = $client->request('POST', '', [
                'multipart' => [
                    [
                        'name' => 'uploaded_file[]',
                        'contents' => fopen(ROOT.$uploadFilePath, 'rb')
                    ],
                ]
            ]);
            $res = $response->getBody()->getContents();
        }catch (\GuzzleHttp\Exception\GuzzleException $exception){
            echo 'GuzzleHttp出错了：'.$exception->getMessage().PHP_EOL;
            exit();
        }
        if(!$res){
            dump($res);
            return 3;
        }
        $url=$this->getUrl($res);
        if(!$url){
            dump($res);
            dump($url);
            return 4;
        }
        return $url;
    }

    /** ------------------------------------------------------------------
     * 读取错误信息
     * @param int $code
     * @return mixed|string
     *--------------------------------------------------------------------*/
    public function getError($code){
        return $this->error[$code] ?? '';
    }

    /** ------------------------------------------------------------------
     * checkResult
     * @param string $res
     * @return bool|string
     *---------------------------------------------------------------------*/
    protected function getUrl($res){
        $arr=json_decode($res,true);
        if($arr && isset($arr['success_image'][0]['url']))
            return 'https://upload.cc/'.$arr['success_image'][0]['url'];
        return false;
    }

}