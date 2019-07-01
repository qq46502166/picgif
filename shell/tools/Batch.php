<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 批处理工具
 * ======================================*/


namespace shell\tools;

use app\api\uploader\Sina;
use app\api\uploader\Weibo;
use extend\Curl;
use extend\HttpClient;
use GuzzleHttp\Client;
use shell\CaijiCommon;

class Batch extends CaijiCommon
{
    public function __construct($param=[])
    {
        parent::__construct($param);
    }



    /** ------------------------------------------------------------------
     * 缩略图批量删除
     * 必须参数 $this->param[1]  缩略图规格 如 '150x150','all'表示全部规格
     * 可选参数 $this->param[2]  是否重设isdo字段  只有值为'isdo'时才重设
     * @return int
     *--------------------------------------------------------------------*/
    public function thumb_del(){
        //验证规格格式
        if(!isset($this->param[1]) || preg_match('/^(all|\d+x\d+)$/',$this->param[1])<1){
            echo '-1'.PHP_EOL;
            return -1;
        }
        $model=app('\app\admin\model\File');
        //重设isdo字段
        if(isset($this->param[2]) && $this->param[2]=='isdo')
            $model->update(['isdo'=>0]);
        $format=$this->param[1];
        //查询所有符合条件的个数
        $total=$model->count(['where'=>[['isimg','eq',1],['isdo','eq',0]]]);
        if($total==0){
            echo '-2'.PHP_EOL;
            return -2;
        }
        $j=0;
        //开始循环处理
        $this->doLoop(
            $total,
            function($perPage,$i)use($model){
                return $model->eq('isimg',1)->limit($i*$perPage,$perPage)->order('id desc')->findAll(true);
            },
            function ($item,$i)use ($format,$model,&$j){
                if($item['thumb']){
                    $allFormat=explode(',',$item['thumb']);
                    $allFormat_tmp=$allFormat;
                    foreach ($allFormat as $key => $itemFormat){
                        if($format=='all' || $format==$itemFormat){
                            //删除
                            @unlink(ROOT.$item['savepath'].'_'.$itemFormat.'.'.$item['ext']);
                            unset($allFormat_tmp[$key]);
                        }
                    }
                    $new_thumb=implode(',',$allFormat_tmp);
                    if($new_thumb!==$item['thumb']) {
                        $j++;
                        $model->eq('id', $item['id'])->update(['thumb' => $new_thumb, 'isdo' => 1]);
                        return 0;
                    }
                }
                $model->eq('id',$item['id'])->update(['isdo'=>1]);
                return 1;
            }
        );
        echo $j.PHP_EOL;
        return $j;
    }
    /** ------------------------------------------------------------------
     * 缩略图批量生成
     * 必须参数 $this->param[1]  缩略图规格 如 '150x150'
     * 可选参数 $this->param[2]  是否重设isdo字段  只有值为'isdo'时才重设
     * @return int
     *--------------------------------------------------------------------*/
    public function thumb_create(){
        //验证规格格式
        if(!isset($this->param[1]) || preg_match('/^\d+x\d+$/',$this->param[1])<1){
            echo '-1'.PHP_EOL;
            return -1;
        }
        $model=app('\app\admin\model\File');
        //重设isdo字段
        if(isset($this->param[2]) && $this->param[2]==='isdo')
            $model->update(['isdo'=>0]);
        $format=$this->param[1];
        //查询所有符合条件的个数
        $total=$model->count(['where'=>[['isimg','eq',1],['isdo','eq',0]]]);
        if($total==0){
            echo '-2'.PHP_EOL;
            return -2;
        }
        $j=0;
        //开始循环处理
        $this->doLoop(
            $total,
            function($perPage,$i)use($model){
                return $model->eq('isimg',1)->limit($i*$perPage,$perPage)->order('id desc')->findAll(true);
            },
            function ($item,$i)use ($format,$model,&$j){
                //查询是否已经有对应规格的缩略图
                if(strpos($item['thumb'],$format)===false){
                    //生成缩略图
                    $resizeClass=app('\extend\ImageResize');
                    $savepath=ROOT.$item['savepath'];
                    if($resizeClass->checkImage($savepath)){
                        list($width,$height)=explode('x',$format);
                        try{
                            $resizeClass->add()-> crop($width,$height,true)->save($savepath.'_'.$width.'x'.$height.'.jpg',IMAGETYPE_JPEG);
                            $item['thumb']=$item['thumb'] ? $item['thumb'].','.$width.'x'.$height : $width.'x'.$height;
                            //更新数据库
                            $model->eq('id',$item['id'])->update(['thumb'=>$item['thumb'],'isdo'=>1]);
                            $j++;
                            return 0;
                        }catch (\Exception $e){

                        }
                    }
                }
                $model->eq('id',$item['id'])->update(['isdo'=>1]);
                return 1;
            }
         );
        echo $j.PHP_EOL;
        return $j;
    }

