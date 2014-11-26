<?php
namespace DuxCms\Model;
use Think\Model;
/**
 * 扩展模型字段操作
 */
class FieldExpandModel extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array()){
        $data = $this->table("__FIELD__ as A")
                    ->join('__FIELD_EXPAND__ as B ON A.field_id = B.field_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->select();
        return $data;
    }

    /**
     * 获取信息
     * @param int $FieldId ID
     * @return array 信息
     */
    public function getInfo($FieldId = 1)
    {
        $map = array();
        $map['A.field_id'] = $FieldId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->table("__FIELD__ as A")
                    ->join('__FIELD_EXPAND__ as B ON A.field_id = B.field_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        
        //事务处理
        $this->startTrans();
        $model = D('DuxCms/Field');
        $fieldId = $model->saveData($type);
        if(!$fieldId){
            $this->error = $model->getError();
            $this->rollback();
            return false;
        }
        //分表处理
        $data = $this->create();
        if(!$data){
            $this->rollback();
            return false;
        }
        if($type == 'add'){
            //写入数据
            $this->field_id = $fieldId;
            $status = $this->add();
            if($status){
                $this->commit();
            }else{
                $this->rollback();
            }
            return $status;
        }
        if($type == 'edit'){
            //修改数据
            $status = $this->where('field_id='.$data['field_id'])->save();
            if($status === false){
                $this->rollback();
                return false;
            }
            $this->commit();
            return true;
        }
        $this->rollback();
        return false;
    }

    /**
     * 删除信息
     * @param int $FieldId ID
     * @return bool 删除状态
     */
    public function delData($fieldId)
    {
        //获取信息
        $info = $this->getWhereInfo($map);
        if(!$info){
            $this->error = '数据不存在！';
            return false;
        }
        $this->startTrans();
        $model = D('DuxCms/Field');
        $status = $model->delData($fieldId);
        if(!$status){
            $this->error = $model->getError();
            $this->rollback();
            return false;
        }
        //删除数据
        $map = array();
        $map['field_id'] = $fieldId;
        $status = $this->where($map)->delete();
        if($status){
            $this->commit();
        }else{
            $this->rollback();
        }
        return $status;
    }

}
