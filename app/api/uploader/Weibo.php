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

class Weibo
{
    //cookie缓存文件名
    const COOKIE_CACHE_FILE = ROOT . '/cache/uploader/weiboCookie.txt';
    //微信图片提交接口url地址
    public $uploadUrl='http://picupload.service.weibo.com/interface/pic_upload.php?mime=image%2Fjpeg&data=base64&url=0&markpos=1&logo=&nick=0&marks=1&app=miniblog&cb=http://weibo.com/aj/static/upimgback.html?_wv=5&callback=STK_ijax_';
    //微信用户名
    public $username;
    //微博密码
    public $password;
    //public $user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:67.0) Gecko/20100101 Firefox/67.0';
    //public $user_agent='Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Mobile Safari/537.36';
    public $cookie;
    protected $error=[
        1=>'微博要求图片最大为20M，你上传的图片超过了20M!',
        2=>'上传的文件格式不对，微博只能上传图片',
        3=>'http获取结果失败',
        4=>'返回的结果格式不对',
        5=>'无法获取文件信息',
    ];
    protected $time;

    /**
     * Upload constructor.
     * @param string $username 微博登陆用户名
     * @param string $username 微博登陆密码
     */
    protected function __construct($username,$password)
    {
        $this->username = $username;
        $this->password = $password;
        //http://picupload.service.weibo.com/interface/pic_upload.php?rotate=0&app=miniblog&s=xml&mime=image/jpeg&data=1&wm=
        //http://picupload.weibo.com/interface/pic_upload.php?cb=https%3A%2F%2Fweibo.com%2Faj%2Fstatic%2Fupimgback.html%3F_wv%3D5%26callback%3DSTK_ijax_1551096206285100&mime=image%2Fjpeg&data=base64&url=weibo.com%2Fu%2F5734329255&markpos=1&logo=1&nick=&marks=0&app=miniblog&s=rdxt&pri=0&file_source=2
        //获取上传用的cookie(微博图床非公共接口，需要模拟登录取得cookie后，再模拟网页上传)
        //$this->getCookie();
    }

    /** ------------------------------------------------------------------
     * 实例化一个Weibo类，唯一实例化方法
     * @param string $username
     * @param string $password
     * @return Weibo|bool
     *---------------------------------------------------------------------*/
    static public function create($username='',$password=''){
        $weibo=new Weibo($username,$password);
        if($weibo->getCookie())
            return $weibo;
        else
            return false;
    }

    /**
     * 获取微博上传用cookie(登录后在header返回的)
     * @return bool
     */
    public function getCookie(){
        //据说微博cookie过期时间为1天，我就设置1天-50秒的秒数
        $expires = 86350;
        //如果文件存在，且未过期，且文件里有内容，则使用文件缓存的token
        if(File::checkFile(self::COOKIE_CACHE_FILE,$expires) && ($cookie=file_get_contents(self::COOKIE_CACHE_FILE)) !=''){
            $this->cookie=json_decode($cookie, true);
            $this->time=filemtime(self::COOKIE_CACHE_FILE);
            return true;
        }
        //循环登陆
        $i=1;
        do{
            if($i>1)
                msleep(8000,400);
            echo '开始第'.$i.'次模拟登陆'.PHP_EOL;
            $i++;
            $isLogin=$this->weiboLogin();
        }while(!$isLogin && $i<10);
        if($isLogin)
            echo '  成功：模拟登陆！'.PHP_EOL;
        else
            echo '  失败：模拟登陆！'.PHP_EOL;
        return $isLogin;
    }

