<?php
/**
 * 获取request请求方法
 */
function request($str = null, $default = null, $function = '') {
	if(empty($str)){
		return ;
	}
	$str = trim($str);
	list($method,$name) = explode('.',$str,2);
	$method = strtolower($method);
	switch ($method) {
		case 'get':
			$type = $_GET;
			break;
		case 'post':
			$type = $_POST;
			break;
		case 'session':
			$type = $_SESSION;
			break;
		case 'request':
			$type = $_REQUEST;
			break;
		case 'cookie':
			$type = $_COOKIE;
			break;
		default:
            return ;
	}
	if(empty($name)){
		$request = $type;
	}else{
		$request = $type[$name];
	}
	$request = filter_string($request);
	if(!empty($name)){
		if(!empty($default)){
			if(empty($request)){
				$request = $default;
			}
		}
		if(!empty($function)){
			$request = call_user_func($function,$request);
		}
	}
	return $request;
}
/**
 * 过滤数据
 * @param  array  $data 过滤数据
 */
function filter_string($data){
    if($data===NULL){
        return false;
    }
    if (is_array($data)){
        foreach ($data as $k=>$v){
            $data[$k] = filter_string($v);
        }
        return $data;
    }else{
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
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
 * 读取模块配置
 * @param string $file 调用文件
 * @return array
 */
function load_config($file){
	$file = get_config_file($file);
	return require $file;
}

/**
 * 读取当前模块配置
 * @return array
 */

function current_config(){
    return load_config('config');
}

/**
 * 保存模块配置
 * @param string $file 调用文件
 * @return array
 */
function save_config($file, $config){
	if(empty($config) || !is_array($config)){
		return array();
	}
	$file = get_config_file($file);
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

/**
 * 字符串转方法数组
 * @param string $str
 * @return array
 */
function get_method_array($str = ''){
    $strArray = array();
    if(!empty($str)){
        $strArray = explode('/', $str, 3);
    }
    $strCount = count($strArray);
    switch ($strCount) {
            case 1:
                $app = APP_NAME;
                $controller = CONTROLLER_NAME;
                $action = $strArray[0];
                break;
            case 2:
                $app = APP_NAME;
                $controller = $strArray[0];
                $action = $strArray[1];
                break;
            case 3:
                $app = $strArray[0];
                $controller = $strArray[1];
                $action = $strArray[2];
                break;
            default:
                $app = APP_NAME;
                $controller = CONTROLLER_NAME;
                $action = ACTION_NAME;
                break;
        }
        return array(
            'app' => strtolower($app),
            'controller' => $controller,
            'action' => $action,
            );
}

/**
 * 解析配置文件路径
 * @param string $file 文件路径或简写路径
 * @return dir
 */
function get_config_file($file){
	$name = $file;
	if(!is_file($file)){
		$str = explode('/', $file);
		$strCount = count($str);
		switch ($strCount) {
			case 1:
				$app = APP_NAME;
				$name = $str[0];
				break;
			case 2:
				$app = $str[0];
				$name = $str[1];
				break;
		}
		if(empty($app)&&empty($file)){
			throw new \Exception("Config '{$file}' not found'", 500);
		}
		$file = APP_PATH . "{$app}/conf/{$name}.php";
		if(!file_exists($file)){
			throw new \Exception("Config '{$name}' not found", 500);
		}
	}
	return $file;
}
/**
 * 自适应URL规则
 * @param string $str URL路径
 * @param string $params 内容数组
 * @return url
 */
function match_url($str,$params = array()){
    $newParams = array();
    //获取规则文件
    $config = config('REWRITE_RULE');
    $configArray = array_flip($config);
    $route = $configArray[$str];
    if(empty($config)||empty($route)){
        return url($str, $params);
    }
    preg_match_all('/<(\w+)>/', $route, $matches);
    foreach ($matches[1] as $value) {
        if($params[$value]){
            $newParams[$value] = $params[$value];
        }
    }
    return url($str, $newParams);
}

/**
 * 通用模块调用函数
 * @param string $str 调用路径
 * @param string $layer 调用层 默认model
 * @return obj
 */
function target($str , $layer = 'model'){
	static $_target  =   array();
	$str = explode('/', $str);
	$strCount = count($str);
	switch ($strCount) {
		case 1:
			$app = APP_NAME;
			$module = $str[0];
			break;
		case 2:
			$app = $str[0];
			$module = $str[1];
			break;
	}
	$app = strtolower($app);
	$name = $app.'/'.$layer.'/'.$module;
	if(isset($_target[$name])){
        return $_target[$name];
	}
	$class = "\\app\\{$app}\\{$layer}\\{$module}".ucfirst($layer);
	if(!class_exists($class)){
		throw new \Exception("Class '{$class}' not found'", 500);
	}
	$target = new $class();
	$_target[$name]  =  $target;
	return $target;
}

/**
 * 获取所有模块Service
 * @param string $name 指定service名
 * @return array
 */
function get_all_service($name,$method,$vars=array()){

    if(empty($name))return null;
    $apiPath = APP_PATH.'*/Service/'.$name.'Service.php';
    $apiList = glob($apiPath);
    if(empty($apiList)){
        return;
    }
    $appPathStr = strlen(APP_PATH);
    $method = 'get'.$method.$name;
    $data = array();
    foreach ($apiList as $value) {
        $path = substr($value, $appPathStr,-4);
        $path = str_replace('\\', '/',  $path);
        $appName = explode('/', $path);
        $appName = $appName[0];
        $config = load_config($appName .'/config');
        if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
            continue;
        }
        $class = target($appName.'/'.$name,'service');
        if(method_exists($class,$method)){
            $data[$appName] = $class->$method($vars);
        }
    }
    return $data;
}

/**
 * 获取指定模块Service
 * @param string $name 指定service名
 * @return Service
 */
function service($appName,$name,$method,$vars=array()){
    $config = load_config($appName .'/config');
    if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
        return;
    }
    $class = target($appName.'/'.$name,'service');
    if(method_exists($class,$method)){
        return $class->$method($vars);
    }
}

/**
 * 调用指定模块的API
 * @param string $name  指定api名
 * @return Api
 */
function api($appName,$name,$method,$vars=array())
{
    $config = load_config($appName .'/config');
    if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
        return;
    }
    $class = target($appName.'/'.$name,'api');
    if(method_exists($class,$method)){
        return $class->$method($vars);
    }
}

