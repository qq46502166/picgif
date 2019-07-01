<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 * QQ 46502166
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * ======================================
 * 网站地图生成工具
 * ======================================*/

namespace shell\tools;
use core\Conf;
use core\lib\cache\File;
use shell\BaseCommon;

class Sitemap extends BaseCommon
{
    protected $perPage=20000;  //每个sitemap文件储存的链接条数
    static public $api='http://data.zz.baidu.com/urls?site=www.iweixinqun.cn&token=hfKnq1AHJXdqst0o';
    protected $logFile=ROOT.'/cache/tools/sitemap.log';
    protected $isAutoSubmit=false;//是否自动提交
    protected $urls=[];


    public function __construct($param=[])
    {
        parent::__construct($param);
        $this->_setCommandOptions(['-a'=>['isAutoSubmit',true]],$this->param);
    }

    /** ------------------------------------------------------------------
     * sitemap自动生成
     *--------------------------------------------------------------------*/
    public function create(){
        $where=[['last_time','lt',time()]];
        $total=$this->model->count([
            'from'=>'sitemap',
            'where'=>$where
        ]);
        if($total<1){
            $this->outPut('Date:'.date('Y-m-d H:i:s').',没有新增加内容，所以无需生成sitemap'.PHP_EOL,true);
            return;
        }
        $this->doLoop($total,function ($perPage,$i) use ($where){
            return $this->model->from('sitemap')->_where($where)->limit($perPage)->findAll(true);
        },function ($item,$key){
            echo 'Generating table:'.$item['table_name'].'-------------------'.PHP_EOL;
            $last_id=$item['last_id'];
            $where=$item['condition'] ? json_decode($item['condition'],true):[];
            $total=$this->model->count([
                'from'=>$item['table_name'],
                'where'=>$this->getWhere($where,$last_id)
            ]);
            if($total<1){
                $this->outPut($item['table_name'].'=>Date:'.date('Y-m-d H:i:s').',没有新增加内容，无需生成sitemap'.PHP_EOL,true);
                return;
            }
            $perPage=30;
            $page=(int)ceil($total/$perPage);
            $counter=$item['counter'];
            for ($i=0;$i<$page;$i++){
                $data=$this->model->from($item['table_name'])->_where($this->getWhere($where,$last_id))->limit($perPage)->order('id')->findAll(true);
                if(!$data)
                    break;
                $last_id=$this->sitemap($data,$item['table_name'],$counter);
                if($last_id ===false)
                    return;
            }
            $this->model->from('sitemap')->eq('id',$item['id'])->update([
                'last_id'=>$last_id,
                'counter'=>$counter,
                'last_time'=>time()
            ]);
            if($this->isAutoSubmit)
                self::submitMulti($this->urls,false);
        });
    }


    /** ------------------------------------------------------------------
     * 生成查询条件
     * @param array $where
     * @param int $last_id
     * @return array
     *---------------------------------------------------------------------*/
    private function getWhere($where,$last_id){
        if($where)
            $where[]=['id','gt',$last_id];
        else
            $where=[['id','gt',$last_id]];
        return $where;
    }

    /** ------------------------------------------------------------------
     * 生成sitemap
     * @param array $data 数据
     * @param string $table 表名
     * @param int $counter 计数器
     * @return int 最后一条记录的id
     *--------------------------------------------------------------------*/
    protected function sitemap($data,$table,&$counter){
        $siteUrl=Conf::get('site_url','site');
        switch ($table){
            case 'weixinqun':
                $url=$siteUrl.'/weixinqun/{%id%}.html';
                $saveName='sitemap-weixinqun';
                break;
            case 'xuexiao':
                $url=$siteUrl.'/xuexiao/{%id%}.html';
                $saveName='sitemap-xuexiao';
                break;
            default:
                $this->outPut($table.'=>Date:'.date('Y-m-d H:i:s').',sitemap()方法中的switch里不存此table'.PHP_EOL,true);
                return false;
        }
        $sitemap='';
        foreach ($data as $item){
            $url_true=str_replace('{%id%}',$item['id'],$url);
            $sitemap.=$url_true."\n";
            if($this->isAutoSubmit)
                $this->urls[]=$url_true;
        }
        $page=ceil($counter/$this->perPage);
        $file=ROOT.'/public/'.$saveName.($page>1 ? ('-'.($page-1)):'').'.txt';
        File::write($file,$sitemap,true);
        $count=count($data);
        $counter+=$count;
        return $data[$count-1]['id'];
    }


    /** -----------------------------------------------------------------
     * 自动提交链接给搜索引擎
     * @param string|array $urls
     *      字符串时,多条用换行分隔
     *      数组时，格式 ['http://www.xxx.com/1.html','http://www.xxx.com/2.html','http://www.xxx.com/3.html']
     * @param string $api 提交的入口，百度请到百度站长工具获得入口
     * @return string
     *--------------------------------------------------------------------*/
    static public function submit($urls,$api='',&$status=true){
        if(!$api)
            $api=self::$api;
        if(is_array($urls)){
            $urls=implode("\n",$urls);
        }
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER=> 1,
            CURLOPT_FOLLOWLOCATION =>1,
            CURLOPT_TIMEOUT=>15,
            CURLOPT_CONNECTTIMEOUT=>7,
            CURLOPT_POSTFIELDS => $urls,
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            CURLOPT_HEADER=>false,
        );
        if(substr($api,0,5)=='https'){
            $options[CURLOPT_SSL_VERIFYPEER]=false;
            $options[CURLOPT_SSL_VERIFYHOST]=0;
        }
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $i=0;
        do{//失败重试三次
            $result = curl_exec($ch);
            $i++;
        }while ($result === false && $i <= 3 && sleep(1) !==false);
        if ($result===false) {
            $status=false;
            $msg= curl_error($ch);
            curl_close($ch);
            return $msg;
        }
        curl_close($ch);
        $status=true;
        return $result;
    }

    /** ------------------------------------------------------------------
     * 自动提交链接到多个搜索引擎
     * @param array|string $urls 要提交的链接
     * @param bool $isReturn 是否要捕捉返回的结果
     * @param array $apis 搜索引擎提交入口集合 格式 ['baidu'=>'https://baidu.com/xxx..','so'=>'https://so/xxx..']
     * @return array 不捕捉结果或没有设置搜索引擎提交入口时，返回空数组，否则返回对应每个搜索引擎提交后的结果集，每个搜索引擎的结果集包含下面的信息:
     * [
     *          'code'=>0  //int ,1或0, 1表示curl链接失败 0表示成功
     *          'msg'=>''   //sting, curl信息提示，失败时返回错误信息，成功输出'curl成功获取结果'
     *          'result' =>''  //string, curl访问页面返回的结果，code为1时为空字符串
     * ]
     *--------------------------------------------------------------------*/
    static public function submitMulti($urls,$isReturn=false,$apis=[]){
        //$apis=['baidu'=>'xxx.com','soso'=>'xx.ssoso.com'];
        if(!$apis)
            $apis=Conf::get('sitemap_api','site');
        $ret=[];
        if($apis){
            foreach ($apis as $key => $api){
                $res=self::submit($urls,$api,$status);
                if($isReturn){
                    if($status){
                        $ret[$key]=[
                            'result'=>$res,
                            'code'=>0,
                            'msg'=>'curl成功获取结果'
                        ];
                    } else{
                        $ret[$key]=[
                            'result'=>'',
                            'code'=>1,
                            'msg'=>$res
                        ];
                    }
                }
            }
        }
        return $ret;
    }


}