    /** ------------------------------------------------------------------
     * 批量给用户添加头像
     *--------------------------------------------------------------------*/
    public function user_img(){
        $model=app('\app\portal\model\User');
        $model->update(['isdo'=>0]);
        $total=$model->count();
        if($total==0){
            echo '没有要处理的了'.PHP_EOL;
            return;
        }
        $j=0;
        $this->doLoop($total,function ($perPage,$i)use($model){
            return $model->select('id,username,avatar')->limit($perPage*$i,$perPage)->order('id desc')->findAll(true);
        },function ($item,$i)use ($model,&$j){
            if($item['avatar'])
                return 1;
            $file='/uploads/user/'.mt_rand(0,500).'.jpg';
            if($model->eq('id',$item['id'])->update(['avatar'=>$file])){
                $j++;
                echo $item['id'].'=>'.$file.PHP_EOL;
            } else
                echo $item['id'].'=>出错了'.PHP_EOL;
            return 0;
        });
        echo '总共处理了：'.$j.PHP_EOL;
    }

    //读取某个路径的文件到数据库中
    public function file2database(){
        $path='F:/caiji/av';
        $savePath='F:/caiji/file';
        $dirAll=scandir($path);
        if(!$dirAll){exit('无法读取目录'.PHP_EOL);}
        foreach ($dirAll as $i=>$item){
            if($item==='.'||$item==='..')
                continue;
            echo '开始处理文件夹：'.$item.PHP_EOL;
            $fileNames=scandir($path.'/'.$item);
            if(!$fileNames){
                echo '  无法读取文件夹：'.$item.PHP_EOL;
                continue;
            }
            $model=app('\app\admin\model\File');
            foreach ($fileNames as $fileName){
                if($fileName==='.'||$fileName==='..')
                    continue;
                if(is_dir($path.'/'.$item.'/'.$fileName))
                    continue;
                echo '处理文件：'.$fileName.' => ';
                $file=[];
                $file['uri']='/uploads/images/av-gif/'.$item.'/'.$fileName;
                $file['savepath']=$path.'/'.$item.'/'.$fileName;
                $data=$model->getFileInfo($file,false,false);
                if(!$data){
                    echo '  无法获取文件信息，提示信息：'.$model->getError().PHP_EOL;
                    continue;
                }
                if($data['md5']===''){
                    echo '  无法获取文件的md5值'.PHP_EOL;
                    continue;
                }
                //检测文件是否已经存在
                if($model->select('id')->eq('md5',$data['md5'])->find(null,true)){
                    echo '  数据库中已经存在相同的文件'.PHP_EOL;
                    echo '  md5:'.$data['md5'].PHP_EOL;
                    continue;
                }
                //入库
                $id=$model->insert($data);
                if(!$id){
                    echo '  插件数据库失败:'.PHP_EOL;
                    echo '  最后的sql语句:'.$model->getSql().PHP_EOL;
                    exit();
                }
                $new_path='/uploads/image/'.get_path_from_id($id);
                $new_uri=$new_path.'/'.$fileName;
                //重新分配保存路径
                if(!is_dir($savePath.'/'.$new_path)){
                    if(!mkdir($savePath.'/'.$new_path,0755,true)){
                        echo '  无法新建目录:'.PHP_EOL;
                        exit();
                    }
                }
                if(!is_file($data['savepath'])){
                    echo '  不是有效的文件:'.PHP_EOL;
                    exit();
                }

                if(!rename($data['savepath'],$savePath.$new_uri)){
                    echo '  无法重命名文件:'.PHP_EOL;
                    echo '  old:'.$data['savepath'].PHP_EOL;
                    echo '  new:'.$savePath.$new_uri.PHP_EOL;
                    exit();
                }
                if($model->eq('id',$id)->update([
                   'uri'=>$new_uri,
                    'savepath'=>'/public'.$new_uri
                ]))
                    echo '  成功：更新数据库';
                else{
                    echo '  失败：更新数据库'.PHP_EOL;
                    exit();
                }
                echo '............'.PHP_EOL;
                //msleep(1000,20);
            }
        }
    }

