<?php
namespace Admin\Model;
use Think\Model;
/**
 * 数据库备份还原
 */

class DatabaseModel {

    public $backupDir = 'Backup/';
    public $tableList = array(); //表列表

    /**
     * 获取备份文件列表
     * @return array 文件列表
     */
    public function backupList()
    {
        $fileDir = ROOT_PATH . $this->backupDir;
        if(!is_dir($fileDir)){
            return false;
        }
        $listFile = glob($fileDir . '*.sql.*');
        if(is_array($listFile)){
            $list=array();
            foreach ($listFile as $key => $value) {
                $value = basename($value);
                $list[$key]['name'] = $value;
                $fileName = explode('-', $value);
                $list[$key]['time'] = $fileName[0].'-'.$fileName[1];
            }
        }
        return $list;
    }

    /**
     * 数据库检测
     */
    public function check() {
        if(C('DB_TYPE') <> 'mysql'){
            $this->error = '抱歉，数据库驱动非mysql时无法使用备份功能！';
            return false;
        }
        $list = $this->loadTableList();
        if(empty($list)){
            $this->error = '没有发现表';
            return false;
        }
        $this->tableList = $list;
        return true;
    }

    /**
     * 数据库列表
     */
    public function loadTableList(){
        $Db = M();
        return $Db->query('SHOW TABLE STATUS');
    }

    /**
     * 优化数据库
     */
    public function optimizeData(){
        if(!$this->check()){
            return false;
        }
        $list = $this->tableList;
        $tables = array();
        foreach ($list as $value) {
            $tables[] = $value['Name'];
        }
        $tables = implode('`,`', $tables);
        $Db = M();
        if($Db->query("OPTIMIZE TABLE `{$tables}`")){
            return true;
        } else {
            $this->error = '数据库优化失败，请稍后重试！';
            return false;
        }
    }

    /**
     * 修复数据库
     */
    public function repairData(){
        if(!$this->check()){
            return false;
        }
        $list = $this->tableList;
        $tables = array();
        foreach ($list as $value) {
            $tables[] = $value['Name'];
        }
        $tables = implode('`,`', $tables);
        $Db = M();
        if($Db->query("REPAIR TABLE `{$tables}`")){
            return true;
        } else {
            $this->error = '数据库修复失败，请稍后重试！';
            return false;
        }
    }

    /**
     * 备份数据库
     */
    public function backupData(){
        if(!$this->check()){
            return false;
        }
        $list = $this->tableList;
        //生成备份文件信息
        $file = array(
            'name' => date('Ymd-His', NOW_TIME),
            'part' => 1,
        );
        //设置备份配置
        $config = array(
            'path'     => ROOT_PATH . $this->backupDir,
            'part'     => 20971520,
            'compress' => 1,
            'level'    => 9,
        );
        //检查是否有正在执行的任务
        $lock = "{$config['path']}backup.lock";
        if(is_file($lock)){
            $this->error = '检测到有一个备份任务正在执行，请稍后再试！';
            return false;
        } else {
            //创建锁文件
            file_put_contents($lock, NOW_TIME);
        }
        //检查目录
        if(!is_dir($config['path'])){
            if(!mkdir($config['path'])){
                $this->error = '无法创建备份目录，请手动根目录创建Backup文件夹！';
                return false;
            }
        }
        //检查备份目录是否可写
        if(!is_writeable($config['path'])){
            $this->error = '备份目录不存在或不可写，请检查后重试！';
            return false;
        }
        //创建备份文件
        $Database = new \Org\Util\Database($file, $config);
        if(!$Database->create()){
            $this->error = '初始化失败，备份文件创建失败！';
            return false;
        }
        $start = 0;
        foreach ($list as $value) {
            $start = $Database->backup($value['Name'], $start);
            if($start === false){
                //删除文件锁
                unlink($lock);
                $this->error = '备份文件创建失败，请稍后再试！';
                return false;
            }
        }
        //删除文件锁
        unlink($lock);
        return true;
    }

    /**
     * 还原数据库
     */
    public function importData($time){
        $name  = $time . '-*.sql*';
        $path  = ROOT_PATH . $this->backupDir . $name;
        $files = glob($path);
        $list  = array();
        foreach($files as $name){
            $basename = basename($name);
            $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
            $gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
            $list[$match[6]] = array($match[6], $name, $gz);
        }
        ksort($list);
        //检测文件正确性
        $last = end($list);
        if(!count($list) === $last[0]){
            $this->error('备份文件可能已经损坏，请检查！');
        }
        //还原数据库
        $start = 0;
        foreach ($list as $value) {
            $file = $value;
            $config = array(
                'path'     => ROOT_PATH . $this->backupDir,
                'compress' => $value[2],
            );
            $db = new \Org\Util\Database($file, $config);
            $start = $db->import($start);
            if($start === false){
                $this->error = '恢复数据失败，请稍后再试！';
                return false;
            }
        }
        return true;
    }

    /**
     * 删除备份文件
     */
    public function delData($time){
        $name  = $time . '-*.sql*';
        $path  = ROOT_PATH . $this->backupDir . $name;
        array_map("unlink", glob($path));
        if(!count(glob($path))){
            return true;
        } else {
            return false;
        }
    }

}
