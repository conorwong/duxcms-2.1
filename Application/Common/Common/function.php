<?php
/**
 * 获取所有模块Service
 * @param string $name 指定Service名
 * @return ServiceList
 */
function getAllService($name,$method,$vars=array()){
    if(empty($name))return null;
    $apiPath = APP_PATH.'*/Service/'.$name.'Service.class.php';
    $apiList = glob($apiPath);
    if(empty($apiList)){
        return;
    }
    $appPathStr = strlen(APP_PATH);
    $method = 'get'.$method.$name;
    $data = array();
    foreach ($apiList as $value) {
        $path = substr($value, $appPathStr,-10);
        $path = str_replace('\\', '/',  $path);
        $appName = explode('/', $path);
        $appName = $appName[0];
        $config = load_config(APP_PATH . '/'. $appName .'/Conf/config.php');
        if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
            continue;
        }
        $class = A($appName.'/'.$name,'Service');
        if(method_exists($class,$method)){
            $data[$appName] = $class->$method($vars);
        }
    }
    return $data;
}

/**
 * 获取指定模块Service
 * @param string $name 指定Service名
 * @return Service
 */
function service($appName,$name,$method,$vars=array()){
    $config = load_config(APP_PATH . '/'. $appName .'/Conf/config.php');
    if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
        return;
    }
    $class = A($appName.'/'.$name,'Service');
    if(method_exists($class,$method)){
        return $class->$method($vars);
    }
}

/**
 * 调用指定模块的API
 * @param string $api
 * @return Api
 */
function api($appName,$api,$method,$vars=array())
{
    $config = load_config(APP_PATH . '/'. $appName .'/Conf/config.php');
    if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
        return;
    }
    return A("$appName/$api","Api")->$method($vars);
}

/**
 * 二维数组排序
 * @param array $array 排序的数组
 * @param string $key 排序主键
 * @param string $type 排序类型 asc|desc
 * @param bool $reset 是否返回原始主键
 * @return ApiList
 */
