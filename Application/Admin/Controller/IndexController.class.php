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
        //设置其他调用
        $this->assign('chartArray',D('DuxCms/TotalVisitor')->getJson(7,'day','m-d'));
        $this->adminDisplay();
    }
}

