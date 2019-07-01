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


namespace shell\ctrl;

use app\api\uploader\UploadCc;
use app\api\uploader\Weibo;
use extend\Curl;
use extend\HttpClient;
use shell\CaijiCommon;

class Upload extends CaijiCommon
{
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
    protected function func1($table,$where,$perPage){
        $this->model->from($table)->_where($where);
        if(isset($this->startId) && $this->startId>0)
            $this->model->gt('id',$this->startId);
        if(isset($this->maxId) && $this->maxId>0 && $this->maxId >$this->startId )
            $this->model->le('id',$this->maxId);
        return $this->model->limit($perPage)->findAll(true);
    }

    /** ------------------------------------------------------------------
     * 检测某张图片是否已经上存到图床
     * @param string $name 图床名
     * @param array $item 图片数据
     *---------------------------------------------------------------------*/
    protected function checkFunc($name,$item){
        //检测是否存在
        $data=$this->model->from('file_item')->eq('fid',$item['id'])->eq('name',$name)->find(null,true);
        $data_update=[];
        if($data){
            echo '  已经上传到'.$name.'！'.PHP_EOL;
            if($item['isdo']=0)
                $data_update['isdo']=1;
        }else{
            echo '  还没有上传到'.$name.'！'.PHP_EOL;
            if($item['isdo']=1)
                $data_update['isdo']=0;
        }
        $data_update['isdo1']=1;
        if($this->model->from('file')->eq('id',$item['id'])->update($data_update))
            echo '  成功：更新file表！'.PHP_EOL;
        else{
            echo '  失败：更新file表！'.PHP_EOL;
        }
    }

    public function checkWeibo(){
        $where=[['isdo1','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            return $this->func1($table,$where,$perPage);
        },function ($item,$key)use ($table){
            echo '开始处理key=>'.$key.',item=>'.$item['id'].'................'.PHP_EOL;
            $this->checkFunc('weibo',$item);
            //exit();
            //msleep(2000);
        });
    }

    protected function checkResult($result,$name=''){
        switch ($name){
            case 'xxxxx':
                break;
            default:
                //检测结果
                if(preg_match('#^https?://[-A-Za-z0-9+&@\#/%?=~_|!:,.;]+[-A-Za-z0-9+&@\#/%=~_|]$#',$result)<1){
                    return false;
                }
        }
        return true;
    }