    /**
     * 模拟微博登录，用于获取返回的cookie
     * 注：保存cookie的文件必须已经创建
     * @return bool
     */
    public function weiboLogin(){
        $loginUrl = 'https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.15)&_='.time();
        $loginData = [
            'entry' => 'sso',
            'gateway' => '1',
            'from' => 'null',
            'savestate' => '30',
            'useticket' => '0',
            'vsnf' => '1',
            'su' => base64_encode($this->username),
            'service' => 'sso',
            'sp' => $this->password,
            'sr' => '1920*1080',
            'encoding' => 'UTF-8',
            'cdult' => '3',
            'domain' => 'sina.com.cn',
            'prelt' => '0',
            'returntype' => 'TEXT',
        ];
        try {
            //实例化GuzzleHttp
            $client = new Client([
                'base_uri' => $loginUrl,
                'timeout'  => 10.0,
                'verify'=>false
            ]);
            $response = $client->request('POST', '', [
                'form_params' => $loginData,
            ]);
            $res = $response->getHeaderLine('Set-Cookie');
            if(!$res)
                return false;
            $cookie=$this->getCookieFromHeader($res);
            if($cookie){
                File::write(self::COOKIE_CACHE_FILE,json_encode($cookie),false);
                $this->cookie=$cookie;
                return true;
            }else{
                $string = $response->getBody()->getContents();
                echo '登陆失败: '.$string.PHP_EOL;
                return false;
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $exception){
            echo 'GuzzleHttp访问出错：'.$exception->getMessage().PHP_EOL;
            return false;
        }
    }

    public function checkIsLogin(){
        $loginUrl = 'https://photo.weibo.com/1661489474/photos';
        try {
            //实例化GuzzleHttp
            $client = new Client([
                'base_uri' => $loginUrl,
                'timeout' => 10.0,
                'verify' => false,
            ]);
            $response = $client->request('get', '', [
                'cookies' => CookieJar::fromArray($this->cookie, 'weibo.com'),
            ]);
            $html = (string)$response->getBody();
            return (strpos($html, 'charset=utf-8"') !== false);
        } catch (\GuzzleHttp\Exception\GuzzleException $exception){
            echo 'GuzzleHttp访问出错：'.$exception->getMessage().PHP_EOL;
            return false;
        }
    }

    /**
     * 获取微博图床的图片链接 (参考：http://blog.kkksos.com/2018/09/21/12.html 的函数)
     * @param string $pid 微博图床每个图片唯一标识符，必须是已经验证过格式的
     * @param int $size 图片尺寸 0-7(数字越大尺寸越小)分别对应： 'large', 'mw1024', 'mw690', 'bmiddle', 'small', 'thumb180', 'thumbnail', 'square'
     * @param bool $https (true) 是否使用 https 协议
     * @return string 图片链接 当 $pid 既不是 pid 也不是合法的微博图床链接时返回空值
     */
    public function getUrl($pid, $size=0, $https=true)
    {
        $sizeArr=['large', 'mw1024', 'mw690', 'bmiddle', 'small', 'thumb180', 'thumbnail', 'square'];
        //判断是不是图片唯一标识符
        //if (preg_match('/^[a-zA-Z0-9]{32}$/', $pid) === 1) {
            return ($https ? 'https' : 'http') . '://' . ($https ? 'ws' : 'ww')
                . ((crc32($pid) & 3) + 1) . ".sinaimg.cn/" . $sizeArr[$size]
                . "/$pid." . ($pid[21] === 'g' ? 'gif' : 'jpg');
        //}
        //图片链接的时候
       /* $imgUrl = preg_replace_callback('/^(https?:\/\/[a-z]{2}\d\.sinaimg\.cn\/)' . '(large|bmiddle|mw1024|mw690|small|square|thumb180|thumbnail)' . '(\/[a-z0-9]{32}\.(jpg|gif))$/i', function ($match) use ($size,$sizeArr) {
            return $match[1] . $sizeArr[$size] . $match[3];
        }, $pid, -1, $count);
        if ($count === 0) {
            return '';
        }
        return $imgUrl;*/
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
        $ff['savepath']=ROOT.$uploadFilePath;
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
            $client = new Client([
                'base_uri' => $this->uploadUrl.time(),
                'timeout'  => 10.0,
            ]);
            $response = $client->request('POST', '', [
                'cookies'=>CookieJar::fromArray($this->cookie,'picupload.service.weibo.com'),
                'multipart' => [
                    [
                        'name' => 'pic1',
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
        $pid=Helper::strCut($res,'"pid":"','"}',false);
        if(!$pid || preg_match('/^[a-zA-Z0-9]{32}$/', $pid) !== 1){
            dump($res);
            dump($pid);
            return 4;
        }
        //1小时更新一次cookie
        /*if(time()-$this->time > 88888600){
            $cookie=$this->getCookieFromHeader($response->getHeaderLine('Set-Cookie'));
            if($this->setCookie($cookie))
                echo '  成功：重设cookie'.PHP_EOL;
            else
                echo '  失败：重设cookie'.PHP_EOL;
        }*/
        return $this->getUrl($pid,$size,$https);
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
     * 重新设置cookie
     * @param string $cookie
     * @return bool
     *---------------------------------------------------------------------*/
    public function setCookie($cookie){
        if(!is_array($cookie))
            $cookie=$this->cookie2arr($cookie);
        if(!$cookie)
            return false;
        $this->cookie=$cookie;
        File::write(self::COOKIE_CACHE_FILE,json_encode($cookie),false);
        return true;
    }

    /** ------------------------------------------------------------------
     * 字符串格式的cookie转化为数组形式
     * @param string $cookie header格式的cookie
     * @return array|bool
     *---------------------------------------------------------------------*/
    public function cookie2arr($cookie=''){
        if(!$cookie)
            return false;
        $cookieArr=explode(';',$cookie);
        $res=[];
        foreach ($cookieArr as $item){
            if(!$item)
                continue;
            list($k,$v)=explode('=',$item);
            $res[$k]=$v;
        }
        return $res;
    }

    /** ------------------------------------------------------------------
     * 从网页内容的header中读取cookie
     * @param string $cookie_string
     * @return array|bool 符合格式的cookie就返回数组化的cookie,否则返回false
     *---------------------------------------------------------------------*/
    protected function getCookieFromHeader($cookie_string){
        if(preg_match('/; SUB=([^;]+);/', $cookie_string)>0){
            return $this->cookie2arr($cookie_string);
        }
        return false;
    }

}