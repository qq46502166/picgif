<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 任何人都可以访问的对外接口
 * ======================================*/


namespace app\api\ctrl;
use app\common\ctrl\ApiCtrl;
use core\Conf;
use GuzzleHttp\Client;

class FileCtrl extends ApiCtrl
{
    public function pic($name){
        list($id,)=explode('.',$name);
        $id=(int)$id;
        if($id<1)
            $this->noPic();
        $model=app('\app\admin\model\File');
        $data=$model->eq('isimg',1)->eq('id',$id)->find(null,true);
        if(!$data)
            $this->noPic();
        $default_engine=Conf::get('default_engine','file');
        if($default_engine==false)
            $default_engine='local';
        if($default_engine=='local'){
            header('Location: '.$data['uri']);
            return;
        }
        //$data2=$model->from('file_item')->eq('fid',$id)->eq('status',1)->eq('name','vim')->find(null,true);
        $data2=$model->from('file_item')->eq('fid',$id)->eq('name','weibo')->eq('status',1)->limit(50)->findAll(true);
        if(!$data2){
            //$this->outLocalPic($data['mime'],$data['savepath'],$data['uri']);
            header('Location: '.$data['uri']);
            return;
        }
        unset($data);
        shuffle($data2);
        header('Location: '.str_replace(['https','//ws'],['http','//s'.mt_rand(0,12)],$data2[0]['url']));
    }

    public function list(){
        $model=app('\app\admin\model\File');
        $order=get('o')?:'id';
        $perPage=get('p','int')?:20;
        $currentPage=get('c','int')?:1;
        $data=$model->eq('isimg',1)->order($order)->limit($currentPage,$perPage)->findAll(true);
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>图片展示</title></head><body>';
        foreach ($data as $item){
            echo '<p><img src="http://img.caiji.my/pic/'.$item['id'].'.'.$item['ext'].'"></p>';
            echo '<p>'.$item['id'].'</p>';
        }
        echo '</body></html>';
    }

    protected function noPic(){
        header("content-type: image/gif");
        imagegif (imagecreatefromgif(ROOT.'/public/uploads/images/no.gif'));
        exit();
    }

    protected function outLocalPic($mime,$file,$uri=''){
        switch ($mime){
            case 'image/gif':
                if($uri)
                    header('Location: '.$uri);
                header("content-type: image/gif");
                imagegif (imagecreatefromgif(ROOT.$file));
                break;
            case 'image/png':
                header("content-type: image/png");
                imagepng (imagecreatefrompng(ROOT.$file));
                break;
            case 'image/jpeg':
                header("content-type: image/jpeg");
                imagejpeg (imagecreatefromjpeg(ROOT.$file));
                break;
            case 'image/webp':
                header("content-type: image/webp");
                imagewebp (imagecreatefromwebp(ROOT.$file));
                break;
            default:
                $this->noPic();
        }

    }

    public function test(){
        $url='http://www.caiji.my/admin/login/login';
        $http=new Client([
            'verify'=>false,
        ]);
        $res=$http->get($url);
        echo time().PHP_EOL;
        dump($res->getHeaders());
        dump( $res->getHeaderLine('Set-Cookie'));
    }
}