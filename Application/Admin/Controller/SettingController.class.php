<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
/**
 * 网站设置
 */

class SettingController extends AdminController {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '网站设置',
                'description' => '设置网站整体功能',
                ),
            'menu' => array(
                    array(
                        'name' => '站点信息',
                        'url' => U('Setting/site'),
                        'icon' => 'exclamation-circle',
                    ),
                    array(
                        'name' => '手机设置',
                        'url' => U('Setting/mobile'),
                        'icon' => 'mobile',
                    ),
                    array(
                        'name' => '模板设置',
                        'url' => U('Setting/tpl'),
                        'icon' => 'eye',
                    ),
                    array(
                        'name' => '性能设置',
                        'url' => U('Setting/performance'),
                        'icon' => 'dashboard',
                    ),
                    array(
                        'name' => '安全设置',
                        'url' => U('Setting/shield'),
                        'icon' => 'shield',
                    )
                )
        );
    }
	/**
     * 站点设置
     */
    public function site(){
        if(!IS_POST){
            $breadCrumb = array('站点信息'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',D('Config')->getInfo());
            $this->adminDisplay();
        }else{
            
            if(D('Config')->saveData()){
                $this->success('站点配置成功！');
            }else{
                $this->error('站点配置失败');
            }
        }
    }
    /**
     * 手机设置
     */
    public function mobile(){
        if(!IS_POST){
            $breadCrumb = array('模板设置'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('themesList',D('Config')->themesList());
            $this->assign('tplList',D('Config')->tplList());
            $this->assign('info',D('Config')->getInfo());
            $this->adminDisplay();
        }else{
            if(D('Config')->saveData()){
                $this->success('模板配置成功！');
            }else{
                $this->error('模板配置失败');
            }
        }
    }
    /**
     * 模板设置
     */
    public function tpl(){
        if(!IS_POST){
            $breadCrumb = array('模板设置'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('themesList',D('Config')->themesList());
            $this->assign('tplList',D('Config')->tplList());
            $this->assign('info',D('Config')->getInfo());
            $this->adminDisplay();
        }else{
            if(D('Config')->saveData()){
                $this->success('模板配置成功！');
            }else{
                $this->error('模板配置失败');
            }
        }
    }
    /**
     * 性能设置
     */
    public function performance(){
        $file = APP_PATH . 'Common/Conf/performance.php';
        if(!IS_POST){
            $breadCrumb = array('性能设置'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',load_config($file));
            $this->adminDisplay();
        }else{
            if(write_config($file, $_POST)){
                $this->success('性能配置成功！');
            }else{
                $this->error('性能配置失败');
            }
        }
    }
    /**
     * 安全设置
     */
    public function shield(){
        $file = APP_PATH . 'Common/Conf/shield.php';
        if(!IS_POST){
            $breadCrumb = array('安全设置'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',load_config($file));
            $this->adminDisplay();
        }else{
            if(write_config($file, $_POST)){
                $this->success('安全配置成功！');
            }else{
                $this->error('安全配置失败');
            }
        }
    }
}