    public function upload2vim(){
        $url='https://img.vim-cn.com/';
        $where=[['isdo','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $savePath='F:/caiji/file';
        $httpClient=new HttpClient();
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            $this->model->from($table)->_where($where);
            if(isset($this->startId) && $this->startId>0)
                $this->model->gt('id',$this->startId);
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $this->model->le('id',$this->maxId);
            return $this->model->limit($perPage)->findAll(true);
        },function ($item,$key)use ($savePath,$httpClient,$url,$table){
            echo '开始处理key=>'.$key.',item=>'.$item['id'].PHP_EOL;
            //检测是否已经上传过
            $data=$this->model->from('file_item')->select('id,url')->eq('fid',$item['id'])->eq('name','vim')->eq('status',1)->find(null,true);
            if($data){
                echo '  本文件已经上传过了！'.PHP_EOL;
                $this->model->from($table)->eq('id',$item['id'])->update(['isdo'=>1]);
                return;
            }
            $data=['name'=>'image','filename'=>new \CURLFile($savePath.$item['uri'])];
            //dump($this->model->from('file_item')->findAll(true));
                //exit;
            $res=trim($httpClient->http($url,'post',$data));
            if(!$res){
                dump($data);
                dump($res);
                exit();
            }
            //检测结果
            if(preg_match('#^https?://[-A-Za-z0-9+&@\#/%?=~_|!:,.;]+[-A-Za-z0-9+&@\#/%=~_|]$#',$res)<1){
                echo '  返回的结果不正常！'.PHP_EOL;
                dump($data);
                dump($res);
                exit();
            }
            if($this->model->from('file_item')->insert([
                'fid'=>(int)$item['id'],
                'name'=>'vim',
                'url'=>$res
            ])){
                $this->model->from($table)->eq('id',$item['id'])->update([
                    'isdo'=>1
                ]);
                echo '  成功：上传成功并保存数据中！'.PHP_EOL;
            }else{
                echo '  失败：插入数据库！'.PHP_EOL;
                echo '  最后执行sql语句：'.$this->model->getSql().PHP_EOL;
                exit();
            }
            //exit();
            msleep(2000);
        });
    }

    public function checkVim(){
        $where=[['isdo1','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            $this->model->from($table)->_where($where);
            if(isset($this->startId) && $this->startId>0)
                $this->model->gt('id',$this->startId);
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $this->model->le('id',$this->maxId);
            return $this->model->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table){
            echo '开始处理key=>'.$key.',item=>'.$item['id'].'................'.PHP_EOL;
            //检测是否有vim
            $data=$this->model->from('file_item')->eq('fid',$item['id'])->eq('name','vim')->limit(100)->findAll(true);
            $data_update=[];
            if($data){
                echo '  已经上传到vim！'.PHP_EOL;
                if($item['isdo']=0)
                    $data_update['isdo']=1;
                array_shift ($data);
                if($data){
                    $in=[];
                    foreach ($data as $v){
                        $in[]=$v['id'];
                    }
                    if($this->model->from('file_item')->in('id',$in)->delete())
                        echo '  成功：删除了多余的项'.PHP_EOL;
                    else
                        echo '  成功：删除多余项时'.PHP_EOL;
                }
            }else{
                echo '  还没有上传到vim！'.PHP_EOL;
                if($item['isdo']=1)
                    $data_update['isdo']=0;
            }
            $data_update['isdo1']=1;
            if($this->model->from($table)->eq('id',$item['id'])->update($data_update))
                echo '  成功：更新file表！'.PHP_EOL;
            else{
                echo '  失败：更新file表！'.PHP_EOL;
            }
            //exit();
            //msleep(2000);
        });
    }

