<?php

if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    header("Content-Type: text/html; charset=UTF-8");
    echo 'PHP环境不能低于5.6';
    exit;
}
require('framework/core.php');
