<?php
namespace Article\Controller;
use Admin\Controller\AdminController;
/**
 * 应用设置
 */
class AdminSettingController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        $menu = A('Admin/Functions');
        $menu = $menu->infoModule;
        $menu['cutNav'] = array(
                'url' => U('Admin/Functions/index'),
                'complete' => false,
                );
        return $menu;
        
    }

    /**
     * 设置
     */
    public function index(){
        if(!IS_POST){
            $breadCrumb = array('应用列表'=>U('Admin/Functions/index'),'文章模块设置'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('info',current_config());
            $this->adminDisplay();
        }else{
            $file = MODULE_PATH . 'Conf/config.php';
            if(write_config($file, $_POST)){
                $this->success('应用配置成功！');
            }else{
                $this->error('应用配置失败');
            }
        }
    }
    

}

