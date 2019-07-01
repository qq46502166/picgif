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


namespace shell\tools;

use shell\BaseCommon;

class Auto extends BaseCommon
{
    protected $task=[
        ['name'=>'学校发布','class'=>'\shell\caiji\Xuexiao','method'=>'fabu','class_param'=>[],'method_param'=>''],
        ['name'=>'sitemap生成','class'=>'\shell\tools\Sitemap','method'=>'create','class_param'=>['-a'],'method_param'=>''],
    ];

    public function run(){
        foreach ($this->task as $key => $item){
            echo '正在处理任务:'.$key.PHP_EOL;
            $this->callback($item['class'].'@'.$item['method'],[$item['method_param']],$item['class_param']);
        }
    }
}