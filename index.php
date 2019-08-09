<?php

if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    header("Content-Type: text/html; charset=UTF-8");
    echo 'PHP环境不能低于7.0';
    exit;
}
require('framework/core.php');
