<?php
namespace Admin\Model;
use Think\Model;
/**
 * 操作记录
 */
class AdminLogModel extends Model {
    //完成
    protected $_auto = array (
        array('time','time',1,'function'),
        array('ip','get_client_ip',1,'function'),
        array('app',MODULE_NAME,1,'string'),
        array('user_id','1',1,'string'),
        
     );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 0){
        $data   = $this->table("__ADMIN_LOG__ as A")
                    ->join('__ADMIN_USER__ as B ON B.user_id = B.user_id')
                    ->field('A.*,B.username')
                    ->where($where)
                    ->limit($limit)
                    ->order('A.log_id desc')
                    ->select();
        return $data;

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
        //只保留5000条数据
        $count = $this->countList();
        if($count>5000){
            $this->where('log_id <> 0')->order('log_id asc')->limit('1')->delete();
        }
        //增加记录
        $this->create($data);
        return $this->add();
    }

}
