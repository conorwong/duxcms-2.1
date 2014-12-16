<?php
namespace Common\Controller;
use Think\Controller;
/**
 * 前台公共类
 */
class BaseController extends Controller {

    public function __construct()
    {
        parent::__construct();
        //判断安装程序
        $lock = realpath('./') . DIRECTORY_SEPARATOR . 'install.lock';
        if(!is_file($lock)){
            $this->redirect('Install/Index/index');
        }
        $this->setCont();
    }

    protected function setCont(){
        // 读取站点配置
        $siteConfig = D('Admin/Config')->getInfo();
        $this->sys = $siteConfig;
        C($siteConfig);
        //设置站点
        $url = $_SERVER['HTTP_HOST'];
        $detect = new \Common\Util\Mobile_Detect();
        if(C('MOBILE_STATUS')){
            //网站跳转
            if (!$detect->isMobile() && !$detect->isTablet()){
                if(C('SITE_URL')&&$url<>C('SITE_URL')){
                    redirect('http://'.C('SITE_URL'));
                }
                define('MOBILE',False);
            }else{
                if(C('MOBILE_DOMAIN')&&$url<>C('MOBILE_DOMAIN')){
                    redirect('http://'.C('MOBILE_DOMAIN'));
                }
                define('MOBILE',True);
            }
        }else{
            //禁用手机版本
            define('MOBILE',False);
        }
        
    }

    /**
     * 页面不存在
     * @return array 页面信息
     */
    protected function error404()
    {
        $this->error('页面不存在！');
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
     * @param int $listRows 每页数量
     */
    protected function getPageLimit($count,$listRows) {

        $this->pager = new \Think\Page($count,$listRows);
        return $this->pager->firstRow.','.$this->pager->listRows;

    }

    /**
     * 分页显示
     * @param array $map 分页附加参数
     */
    protected function getPageShow($map = '') {
        if(!empty($map)){
            $map = array_filter($map);
            $this->pager->parameter = $map;
        }
        return $this->pager->show();
    }

    protected function setPageConfig($name , $value) {
        $this->pager->setConfig($name , $value);
    }
}