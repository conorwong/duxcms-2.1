<?php
namespace DuxCms\Model;
use Think\Model;
/**
 * 安全统计
 */
class SafeModel {

    /**
     * 获取安全测试结果
     */
    public function getList()
    {
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
        return $safeArray;
    }

    /**
     * 获取安全评分
     */
    public function getCount()
    {
        $list = $this->getList();
        $count = 0;
        foreach ($list as $value) {
            if($value){
                $count = $count + 20;
            }
        }
        return $count;
    }

}
