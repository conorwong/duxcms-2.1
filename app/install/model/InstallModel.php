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
     * @param array $data 数据库配置
     * @param array $sqlArray sql数组
     * @return array 状态
     */
    public function runSql($data, $sqlArray = array())
    {
        $model = new cpModel($data);
        foreach ($sqlArray as $sql) {
            if (!@$model->db->query($sql)) {
                return false;
            }
        }
        return true;
    }
    public function create_db($data){
        $model=new cpModel($data);
        $sql="CREATE DATABASE IF NOT EXISTS `".$data['DB_NAME']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        return @$model->query($sql);
    }
    public function sql_db($data,$sql){
        $model=new cpModel($data);
        return @$model->query($sql);
    }
}
?>