/**
 * 二维数组排序
 * @param array $array 排序的数组
 * @param string $key 排序主键
 * @param string $type 排序类型 asc|desc
 * @param bool $reset 是否返回原始主键
 * @return array
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
 * session获取
 * @param string $name
 * @param string $value
 * @return  string
 */
function session($name='',$value='') {
	if(empty($name)){
		return $_SESSION;
	}
    $sessionId = request('request.session_id');
    if(!empty($sessionId)){
        session_id($sessionId);
    }
	if (!session_id()) session_start();
	$pre = config('COOKIE_PREFIX');
	if(empty($value)){
		$session = $_SESSION[$pre . $name];
	}else{
		$session = $_SESSION[$pre . $name] = $value;
	}
	return $session;
}

/**
 * 默认数据
 * @param string $data
 * @param string $var
 * @return  string
 */
function default_data($data,$var){
    if(empty($data)){
        return $var;
    }else{
        return $data;
    }
}

//图片裁剪
function cut_image($img, $width, $height, $type = 3)
{
    if(empty($width)&&empty($height)){
        return $img;
    }
    $imgDir = realpath(ROOT_PATH.$img);
    if(!is_file($imgDir)){
        return $img;
    }
    $imgInfo = pathinfo($img);
    $newImg = $imgInfo['dirname'].'/cut_'.$width.'_'.$height.'_'.$imgInfo["basename"];
    $newImgDir = ROOT_PATH.$newImg;
    if(!is_file($newImgDir)){
        $image = new \app\base\util\ThinkImage();
        $image->open($imgDir);
        $image->thumb($width, $height,$type)->save($newImgDir);
    }
    return $newImg;
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
 * 遍历删除目录和目录下所有文件
 * @param string $dir 路径
 * @return bool
 */
function del_dir($dir){
    framework\ext\Util::delDir($dir);
}

/**
 * html代码输入
 */
function html_in($str){
    $str=htmlspecialchars($str);
    if(!get_magic_quotes_gpc()) {
        $str = addslashes($str);
    }
   return $str;
}

/**
 * html代码输出
 */
function html_out($str){
    if(function_exists('htmlspecialchars_decode')){
        $str=htmlspecialchars_decode($str);
    }else{
        $str=html_entity_decode($str);
    }
    $str = stripslashes($str);
    return $str;
}

/**
 * 字符串截取
 */
function len($str, $len=0)
{
    if(!empty($len)){
        return \framework\ext\Util::msubstr($str, 0, $len);
    }else{
        return $str;
    }
}

/**
 * 兼容老版U方法
 */
function U($str = null, $var = null)
{
    return url($str,$var);
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