    /** ------------------------------------------------------------------
     * copyOneFile
     * @param string $name
     * @param string $from_name
     * @param array $item
     * @param string $url
     * @param HttpClient $httpClient
     * @param string $check_name
     * @param array $post_data
     * @return int
     *---------------------------------------------------------------------*/
    protected function copyOneFile($name,$from_name,$item,$url,$httpClient,$check_name,$post_data=[]){
        $data=$this->model->from('file_item')->select('id,url')->eq('fid',$item['id'])->eq('name',$name)->eq('status',1)->find(null,true);
        if($data){
            echo '  本文件已经copy过了！'.PHP_EOL;
            $this->model->from('file')->eq('id',$item['id'])->update(['isdo1'=>1]);
            return 1;
        }
        $data=$this->model->from('file_item')->eq('fid',$item['id'])->eq('name',$from_name)->find(null,true);
        if(!$data){
            echo '  还没上传到'.$from_name.'！'.PHP_EOL;
            dump($data);
            echo '  最后执行sql语句：'.$this->model->getSql().PHP_EOL;
            exit();
        }
        $post_data['url']=$data['url'];
        $res=trim($httpClient->http($url,'post',$post_data));
        if(!$res){
            echo '  无法提交数据到目标网站！'.PHP_EOL;
            dump($res);
            exit();
        }
        //检测结果
        if(!$this->checkResult($res,$check_name)){
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
            $this->model->from('file')->eq('id',$item['id'])->update([
                'isdo1'=>1
            ]);
            echo '  成功：复制到目标并把结果保存到数据库中！'.PHP_EOL;
        }else{
            echo '  失败：插入数据库！'.PHP_EOL;
            echo '  最后执行sql语句：'.$this->model->getSql().PHP_EOL;
            exit();
        }
        return 0;
    }
    /** ------------------------------------------------------------------
     * copyOneFile
     * @param string $name
     * @param string $from_name
     * @param array $item
     * @param string $url
     * @param HttpClient $httpClient
     * @param string $check_name
     * @param array $post_data
     *---------------------------------------------------------------------*/
    protected function copyFile($name,$url,$where,$from_name,$check_name,$post_data=[]){
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $httpClient=new HttpClient();
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            return $this->func1($table,$where,$perPage);
        },function ($item,$key)use ($name,$from_name,$httpClient,$url,$check_name,$post_data){
            echo '开始处理key=>'.$key.',item=>'.$item['id'].'............'.PHP_EOL;
            $this->copyOneFile($name,$from_name,$item,$url,$httpClient,$check_name,$post_data);
        });
    }

    public function copy2catbox(){
        $url='https://catbox.moe/user/api.php';
        $where=[['isdo1','eq',0]];
        $this->copyFile('catbox',$url,$where,'weibo','',[ 'reqtype'=>'urlupload', 'userhash'	=>'']);
    }

    public function dodo(){
        $where=[['isdo1','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            return $this->func1($table,$where,$perPage);
        },function ($item,$key)use ($table){
            echo '开始处理key=>'.$key.',item=>'.$item['id'].'................'.PHP_EOL;
            $name='catbox';
            $data=$this->model->from('file_item')->eq('fid',$item['id'])->eq('name',$name)->find(null,true);
            $data_update=[];
            if($data){
                echo '  已经上传到'.$name.'！'.PHP_EOL;
                if($item['isdo']=0)
                    $data_update['isdo']=1;
            }else{
                echo '  还没有上传到'.$name.'！'.PHP_EOL;
                if($item['isdo']=1)
                    $data_update['isdo']=0;
            }
            $data_update['isdo1']=1;
            if($this->model->from('file')->eq('id',$item['id'])->update($data_update))
                echo '  成功：更新file表！'.PHP_EOL;
            else{
                echo '  失败：更新file表！'.PHP_EOL;
            }
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
        $name='catbox';
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            return $this->func1($table,$where,$perPage);
        },function ($item,$key)use ($table,$name){
            echo '开始处理,id=>'.$item['id'].',key=>'.$key.'................'.PHP_EOL;
            //检测是否有vim
            $data=$this->model->from('file_item')->eq('fid',$item['id'])->eq('name',$name)->find(null,true);
            $data_update=[];
            if(!$data){
                //$this->model->from('file')->eq('id',$item['id'])->update(['isdo'=>1,'isdo1'=>0]);
                //return;
                $data_update['isdo1']=0;
            }else{
                /*$status=$this->head($data['url']);
                echo '  获得状态为：'.$status.PHP_EOL;
                if($status != 200){
                    $data_update['isdo1']=0;
                    if($this->model->from('file_item')->eq('id',$data['id'])->update(['status'=>0]))
                        echo '  成功：更新file_item表'.PHP_EOL;
                    else
                        echo '  成功：更新file_item表'.PHP_EOL;
                }*/
                if(preg_match('/\.gif$/',$data['url'])==0){
                    $data_update['isdo1']=0;
                    if($this->model->from('file_item')->eq('id',$data['id'])->delete())
                        echo '  成功：删除file_itme表'.PHP_EOL;
                    else
                        echo '  失败：删除file_itme表'.PHP_EOL;
                }
            }
            $data_update['isdo']=1;
            if($this->model->from($table)->eq('id',$item['id'])->update($data_update))
                echo '  成功：更新file表'.PHP_EOL;
            else{
                echo '  失败：更新file表'.PHP_EOL;
                exit();
            }
            //exit();
            //msleep(1000);
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

    /** ------------------------------------------------------------------
     * uploadOne
     * @param $name
     * @param $item
     * @param  Weibo|UploadCc $uploader
     * @return int
     *---------------------------------------------------------------------*/
    protected function uploadOne($name,$item,$uploader){
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
        //$this->model->from('file_item')->insert([
        //            'fid'=>(int)$item['id'],
        //            'name'=>$name,
        //            'url'=>$res
        //        ])
        if($this->model->_exec('REPLACE INTO '.$this->prefix.'file_item (`fid`,`name`,`url`) VALUES (?,?,?)',[(int)$item['id'],$name,$res],false)){
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
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            return $this->func1($table,$where,$perPage);
        },function ($item,$key)use ($table,$upload){
            echo '开始处理id=>'.$item['id'].',key=>'.$key.' ……'.PHP_EOL;
            $this->uploadOne('weibo',$item,$upload);
            //exit();
            //msleep(2000,3000);
        });

    }

    public function upload2uploadcc(){
        $where=[['isdo','eq',0]];
        $table='file';
        $total=$this->model->count(['from'=>$table,'where'=>$where]);
        $upload=UploadCc::create();
        $this->doLoop($total,function ($perPage,$i)use ($where,$table){
            return $this->func1($table,$where,$perPage);
        },function ($item,$key)use ($table,$upload){
            echo '开始处理id=>'.$item['id'].',key=>'.$key.' ……'.PHP_EOL;
            $this->uploadOne('uploadcc',$item,$upload);
            //exit();
            //msleep(2000,3000);
        });
    }


}