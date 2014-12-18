<?php
$dir = dirname(__FILE__);
//载入其他配置(//解决入口无法定义配置的问题)
$performance = include $dir.'/performance.php';
$shield = include $dir.'/shield.php';
$db = include $dir.'/db.php';
$ver = include $dir.'/ver.php';
$config = array(
	//'配置项'=>'配置值'
	'TMPL_ENGINE_TYPE' => 'Dux',
    'TMPL_TEMPLATE_SUFFIX'=>'.html',
    'VAR_SESSION_ID' => 'session_id',
    'URL_HTML_SUFFIX'=>'html',
	'SITE_URL' => '',
	'APP_SYSTEM' => 1,
	'APP_NAME' => '基础应用',
	'URL_404_REDIRECT'      =>  'http://www.baidu.com',
	);

$config = array_merge($performance,$shield,$db,$ver,$config,(array)$GLOBALS['config']);
//设置404页面
if(!$config['SHOW_ERROR_MSG']){
	$config['TMPL_EXCEPTION_FILE'] = ROOT_PATH.'Themes/Common/404.html';
}
return $config;