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
            $info = load_config($file,'php');
            $configArray[$fileName] = $info;
            $configArray[$fileName]['APP'] = $fileName;
            $configArray[$fileName]['APP_DIR'] = './Application/'.$fileName;
            if($info['APP_SYSTEM']){
                $configArray[$fileName]['APP_STATE'] = 1;
                $configArray[$fileName]['APP_INSTALL'] = 1;
            }
        }
        $configArray = array_order($configArray,'APP_SYSTEM');
        return $configArray;

    }
    /**
     * 获取数量
     * @return int 数量
     */
    public function countList($where = array()){
        return $this->table("__ADMIN_LOG__ as A")
                    ->join('__ADMIN_USER__ as B ON B.user_id = B.user_id')
                    ->where($where)
                    ->count();
    }

    /**
     * 添加信息
     * @param string $log 增加数据
     * @return bool 更新状态
     */
    public function addData($log){
        $data = array();
        $data['content'] = $log;
        if(empty($data)){
            return false;
        }
        //只保留500条数据
        $count = $this->countList();
        if($count>500){
            $this->order('log_id asc')->limit('1')->delete();
        }
        //增加记录
        $this->create($data);
        return $this->add();
    }

}
