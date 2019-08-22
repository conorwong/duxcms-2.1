<?php
namespace app\base\controller;

use framework\base\Controller;

class ErrorController extends Controller
{
    public function error404($e=null)
    {
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        $this->error($e);
    }
    
    public function error($e=null)
    {
        //关闭调试或者是线上版本，不显示详细错误
        if (false==config('DEBUG') || 'production' == config('ENV')) {
            $home = new \app\home\controller\IndexController();
            $home->error404();
        } else {
            throw $e;
        }
    }
}
