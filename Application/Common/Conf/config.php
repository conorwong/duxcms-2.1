<?php
$dir = dirname(__FILE__);
//载入其他配置(//解决入口无法定义配置的问题)
$files = array('performance.php','shield.php','db.php','ver.php');
$config = array(
	//'配置项'=>'配置值'
	'TMPL_ENGINE_TYPE' => 'Dux',
    'TMPL_TEMPLATE_SUFFIX'=>'.html',
    'VAR_SESSION_ID' => 'session_id',
    'URL_HTML_SUFFIX'=>'html',
	'SITE_URL' => '',
	'APP_SYSTEM' => 1,
	'APP_NAME' => '基础应用',
	'URL_CASE_INSENSITIVE' => false, 
	);
foreach ($files as $value) {
	$array = include $dir.'/'.$value;
	$config = array_merge($config,$array);
}

$router = include $dir.'/router/router-'.$config['URL_ROUTER_TYPE'].'.php';
if($config['URL_MODEL'] == 2) {
	$config['URL_ROUTER_ON'] = true;
}else{
	$config['URL_ROUTER_ON'] = false;
}
$config = array_merge($config,$router,(array)$GLOBALS['config']);

//设置404页面
if(!$config['SHOW_ERROR_MSG']){
	$config['TMPL_EXCEPTION_FILE'] = ROOT_PATH.'Themes/Common/404.html';
}
return $config;