    public function copy2catbox(){
        $url='https://catbox.moe/user/api.php';
        $where=[['isdo1','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $savePath='F:/caiji/file';
        $httpClient=new HttpClient();
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            $this->model->from($table)->_where($where);
            if(isset($this->startId) && $this->startId>0)
                $this->model->gt('id',$this->startId);
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $this->model->le('id',$this->maxId);
            return $this->model->limit($perPage)->findAll(true);
        },function ($item,$key)use ($savePath,$httpClient,$url,$table){
            echo '开始处理key=>'.$key.',item=>'.$item['id'].'............'.PHP_EOL;
            //检测是否已经上传过
            $data=$this->model->from('file_item')->select('id,url')->eq('fid',$item['id'])->eq('name','catbox')->eq('status',1)->find(null,true);
            if($data){
                echo '  本文件已经copy过了！'.PHP_EOL;
                $this->model->from($table)->eq('id',$item['id'])->update(['isdo1'=>1]);
                return;
            }
            $data=$this->model->from('file_item')->eq('fid',$item['id'])->eq('name','vim')->eq('status',1)->find(null,true);
            if(!$data){
                echo '  还没上传到vim！'.PHP_EOL;
                dump($data);
                echo '  最后执行sql语句：'.$this->model->getSql().PHP_EOL;
                exit();
            }
            $res=trim($httpClient->http($url,'post',[
                'reqtype'=>'urlupload',
                'userhash'	=>'',
                'url'	=>$data['url'],
            ]));
            if(!$res){
                echo '  无法提交数据到目标网站！'.PHP_EOL;
                dump($res);
                exit();
            }
            //检测结果
            if(preg_match('#^https?://[-A-Za-z0-9+&@\#/%?=~_|!:,.;]+[-A-Za-z0-9+&@\#/%=~_|]$#',$res)<1){
                echo '  返回的结果不正常！'.PHP_EOL;
                dump($data);
                dump($res);
                exit();
            }
            if($this->model->from('file_item')->insert([
                'fid'=>(int)$item['id'],
                'name'=>'catbox',
                'url'=>$res
            ])){
                $this->model->from($table)->eq('id',$item['id'])->update([
                    'isdo1'=>1
                ]);
                echo '  成功：复制到目标并把结果保存到数据库中！'.PHP_EOL;
            }else{
                echo '  失败：插入数据库！'.PHP_EOL;
                echo '  最后执行sql语句：'.$this->model->getSql().PHP_EOL;
                exit();
            }
            //exit();
            //msleep(2000);
        });

    }

    public function dodo(){
        $where=[['isdo','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            $this->model->from($table)->_where($where);
            if(isset($this->startId) && $this->startId>0)
                $this->model->gt('id',$this->startId);
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $this->model->le('id',$this->maxId);
            return $this->model->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table) {
            echo '开始处理id=>' . $item['id'] . ',key=>' . $key . '………' . PHP_EOL;
            $data = [];
            $data['uri'] = str_replace('/uploads/image/', '/uploads/images/', $item['uri']);
            $data['savepath'] = str_replace('/uploads/image/', '/uploads/images/', $item['savepath']);
            $data['isdo'] = 1;
            if ($this->model->from($table)->eq('id', $item['id'])->update($data))
                echo '  成功：更新数据！' . PHP_EOL;
            else
                echo '  失败：更新数据！' . PHP_EOL;
        });
    }

    //删除某些文件
    public function delete_some(){
        $ids='6420,2027';
        $ids=explode(',',$ids);
        foreach ($ids as $id){
            $data=$this->model->from('file')->eq('id', $id)->find(null,true);
            if(!$data)
                continue;
            if($this->model->from('file_item')->eq('fid', $id)->delete())
                echo '删除file_item表'.PHP_EOL;
            if(unlink(ROOT.$data['savepath']))
                echo '删除文件'.PHP_EOL;
            if($this->model->from('file')->eq('id', $id)->delete())
                echo '删除file表'.PHP_EOL;
        }
    }

    public function check404(){
        $where=[['isdo','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $name='vim';
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            $this->model->from($table)->_where($where);
            if(isset($this->startId) && $this->startId>0)
                $this->model->gt('id',$this->startId);
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $this->model->le('id',$this->maxId);
            return $this->model->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table,$name){
            echo '开始处理,id=>'.$item['id'].',key=>'.$key.'................'.PHP_EOL;
            //检测是否有vim
            $data=$this->model->from('file_item')->eq('fid',$item['id'])->eq('name',$name)->find(null,true);
            if(!$data)
                return;
            $status=$this->head($data['url']);
            echo '  获得状态为：'.$status.PHP_EOL;
            if($status != 200){
                if($this->model->from('file_item')->eq('id',$data['id'])->update(['status'=>0]))
                    echo '  成功：更新file_item表'.PHP_EOL;
                else
                    echo '  成功：更新file_item表'.PHP_EOL;
            }
            if($this->model->from($table)->eq('id',$item['id'])->update(['isdo'=>1]))
                echo '  成功：更新file表'.PHP_EOL;
            else
                echo '  成功：更新file表'.PHP_EOL;
            //exit();
            //msleep(4000);
        });
    }

