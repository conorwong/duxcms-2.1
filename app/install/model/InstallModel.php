<?php
namespace app\install\model;
use app\base\model\BaseModel;
/**
 * 安装数据处理
 */
class InstallModel extends BaseModel
{
    /**
     * 运行sql
     * @param array $sqlArray sql数组
     * @return array 状态
     */
    public function runSql($sqlArray = array())
    {
        foreach ($sqlArray as $sql) {
            if (@$this->execute($sql) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 创建数据库
     * @param array $sqlArray sql数组
     * @return array 状态
     */
    public function createDB($name){
        $sql="CREATE DATABASE IF NOT EXISTS `".$name."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        return $this->execute($sql);
    }
}
?>