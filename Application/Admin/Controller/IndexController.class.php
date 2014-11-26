<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
/**
 * 后台首页
 */
class IndexController extends AdminController {

    /**
     * 当前模块参数
     */
	protected function _infoModule(){
		return array(
            'menu' => array(
            		array(
	                    'name' => '管理首页',
	                    'url' => U('Index/index'),
	                    'icon' => 'dashboard',
                    )
                ),
            'info' => array(
                    'name' => '管理首页',
                    'description' => '站点运行信息',
                )
            );
	}
	/**
     * 首页
     */
    public function index(){
    	//设置目录导航
    	$breadCrumb = array('首页'=>U('Index/index'));
        $this->assign('breadCrumb',$breadCrumb);
        $this->adminDisplay();
    }
}