function array_order($array, $key, $type = 'asc', $reset = false)
{
    if (empty($array) || !is_array($array)) {
        return $array;
    }
    foreach ($array as $k => $v) {
        $keysvalue[$k] = $v[$key];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    $i = 0;
    foreach ($keysvalue as $k => $v) {
        $i++;
        if ($reset) {
            $new_array[$k] = $array[$k];
        } else {
            $new_array[$i] = $array[$k];
        }
    }
    return $new_array;
}

/**
 * 读入当前模块配置文件
 * @param  array $config 配置信息
 */
function current_config(){
    $file = MODULE_PATH . 'Conf/config.php';
    return load_config($file);
}

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($file, $config){
    if(is_array($config)){
        //读取配置内容
        $conf = file_get_contents($file);
        //替换配置项
        foreach ($config as $key => $value) {
            if (is_string($value) && !in_array($value, array('true','false'))){
                if (!is_numeric($value)) {
                    $value = "'" . $value . "'"; //如果是字符串，加上单引号
                }
            }
            $conf = preg_replace("/'" . $key . "'\s*=\>\s*(.*?),/iU", "'".$key."'=>".$value.",", $conf);
        }
        //写入应用配置文件
        if(!IS_WRITE){
            return false;
        }else{
            if(file_put_contents($file, $conf)){
                return true;
            } else {
                return false;
            }
            return '';
        }

    }
}

/**
 * 遍历删除目录和目录下所有文件
 * @param string $dir 路径
 * @return bool
 */
function del_dir($dir){
    if (!is_dir($dir)){
        return false;
    }
    $handle = opendir($dir);
    while (($file = readdir($handle)) !== false){
        if ($file != "." && $file != ".."){
            is_dir("$dir/$file")?   del_dir("$dir/$file"):@unlink("$dir/$file");
        }
    }
    if (readdir($handle) == false){
        closedir($handle);
        @rmdir($dir);
    }
}


//复制目录
function copy_dir($sourceDir,$aimDir){
        $succeed = true;
        if(!file_exists($aimDir)){
            if(!mkdir($aimDir,0777)){
                return false;
            }
        }
        $objDir = opendir($sourceDir);
        while(false !== ($fileName = readdir($objDir))){
            if(($fileName != ".") && ($fileName != "..")){
                if(!is_dir("$sourceDir/$fileName")){
                    if(!copy("$sourceDir/$fileName","$aimDir/$fileName")){
                        $succeed = false;
                        break;
                    }
                }
                else{
                    copy_dir("$sourceDir/$fileName","$aimDir/$fileName");
                }
            }
        }
        closedir($objDir);
        return $succeed;
}


/**
 * 获取文件或文件大小
 * @param string $directoty 路径
 * @return int
 */
function dir_size($directoty)
{
    $dir_size = 0;
    if ($dir_handle = @opendir($directoty)) {
        while ($filename = readdir($dir_handle)) {
            $subFile = $directoty . DIRECTORY_SEPARATOR . $filename;
            if ($filename == '.' || $filename == '..') {
                continue;
            } elseif (is_dir($subFile)) {
                $dir_size += dir_size($subFile);
            } elseif (is_file($subFile)) {
                $dir_size += filesize($subFile);
            }
        }
        closedir($dir_handle);
    }
    return ($dir_size);
}

/**
 * 字符串转布尔
 * @param string $directoty 路径
 * @return bool
 */
function string_to_bool($val)
{
    switch ($val) {
        case 'true':
            return true;
            break;
        case 'false':
            return false;
            break;
        
        default:
            return $val;
            break;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 生成唯一数字
 */
function unique_number()
{
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 生成随机字符串
 */
function random_str()
{
    $year_code = array('A','B','C','D','E','F','G','H','I','J');
    $order_sn = $year_code[intval(date('Y'))-2010].
    strtoupper(dechex(date('m'))).date('d').
    substr(time(),-5).substr(microtime(),2,5).sprintf('d',rand(0,99));
    return $order_sn;
}

/**
 * 判断不为空
 * @param string $directoty 路径
 * @return bool
 */
function not_empty($data)
{
    if(!empty($data)){
        return true;
    }else{
        return false;
    }
}

 //中文字符串截取
function msubstr($str, $length, $charset="utf-8", $suffix=true){
    if(empty($str)){
        return;
    }
    $sourcestr=$str;
    $cutlength=$length;
    $returnstr = '';
    $i = 0;
    $n = 0.0;
    $str_length = strlen($sourcestr); //字符串的字节数
    while ( ($n<$cutlength) and ($i<$str_length) ){
       $temp_str = substr($sourcestr, $i, 1);
       $ascnum = ord($temp_str); 
       if ( $ascnum >= 252){
        $returnstr = $returnstr . substr($sourcestr, $i, 6); 
        $i = $i + 6; 
        $n++; 
       }elseif ( $ascnum >= 248 ){
        $returnstr = $returnstr . substr($sourcestr, $i, 5);
        $i = $i + 5;
        $n++;
       }elseif ( $ascnum >= 240 ){
        $returnstr = $returnstr . substr($sourcestr, $i, 4);
        $i = $i + 4;
        $n++;
       }elseif ( $ascnum >= 224 ){
        $returnstr = $returnstr . substr($sourcestr, $i, 3);
        $i = $i + 3 ; 
        $n++; 
       }elseif ( $ascnum >= 192 ){
        $returnstr = $returnstr . substr($sourcestr, $i, 2);
        $i = $i + 2; 
        $n++; 
       }elseif ( $ascnum>=65 and $ascnum<=90 and $ascnum!=73){
        $returnstr = $returnstr . substr($sourcestr, $i, 1);
        $i = $i + 1;
        $n++;
       }elseif ( !(array_search($ascnum, array(37, 38, 64, 109 ,119)) === FALSE) ){
        $returnstr = $returnstr . substr($sourcestr, $i, 1);
        $i = $i + 1;
        $n++; 
       }else{
        $returnstr = $returnstr . substr($sourcestr, $i, 1);
        $i = $i + 1;
        $n = $n + 0.5; 
       }
    }
    if ( $i < $str_length && $suffix){
       $returnstr = $returnstr . '…';
    }
    return $returnstr;
}

 //字符串截取
function len($str, $len=0)
{
    if(!empty($len)){
        return msubstr($str, $len);
    }else{
        return $str;
    }
}

/**
 * 加解密函数
 * @param string $string 明文 或 密文 
 * @param string $operation DECODE表示解密,其它表示加密  
 * @param string $key 密匙  
 * @param string $expiry 密文有效期
 * @return bool
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {  
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙  
    $ckey_length = 4;  
      
    // 密匙  
    $key = md5($key ? $key : C('SAFE_KEY'));  
      
    // 密匙a会参与加解密  
    $keya = md5(substr($key, 0, 16));  
    // 密匙b会用来做数据完整性验证  
    $keyb = md5(substr($key, 16, 16));  
    // 密匙c用于变化生成的密文  
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';  
    // 参与运算的密匙  
    $cryptkey = $keya.md5($keya.$keyc);  
    $key_length = strlen($cryptkey);  
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性  
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确  
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;  
    $string_length = strlen($string);  
    $result = '';  
    $box = range(0, 255);  
    $rndkey = array();  
    // 产生密匙簿  
    for($i = 0; $i <= 255; $i++) {  
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);  
    }  
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度  
    for($j = $i = 0; $i < 256; $i++) {  
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;  
        $tmp = $box[$i];  
        $box[$i] = $box[$j];  
        $box[$j] = $tmp;  
    }  
    // 核心加解密部分  
    for($a = $j = $i = 0; $i < $string_length; $i++) {  
        $a = ($a + 1) % 256;  
        $j = ($j + $box[$a]) % 256;  
        $tmp = $box[$a];  
        $box[$a] = $box[$j];  
        $box[$j] = $tmp;  
        // 从密匙簿得出密匙进行异或，再转成字符  
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));  
    }  
    if($operation == 'DECODE') {  
        // substr($result, 0, 10) == 0 验证数据有效性  
        // substr($result, 0, 10) - time() > 0 验证数据有效性  
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性  
        // 验证数据有效性，请看未加密明文的格式  
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {  
            return substr($result, 26);  
        } else {  
            return '';  
        }  
    } else {  
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因  
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码  
        return $keyc.str_replace('=', '', base64_encode($result));  
    }  
 }

 /**
 * 时间格式化
 */
 function format_time($time, $type = 1 , $date = 'Y-m-d H:i:s') {
    switch ($type) {
        case 1:
            return date($date,$time);
            break;
        case 2:
            $d=time()-$time;
            if($d<0){ 
                return date($date,$time);
            }
            if($d<60){ 
                return $d.'秒前'; 
            }else{ 
                if($d<3600){ 
                    return floor($d/60).'分钟前'; 
                }else{ 
                    if($d<86400){ 
                        return floor($d/3600).'小时前'; 
                    }else{ 
                        if($d<259200){
                            return floor($d/86400).'天前'; 
                        }else{ 
                            return date($date,$time); 
                        } 
                    } 
                } 
            }
            break;
    }
}

//文本输入
function text_in($str){
    $str=strip_tags($str,'<br>');
    $str = str_replace(" ", "&nbsp;", $str);
    $str = str_replace("\n", "<br>", $str); 
    if(!get_magic_quotes_gpc()) {
      $str = addslashes($str);
    }
    return $str;
}

//文本输出
function text_out($str){
    $str = str_replace("&nbsp;", " ", $str);
    $str = str_replace("<br>", "\n", $str); 
    $str = stripslashes($str);
    return $str;
}

//html代码输入
function html_in($str){
    $str=htmlspecialchars($str);
    if(!get_magic_quotes_gpc()) {
        $str = addslashes($str);
    }
   return $str;
}

//html代码输出
function html_out($str){
    if(function_exists('htmlspecialchars_decode')){
        $str=htmlspecialchars_decode($str);
    }else{
        $str=html_entity_decode($str);
    }
    $str = stripslashes($str);
    return $str;
}

//默认数据
function default_data($data,$var){
    if(empty($data)){
        return $var;
    }else{
        return $data;
    }
}
