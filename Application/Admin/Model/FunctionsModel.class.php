<?php
namespace Admin\Model;
use Think\Model;
/**
 * 应用操作
 */
class FunctionsModel extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        $list = glob('./Application/*/Conf/config.php');
        $configArray = array();
        foreach ($list as $file) {
            //解析模块名
            $fileName = explode('/', $file);
            $fileName = $fileName[2];
            $configArray[$fileName] = $this->getInfo($fileName);
        }
        $configArray = array_order($configArray,'APP_SYSTEM');
        return $configArray;

    }

    /**
     * 添加APP信息
     * @param string $app 应用名
     */
    public function getInfo($app){
        $file = APP_PATH.'/'. $app .'/Conf/config.php';
        $info = load_config($file);
        if(empty($info)){
            return ;
        }
        $info['APP'] = $app;
        $info['APP_DIR'] = APP_PATH.$app;
        if($info['APP_SYSTEM']){
            $info['APP_STATE'] = 1;
            $info['APP_INSTALL'] = 1;
        }
        return $info;
    }

}
