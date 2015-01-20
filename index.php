<?php
if (version_compare(PHP_VERSION, '5.3.0','<')) {
    echo 'PHP环境不能低于5.3.0';
    exit;
}
require( 'framework/core.php' );