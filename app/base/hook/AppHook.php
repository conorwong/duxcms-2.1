<?php
namespace app\base\hook;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
require BASE_PATH . '/vendor/autoload.php';

class AppHook
{
    public $startTime = 0;

    public function appBegin()
    {
        // 注册异常接管
        $whoops = new Run();
        $whoops->prependHandler(new PrettyPageHandler);
        $whoops->register();

        $this->startTime = microtime(true);
    }
    
    public function appEnd()
    {
        //echo microtime(true) - $this->startTime ;
    }

    public function appError($e)
    {
        if (404 == $e->getCode()) {
            $action = 'error404';
        } else {
            $action = 'error';
        }
        obj('app\base\controller\ErrorController')->$action($e);
    }
    
    public function routeParseUrl($rewriteRule, $rewriteOn)
    {
    }

    public function actionBefore($obj, $action)
    {
    }

    public function actionAfter($obj, $action)
    {
    }
}
