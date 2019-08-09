<?php
namespace framework\base;

class Hook
{
    public static $tags = array();
    
    public static function init($basePath='')
    {
        $dir = str_replace('/', DIRECTORY_SEPARATOR, $basePath.'app/base/hook/');
        foreach (glob($dir . '*.php') as $file) {
            $pos = strrpos($file, DIRECTORY_SEPARATOR);
            if (false === $pos) {
                continue;
            }
            
            $class = substr($file, $pos + 1, -4);
            $class = "\\app\\base\\hook\\{$class}";
            
            $methods = get_class_methods($class);
            foreach ((array)$methods as $method) {
                self::$tags[$method][] = $class;
            }
        }
    }
    
    public static function listen($tag, $params=array(), &$result=null)
    {
        if (!isset(self::$tags[$tag])) {
            return false;
        }
        foreach (self::$tags[$tag] as $class) {
            $result = self::exec($class, $tag, $params);
            if (false === $result) {
                break;
            }
        }
        return true;
    }
    
    protected static function exec($class, $method, $params)
    {
        static $objArr = array();
        if (!isset($objArr[$class])) {
            $objArr[$class]= new $class();
        }
        return call_user_func_array(array($objArr[$class], $method), (array)$params);
    }
}
