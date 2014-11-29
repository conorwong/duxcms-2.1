<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
/**
 * 应用管理
 */

class FunctionsController extends AdminController {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '应用管理',
                'description' => '管理网站基本功能',
                ),
            'menu' => array(
                    array(
                        'name' => '应用列表',
                        'url' => U('index'),
                        'icon' => 'exclamation-circle',
                    ),
                )
            );
    }
	/**
     * 应用列表
     */
    public function index(){
        if(!IS_POST){
            $breadCrumb = array('应用管理'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('list',D('Functions')->loadList());
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

    /**
     * 状态更改
     */
    public function state(){
        $data = I('post.data');
        $app = I('get.app');
        //更新配置
        $config = array();
        $config['APP_STATE'] = $data;
        $file = APP_PATH.'/'. $app .'/Conf/config.php';
        $info = load_config($file);
        if($info['APP_SYSTEM']){
            $this->error('系统应用无法禁用！');
        }
        if(write_config($file, $config)){
            $this->success('应用状态更新成功！');
        }else{
            $this->error('应用状态更新失败！');
        }
    }

    /**
     * 卸载
     */
    public function uninstall(){
        $data = I('post.data');
        $app = I('get.app');
        //更新配置
        $config = array();
        $config['APP_STATE'] = $data;
        $file = APP_PATH.'/'. $app .'/Conf/config.php';
        $info = load_config($file);
        if($info['APP_SYSTEM']){
            $this->error('系统应用无法禁用！');
        }
        if(write_config($file, $config)){
            $this->success('应用状态更新成功！');
        }else{
            $this->error('应用状态更新失败！');
        }
    }
}

