<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Application/');

// 定义运行时目录
define('RUNTIME_PATH','./Runtime/');

//定义网站物理路径
define('ROOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

$_GET['m'] = 'Admin'; 

//定义后台配置
$GLOBALS['config'] = array (
  'TMPL_CACHE_ON' => false,
  'HTML_CACHE_ON' => false,
  'DB_SQL_BUILD_CACHE' => false,
  'URL_MODEL' => 3,
  'URL_ROUTER_ON' => false,
  'URL_HTML_SUFFIX'=>'html',
  'LOG_RECORD' => true,
  'SHOW_ERROR_MSG' => true,
  'DB_SQL_LOG' => false,
);

// 引入ThinkPHP入口文件
require './Core/ThinkPHP.php';