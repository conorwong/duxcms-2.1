<?php
$dir = dirname(__FILE__);
//载入其他配置(//解决入口无法定义配置的问题)
$performance = include $dir.'/performance.php';
$shield = include $dir.'/shield.php';
$db = include $dir.'/db.php';
$config = array(
	//'配置项'=>'配置值'
	'TMPL_ENGINE_TYPE' => 'Dux',
    'TMPL_TEMPLATE_SUFFIX'=>'.html',
    'VAR_SESSION_ID' => 'session_id',
    'URL_HTML_SUFFIX'=>'html',
	'SITE_URL' => '',
	'APP_SYSTEM' => 1,
	'APP_NAME' => '基础应用',
	);
$config = array_merge($performance,$shield,$db,$config,(array)$GLOBALS['config']);
return $config;