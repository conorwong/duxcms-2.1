<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
/**
 * 网站管理
 */

class ManageController extends AdminController {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '网站管理',
                'description' => '管理网站基本功能',
                ),
            'menu' => array(
                    array(
                        'name' => '缓存管理',
                        'url' => U('cache'),
                        'icon' => 'exclamation-circle',
                    ),
                )
            );
    }
	/**
     * 站点设置
     */
    public function cache(){
        if(!IS_POST){
            $breadCrumb = array('缓存管理'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('list',D('Manage')->getCacheList());
            $this->adminDisplay();
        }else{
            $key = I('post.data');
            if(empty($key)){
                $this->error('没有获取到清除动作！');
            }
            if(D('Manage')->delCache($key)){
                $this->success('缓存清空成功！');
            }else{
                $this->error('缓存清空失败！');
            }
            
        }
    }
}

