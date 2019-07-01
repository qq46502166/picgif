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


namespace shell;

use core\Conf;
use core\lib\cache\File;
use extend\Helper;

abstract class BaseCommon extends BaseShell
{
    public $isTable=true;//是否实例化数据库
    public $param;
    public $path=ROOT.'/cache';
    public $outType=2;//1为直接输出，2为重要信息保存为日志，可以传第二个参数'-e'来改变此值
    public $debug=false;
    public $runOnce=0;
    public $startId=0;
    public $maxId=0;
    protected $total=0;
    protected $model;
    protected $prefix;
    public function __construct($param=[]){
        if(!$param ){
            $this->param=['help'];
            return;
        }
        $this->param = $param;
        $this->_setCommandOptions(['-e'=>['outType',1],'-d'=>['debug',true],'-o'=>['runOnce',true],'-t'=>['isTable',false],'-s'=>['startId'],'-m'=>['maxId']],$this->param);
        parent::__construct();
        if($this->isTable)
            $this->model=app('\core\Model');
        $this->prefix=Conf::get('prefix','database');
        if(method_exists($this,'_init')) {
            $this->_init();
        }
        if(!$this->param || !isset($this->param[0])){
            $this->tipEcho();
            $this->goodbye();
            exit();
        }
        $this->outPut('Date:'.date('Y-m-d H:i:s').','.$this->param[0].' running--------------------------------'.PHP_EOL,true);
    }

    /** ------------------------------------------------------------------
     * cli模式下入口
     *--------------------------------------------------------------------*/
    public function start()
    {
        $method=$this->param[0];
        if( is_callable([$this,$method])){
            call_user_func([$this,$method]);
        }else{
            $this->tipEcho();
        }
        $this->goodbye();
    }

    private function tipEcho(){
        echo '  1)create queue useg: php cmd tools/queue create'.PHP_EOL;
        echo '  2)run queue useg: php cmd tools/queue run'.PHP_EOL;
        echo '      可选参数:   -e:设置输出种类为直接输出'.PHP_EOL;
    }

    /** ------------------------------------------------------------------
     * 输出: this->outType==2时会把重要信息写到日志里; this->outType==1时，直接输出
     * @param string $msg 信息
     * @param bool $important 是否是重要信息
     *--------------------------------------------------------------------*/
    protected function outPut($msg,$important=false){
        if($this->outType ==2 && $important){
            File::write($this->path.'.log',$msg,true);
        }elseif($this->outType ==1){
            echo $msg;
        }
    }

    /** ------------------------------------------------------------------
     * 循环处理
     * @param int $total  总条数
     * @param callable $func1
     * @param callable $func2
     *--------------------------------------------------------------------*/
    protected function doLoop($total,$func1,$func2){
        $perPage=30;
        $page=(int)ceil($total/$perPage);
        for ($i=0;$i<$page;$i++){
            $data=$this->callback($func1,[$perPage,$i]);
            if($data){
                foreach ($data as $key => $item){
                    $ret=$this->callback($func2,[$item,$key]);
                    if($ret==='break')
                        break;
                    elseif ($ret==='break all'){
                        break 2;
                    }
                }
            }else{
                break;
            }
        }
    }

    /** ------------------------------------------------------------------
     * 使用回调函数
     * @param callable $func
     * @param array $params
     * @param mixed $class_params
     * @return mixed
     *---------------------------------------------------------------------*/
    protected function callback($func,$params, ...$class_params){
        return Helper::callback($func,$params, ...$class_params);
    }

    /** ------------------------------------------------------------------
     * 判断是否条件成立，成立就退出运行关输出消息，否则不作处理
     * @param bool $var
     * @param string $msg
     *---------------------------------------------------------------------*/
    protected function dieEcho($var,$msg=''){
        if($var){
            if($msg)
                echo $msg;
            exit();
        }
    }
}