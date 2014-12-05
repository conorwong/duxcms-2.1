<?php
namespace DuxCms\Controller;
use Admin\Controller\AdminController;
/**
 * 站点安全
 */

class AdminSafeController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '安全中心',
                'description' => '测试站点整体安全性',
                ),
            );
    }
	/**
     * 安全测试
     */
    public function index(){
        
        $safeArray = array();
        //数据库弱口令
        if(preg_match('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W).+$/', C('DB_PWD'))){
            $safeArray['db'] = true;
        }
        //登录密码检测
        $list = D('Admin/AdminUser')->loadList();
        $safeArray['user'] = true;
        foreach ($list as $value) {
            if($value['password'] == '21232f297a57a5a743894a0e4a801fc3'){
                $safeArray['user'] = false;
            }
        }
        //后台入口检测
        if(!is_file(__ROOT__ . '/admin.php')){
            $safeArray['login'] = true;
        }
        //上传设置检测
        $ext = 'php,asp,jsp,aspx';
        $extArray = explode(',', $ext);
        $safeArray['upload'] = true;
        foreach ($extArray as $value) {
            if(strstr($value, C('upload_exts'))){
                $safeArray['upload'] = false;
            }
        }
        //安装模块检测
        if(!is_dir(APP_PATH . 'Install')){
            $safeArray['install'] = true;
        }

        $checkArray = array();
        //上传目录检测
        $dir = @fopen(__ROOT__ .'Uploads','wb');
        if ($dir !== false)
        {
            $checkArray['upload'] = true;
        }
        $breadCrumb = array('安全中心'=>U());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('safeArray',$safeArray);
        $this->assign('checkArray',$checkArray);
        $this->adminDisplay();
    }

}

