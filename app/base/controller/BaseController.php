<?php
namespace app\base\controller;

class BaseController extends \framework\base\Controller{
	
	public function __construct()
    {
        //设置错误级别
        //error_reporting( E_ALL ^ E_NOTICE  ^ E_WARNING);
    	//定义常量
    	define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);
        define('DATA_PATH', ROOT_PATH . 'data' . DIRECTORY_SEPARATOR);
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);
        define('__PUBLIC__', substr(PUBLIC_URL, 0, -1));
        define('__ROOT__', substr(ROOT_URL, 0, -1));

        //判断安装程序
        $lock = ROOT_PATH . 'install.lock';
        if(!is_file($lock)){
            $this->redirect(url('install/Index/index'));
        }
        //引入扩展函数
        require_once(APP_PATH . 'base/util/Function.php');
        //引入当前模块配置
        $config = load_config('config');
        if(!empty($config)){
            foreach ((array)$config as $key => $value) {
                config($key, $value);
            }
        }
        //判断模块是否开启
        if(!config('APP_SYSTEM')){
            if (1 != config('APP_STATE') || 1 != config('APP_INSTALL')) {
                $this->error('该应用尚未开启!', false);
            }
        }

        $this->setCont();

    }

    /**
     * 设置站点基本信息
     */
    protected function setCont(){
        // 读取站点配置
        $siteConfig = target('admin/Config')->getInfo();
        $this->sys = $siteConfig;
        foreach ($siteConfig as $key => $value) {
            config($key, $value);
        }
        
        //设置站点
        $url = $_SERVER['HTTP_HOST'];
        $detect = new \app\base\util\Mobile_Detect();
        if(config('mobile_status')){
            //网站跳转
            if (!$detect->isMobile() && !$detect->isTablet()){
                if(config('site_url')&&$url<>config('site_url')){
                    $this->redirect('http://'.config('site_url'));
                }
                define('MOBILE',false);
            }else{
                if(config('mobile_domain')&&$url<>config('mobile_domain')){
                    $this->redirect('http://'.config('mobile_domain'));
                }
                define('MOBILE',true);
            }
        }else{
            //禁用手机版本
            define('MOBILE',false);
        }
        
    }

    /**
     * 获取渲染html
     */
    public function show($html = null){
        $this->display($html, false, false);
    }

    /**
     * 错误提示方法
     */
    public function error($msg, $url = null){
        if(IS_AJAX){
            $array = array(
                'info' => $msg,
                'status' => false,
                'url' => $url,
            );
            $this->ajaxReturn($array);
        }else{
            $this->alert($msg, $url);
        }
    }

    /**
     * 成功提示方法
     */
    public function success($msg, $url = null){
        if(IS_AJAX){
            $array = array(
                'info' => $msg,
                'status' => true,
                'url' => $url,
            );
            $this->ajaxReturn($array);
        }else{
            $this->alert($msg, $url);
        }
    }
    
    /**
     * AJAX返回
     * @param string $message 提示内容
     * @param bool $status 状态
     * @param string $jumpUrl 跳转地址
     * @return array
     */
    public function ajaxReturn($data){
        header('Content-type:text/json');
        echo json_encode($data);
        exit;
    }

    /**
     * 页面不存在
     * @return array 页面信息
     */
    protected function error404()
    {
        throw new \Exception("404页面不存在！", 404);
    }

    /**
     * 通讯错误
     */
    protected function errorBlock(){
        $this->error('通讯发生错误，请稍后刷新后尝试！');
    }

    /**
     * 获取分页数量
     * @param int $count 数据总数
     * @param int $num 每页数量
     */
    protected function getPageLimit($count, $num = 10)
    {
        //生成当前URL
        $url = url(APP_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME, array('page' => '{page}'));
        $url = urldecode($url);
        $page = is_object($this->pager['obj']) ? $this->pager['obj'] : new \framework\ext\Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $num;
        $limit = ($limit_start . ',') . $num;
        $this->pager = array(
            'obj' => $page,
            'url' => $url,
            'num' => $num,
            'count' => $count,
            'cur_page' => $cur_page,
            'limit' => $limit
        );
        return $limit;
    }

    //分页结果显示
    protected function getPageShow($map = array())
    {
        $map = array_filter($map);
        $url = url(APP_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME, array_merge((array)$map,array('page' => '{page}')));
        $url = urldecode($url);
        $page = $this->pager['obj']->show($url, $this->pager['count'], $this->pager['num']);
        $page = '<div class="dux-page">'.$page.'</div>';
        return $page;
    }

    protected function setPageConfig($name , $value) {
        $this->pager->set($name , $value);
    }
}