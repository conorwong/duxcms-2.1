<?php
namespace Admin\Model;
use Think\Model;
/**
 * 用户组操作
 */
class AdminGroupModel extends Model {
    //完成
    protected $_auto = array (
         array('status','intval',3,'function'),
         array('name','htmlspecialchars',3,'function'),
     );
    //验证
    protected $_validate = array(
        array('name','1,20', '用户组名称只能为1~20个字符', 0 ,'length',3),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        $data   = $this->select();
        return $data;
    }

    /**
     * 获取信息
     * @param int $groupId ID
     * @return array 信息
     */
    public function getInfo($groupId = 1)
    {
        $map = array();
        $map['group_id'] = $groupId;
        return $this->where($map)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        $data = $this->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            return $this->add();
        }
        if($type == 'edit'){
            if(empty($data['group_id'])){
                return false;
            }
            $status = $this->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 更新权限
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function savePurviewData(){
        $this->_auto = array();
        $data = $this->create();
        $this->menu_purview = serialize($this->menu_purview);
        $this->base_purview = serialize($this->base_purview);
        $status = $this->save();
        if($status === false){
            return false;
        }
        return true;
    }

    /**
     * 删除信息
     * @param int $groupId ID
     * @return bool 删除状态
     */
    public function delData($groupId)
    {
        $map = array();
        $map['group_id'] = $groupId;
        return $this->where($map)->delete();
    }

}