    protected function head($url){
        $curl=new Curl([
            'opt'=>[
                CURLOPT_CUSTOMREQUEST=>'HEAD',
                CURLOPT_NOBODY=>true,
                CURLOPT_HEADER=>true,
            ]
        ]);
        $curl->request($url);
        return $curl->getStatus();
    }

    public function upload(){
        $where=[['isdo','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $upload=Weibo::create('xxx','xxx');
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            $this->model->from($table)->_where($where);
            if(isset($this->startId) && $this->startId>0)
                $this->model->gt('id',$this->startId);
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $this->model->le('id',$this->maxId);
            return $this->model->limit($perPage)->findAll(true);
        },function ($item,$i)use ($upload,$table){

        });
    }

    /** ------------------------------------------------------------------
     * weiboFunc
     * @param $name
     * @param $item
     * @param Weibo $uploader
     * @return int
     *---------------------------------------------------------------------*/
    protected function weiboFunc($name,$item,$uploader){
        $data=$this->model->from('file_item')->select('id,url')->eq('fid',$item['id'])->eq('name',$name)->find(null,true);
        if($data){
            echo '  '.$name.'表中此文件已经上传过了！'.PHP_EOL;
            return 1;
        }
        $res=$uploader->upload($item['savepath'],2,true);
        if(is_int($res)){
            echo $uploader->getError($res).PHP_EOL;
            exit();
        }
        if($this->model->from('file_item')->insert([
            'fid'=>(int)$item['id'],
            'name'=>'weibo',
            'url'=>$res
        ])){
            $this->model->from('file')->eq('id',$item['id'])->update([
                'isdo'=>1
            ]);
            echo '  成功：上传并保存到数据库中！'.PHP_EOL;
        }else{
            echo '  失败：插入数据库！'.PHP_EOL;
            echo '  最后执行sql语句：'.$this->model->getSql().PHP_EOL;
            exit();
        }
        return 0;
    }

    public function upload2weibo(){
        $where=[['isdo','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $upload=Weibo::create('xxx','xxx');
        //$upload->setCookie('');
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            $this->model->from($table)->_where($where);
            if(isset($this->startId) && $this->startId>0)
                $this->model->gt('id',$this->startId);
            if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
                $this->model->le('id',$this->maxId);
            return $this->model->limit($perPage)->findAll(true);
        },function ($item,$key)use ($table,$upload){
            echo '开始处理id=>'.$item['id'].',key=>'.$key.' ……'.PHP_EOL;
            //检测是否已经上传过
            $data=$this->model->from('file_item')->select('id,url')->eq('fid',$item['id'])->eq('name','weibo')->find(null,true);
            if($data){
                echo '  本文件已经上传过了！'.PHP_EOL;
                $this->model->from($table)->eq('id',$item['id'])->update(['isdo'=>1]);
                return;
            }
            $res=$upload->upload($item['savepath'],2,true);
            if(is_int($res)){
                echo $upload->getError($res).PHP_EOL;
                exit();
            }
            if($this->model->from('file_item')->insert([
                'fid'=>(int)$item['id'],
                'name'=>'weibo',
                'url'=>$res
            ])){
                $this->model->from($table)->eq('id',$item['id'])->update([
                    'isdo'=>1
                ]);
                echo '  成功：上传并保存到数据库中！'.PHP_EOL;
            }else{
                echo '  失败：插入数据库！'.PHP_EOL;
                echo '  最后执行sql语句：'.$this->model->getSql().PHP_EOL;
                exit();
            }
            //exit();
            msleep(2000,3000);
        });

    }

    public function test(){
        //$upload=new Weibo('x','x');
        //echo $upload->getUrl('63085142gy1g4ivq9dfjig20ae05vnpe',2,true);
    }

}