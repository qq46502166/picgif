<?php
/* ========================================================================
 * 加载系统配置类,可以防止重复引入文件
 * ======================================================================== */
namespace core;

class Conf
{
    /**
     * 用来存储已经加载过的配置
     *
     * @var array
     */
    static protected $conf = [];
    
    /**
     * 加载系统配置,如果之前已经加载过,那么就直接返回
     * @param string $name 配置名
     * @param string $file 文件名
	 * @param mixed $default 默认值，读取不到时返回的值
	 * @param string $path 文件夹相对于根目录的路径
     * @return null|array|string
     */
    static public function get($name, $fileName, $default = null, $folder='config/')
    {
        if(isset(self::$conf[$folder.$fileName][$name])) {
            return self::$conf[$folder.$fileName][$name];
        } else { 
            $conf = ROOT.'/'.$folder.$fileName.'.php';
            if(is_file($conf)) {
                self::$conf[$folder.$fileName] = include $conf;
                return isset(self::$conf[$folder.$fileName][$name]) ? self::$conf[$folder.$fileName][$name]: $default;
            } else {
                return $default;
            }
        }
        
    }
    
    /**
     * 加载系统配置文件(直接加载整个配置文件),如果之前已经加载过,那么就直接返回
     * @param string $file 文件名
	 * @param mixed $default 默认值，读取不到文件时返回的值
	 * @param string $path 文件夹相对于根目录的路径
     * @return null|array|string
     */
    static public function all($fileName,$default = null,$folder= 'config/')
    {
        if(isset(self::$conf[$folder.$fileName])) {
            return self::$conf[$folder.$fileName];
        } else {
            $conf = ROOT.'/'.$folder.$fileName.'.php';
            if(is_file($conf)) {
                self::$conf[$folder.$fileName] = include $conf;
                return self::$conf[$folder.$fileName];
            } else {
                return $default;
            }
        }
    }

    /** ------------------------------------------------------------------
     * 向配置文件中添加项
     * @param string $name 配置项的名称
     * @param int|string|array $value  配置项的值
     * @param bool $isArray 配置项是否是数组
     * @param string $fileName 文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *---------------------------------------------------------------------*/
    static public function add($name,$value,$isArray,$fileName,$folder='config/'){
        $all=self::all($fileName,[],$folder);
        if($isArray){
            if(!isset($all[$name]))
                $all[$name]=[];
            $all[$name][]=$value;
        }else
            $all[$name]=$value;
        return self::write($all,$fileName,$folder);
    }

    /** ------------------------------------------------------------------
     * 删除配置项
     * @param string $name 配置项的名称
     * @param int|string $key 键名
     * @param string $fileName 文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *---------------------------------------------------------------------*/
    static public function del($name,$key,$fileName,$folder='config/'){
        $all=self::all($fileName,[],$folder);
        if($key===null)
            unset($all[$name]);
        else
            unset($all[$name][$key]);
        return self::write($all,$fileName,$folder);
    }

    /** ------------------------------------------------------------------
     * 修改配置
     * @param string $name 配置项的名称
     * @param int|string $newKey 新的key名
     * @param int|string $oldKey 原来的key名
     * @param int|string|array $value  配置项的值
     * @param string $fileName  文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *---------------------------------------------------------------------*/
    static public function edit($name,$newKey,$oldKey,$value,$fileName,&$msg='',$folder='config/'){
        $all=self::all($fileName,[],$folder);
        if($newKey===null)
            $all[$name]=$value;
        else{
            unset($all[$name][$oldKey]);
            if(array_key_exists($newKey,$all[$name])){
                $msg='有相同的健名存在';
                return false;
            }
            $all[$name][$newKey]=$value;
        }
        //reset ( $all[$name]);
        return self::write($all,$fileName,$folder);
    }

    /** ------------------------------------------------------------------
     * 写入文件中
     * @param mixed $data 数据
     * @param string $fileName 文件名
     * @param string $folder 相对于根目录的文件夹路径
     * @return bool
     *--------------------------------------------------------------------*/
    static function write($data,$fileName,$folder='config/'){
        $str="<?php \n return ".var_export($data,true).';';
        $path=ROOT.'/'.$folder.$fileName.'.php';
        return \core\lib\cache\File::write($path,$str);
    }


}