<?php
namespace Admin\Controller;
use Common\Controller\BaseController;
/**
 * 后台公共类
 */
class AdminController extends BaseController {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
        //强制后台入口登录
        if (!defined('ADMIN_STATUS')) { 
            $this->error('请从后台入口重新登录！', false);
        }
        // 检测用户登录
        define('ADMIN_ID',$this->isLogin());
        if( !ADMIN_ID && ( MODULE_NAME <> 'Admin' || CONTROLLER_NAME <> 'Login' )){
            $this->redirect('Admin/Login/index');
        }
        // 读取后台配置
        //$config = load_config(APP_PATH . 'Common/Conf/admin.php');
        //C($config);
        //判断模块是否开启
        if(!C('APP_SYSTEM')){
            if (1 != C('APP_STATE') || 1 != C('APP_INSTALL')) {
                $this->error('该应用尚未开启!', false);
            }
        }
        //设置登录用户信息
        $this->loginUserInfo = D('Admin/AdminUser')->getInfo(ADMIN_ID);
        //检测权限
        $this->checkPurview();
        //赋值当前菜单
        if(method_exists($this,'_infoModule')){
            $this->infoModule = $this->_infoModule();
        }
    }

    /**
     * 用户权限检测
     */
    protected function checkPurview()
    {
        if ($this->loginUserInfo['user_id'] == 1 || $this->loginUserInfo['group_id'] == 1) {
            return true;
        }
        $basePurview = unserialize($this->loginUserInfo['base_purview']);
        $purviewInfo = service(MODULE_NAME,'Purview','getAdminPurview');
        if (empty($purviewInfo)) {
            return true;
        }
        $controller = $purviewInfo[CONTROLLER_NAME];
        if (empty($controller['auth'])) {
            return true;
        }
        $action = $controller['auth'][ACTION_NAME];
        if (empty($action)) {
            return true;
        }
        $current = APP_NAME . '_' . CONTROLLER_NAME;
        if (!in_array($current, (array) $basePurview)) {
            $this->error('您没有权限访问此功能！');
        }
        $current = APP_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
        if (!in_array($current, (array) $basePurview)) {
            $this->error('您没有权限访问此功能！');
        }
        return true;
    }

    /**
     * 检测用户是否登录
     * @return int 用户IP
     */
    protected function isLogin(){
        $user = session('admin_user');
        if (empty($user)) {
            return 0;
        } else {
            return session('admin_user_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
        }
    }

    /**
     * 后台模板显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * @return void
     */
    protected function adminDisplay($templateFile='') {
        //获取菜单
        $menuList = D('Admin/Menu')->getMenu($this->loginUserInfo,$this->infoModule['cutNav']['url'],$this->infoModule['cutNav']['complete']);
        $this->assign('menuList',$menuList);
        //复制当前URL
        $this->assign('self',__SELF__);
        //嵌套模板
        $common = $this->fetch(APP_PATH.'Admin/View/common.html');
        $tpl = $this->fetch($templateFile);
        echo str_replace('<!--common-->', $tpl, $common);
    }
}