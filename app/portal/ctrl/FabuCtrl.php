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


namespace app\portal\ctrl;
use app\common\ctrl\ApiCtrl;
use extend\Download;

/**
 * Class FabuCtrl：入口start() 1个方法
 * @package app\portal\ctrl
 */
class FabuCtrl extends ApiCtrl
{
    protected $post=[];
    /** ------------------------------------------------------------------
     * 自动发布入口
     *---------------------------------------------------------------------*/
    public function start(){
        if(!$this->checkPermissions())
            die('权限不足!');
        /*if(($is_cate=get('is_cate','int',0))==1){
            //$cateModel=app('\app\portal\model\PortalCategory');
            //echo $cateModel->getTreeSelect();
            exit();
        }*/
        if(!isset($_POST) || !$_POST ) die('无权访问!');
        $data=$this->getPost(['from_id','title','content','category','thumb','category_id','create_time','type']);
        //不能为空项-------------------------
        if(!$data['title']) die('标题不能为空');
        //if(!$data['content']) die('内容不能为空');
        //默认项--------------------------------
        $data['from_id']=(int)$data['from_id'];
        if($data['from_id']>0){
            if(app('\core\Model')->from('portal_post')->select('id')->eq('from_id',$data['from_id'])->find(null,true))
                die('发布成功');
        }else{
            $data['from_id']=0;
        }
        //种类
        if(!$data['type']){
            $data['type']=3;
        }
        //分类
        if(!$data['category_id']){
            $data['category_id']=isset($data['category'])? $this->getCategoryId($data['category'],$data['type'],1) : 1;
        }
        //创建时间
        $data['create_time']=$this->getDate($data['create_time']);
        $data['update_time']=$data['create_time'];
        //用户id
        $data['uid']=2;
        //计数
        $data['views']=mt_rand(1050,9500);
        $data['downloads']=mt_rand(25,95);
        $data['likes']=mt_rand(5,25);
        //标签
        $tag=$data['tag'] ?? '';
        //数据处理并入库
        $id=$this->handler($data);
        if($id > 0){
            //添加tag
            if($tag){
                $type=[1=>'article',2=>'soft',3=>'goods'];
                app('\app\admin\model\Tag')->addFromOid($id,$tag,'portal_'.$type[$data['type']],0);
            }
            echo '发布成功';
            //后续处理
            if($this->post){//有图片就下载图片
                $cmd='php '.ROOT.'/cmd tools/download portalImg '.str_replace('&','@',http_build_query($this->post)).' >'.ROOT.'/cache/1.txt 2>&1';
                system($cmd);
            }
        }else{
            echo '入库失败，原因未明';
        }
    }

    /** ------------------------------------------------------------------
     * 自动处理$_POST数据：去掉两头的空白，并对必要的字段进行初始化即如果没有设置就设置为空
     * @param array $require
     * @return array
     *---------------------------------------------------------------------*/
    protected function getPost($require=[]){
        $data=[];
        if($_POST){
            array_walk($_POST,function(&$v,$k){$v=trim($v);});
            $data=$_POST;
            unset($_POST);
            //删除id字段
            if(isset($data['id']))
                unset($data['id']);
            foreach ($require as $v){
                $data[$v]=$data[$v] ?? '';
            }
        }
        return $data;
    }

    /** ------------------------------------------------------------------
     * 数据处理器
     * @param array $data
     * @return int 成功返回新插入数据的id，否则返回0
     *--------------------------------------------------------------------*/
    protected function handler($data){
        //内链添加
        $this->keywordLink($data['content']);
        //缩略图与内容图片处理
        $data['more']=$this->image($data['content'],$data['thumb']);
        $id=(int) $this->add($data,'portal_post');
        if($id <1)
            return 0;
        if($data['more']){
            $this->post['z']=1;
        }
        if($this->post)
            $this->post['id']=$id;
        return $id;
    }

    /** ------------------------------------------------------------------
     * 获取发贴时间
     * @param string $strDate
     * @return int
     *--------------------------------------------------------------------*/
    protected function getDate($strDate){
        $date=strtotime($strDate);
        if($date!==false)
            return $date;
        //$mode=app('\app\portal\model\PortalPost');
        $model=app('\core\Model');
        $res=$model->from('portal_post')->select('create_time')->order('id desc')->find(null,true);
        if($res){
            if($res['create_time']>0){
                return $res['create_time']+mt_rand(1100,4800);
            }
        }
        return time();
    }

    /** ------------------------------------------------------------------
     * 获取用户id : 用户名为空时取随机用户，用户名不存时自动添加用户
     * @param $username
     * @return int
     *---------------------------------------------------------------------*/
    protected function getUserId(&$username){
        $model=app('\app\portal\model\User');
        //用户名为空时 取随机用户
        if(!$username){
            $data=$model->getRandomUser(1,'id,username');
            if($data){
                $username=$data['0']['username'];
                return $data[0]['id'];
            }
            return 0;
        }
        return $model->addFromName($username);
    }

    /** ------------------------------------------------------------------
     * 获取分类id
     * @param string $name 分类名
     * @param int $type : 分类种类
     * @param int $default 出错时默认返回种类
     * @return int
     *--------------------------------------------------------------------*/
    protected function getCategoryId($name,$type,$default){
        $category_id=app('\app\portal\model\PortalCategory')->setType($type)->getIdByName($name,true);
        return $category_id >0 ? $category_id : $default;
    }

    /** ------------------------------------------------------------------
     * 下载图片的整理
     * @param string $content 内容，会替换内容中的图片为占位符
     * @param string $tu 缩略图
     * @return string json格式化的字符串
     *--------------------------------------------------------------------*/
    protected function image(&$content,$tu){
        /*if($tu){
            $arr=explode('{%|||%}',$tu);
            foreach ($arr as $item){
                $content.='<p><img src="'.$item.'" alt="'.$title.'"></p>';
            }
        }*/
        $download=new Download();
        $content=$download->addImg('content',$content);
        //$download->dump();
        if($tu){
            $download->addThumb($tu);
        }
        //$download->dump();
        return $download->all();
    }

    /** ------------------------------------------------------------------
     * 数据入库
     * @param array $data 数据
     * @param string $table 表名
     * @return bool|int
     *--------------------------------------------------------------------*/
    protected function add($data,$table){
        $model=app('\core\Model');
        $model->table=$table;
        $data=$model->_filterData($data);
        return $model->insert($data);
    }

    /** ------------------------------------------------------------------
     * 站内描文本添加
     * @param string $content
     * @return int 返回发生替换的次数
     *--------------------------------------------------------------------*/
    protected function keywordLink(&$content){
        //选去掉用户名和发布时间  时间{%||%}用户名{%||%}内容{%|||%}时间{%||%}用户名{%||%}内容{%|||%}
        $i=-1;
        $user_filter=[];
        $content=preg_replace_callback('#\{%\|\|%\}.*?\{%\|\|%\}#' ,function ($mat) use (&$i,&$user_replace,&$user_filter){
            $i++;
            $user_replace[$i]=$mat[0];
            $user_filter[$i]='{%user_filter_'.$i.'%}';
            return $user_filter[$i];
        },$content);
        $model=app('\app\admin\model\KeywordLink');
        $count=$model->doLoop($content);
        if($i>-1){
            $content=str_replace($user_filter,$user_replace,$content);
        }
        return $count;
    }

    protected function checkPermissions(){
        return (trim(get('pwd')) === 'Djidksl$$EER4ds58cmO');
    }

}