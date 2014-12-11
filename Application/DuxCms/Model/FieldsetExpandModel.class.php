<?php
namespace DuxCms\Model;
use Think\Model;
/**
 * 扩展模型操作
 */
class FieldsetExpandModel extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array()){
        $data = $this->table("__FIELDSET__ as A")
                    ->join('__FIELDSET_EXPAND__ as B ON A.fieldset_id = B.fieldset_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->select();
        return $data;
    }

    /**
     * 获取信息
     * @param int $fieldsetId ID
     * @return array 信息
     */
    public function getInfo($fieldsetId)
    {
        $map = array();
        $map['A.fieldset_id'] = $fieldsetId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->table("__FIELDSET__ as A")
                    ->join('__FIELDSET_EXPAND__ as B ON A.fieldset_id = B.fieldset_id')
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
        $model = D('DuxCms/Fieldset');
        $fieldsetId = $model->saveData($type);
        if(!$fieldsetId){
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
            $this->fieldset_id = $fieldsetId;
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
            $status = $this->where('fieldset_id='.$data['fieldset_id'])->save();
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
     * @param int $fieldsetId ID
     * @return bool 删除状态
     */
    public function delData($fieldsetId)
    {
        $this->startTrans();
        $model = D('DuxCms/Fieldset');
        $status = $model->delData($fieldsetId);
        if(!$status){
            $this->error = $model->getError();
            $this->rollback();
            return false;
        }
        //删除数据
        $map = array();
        $map['fieldset_id'] = $fieldsetId;
        $status = $this->where($map)->delete();
        if($status){
            $this->commit();
        }else{
            $this->rollback();
        }
        return $status;
    }

    /**
     * 获取还原后扩展数据
     * @param int $fieldsetId 字段集ID
     * @param int $dataId 数据ID
     * @return array 数据
     */
    public function getDataInfo($fieldsetId,$dataId)
    {
        //获取模型信息
        $fieldsetInfo = D('DuxCms/FieldsetExpand')->getInfo($fieldsetId);
        if(empty($fieldsetInfo)){
            return ;
        }
        //获取字段内容
        D('DuxCms/FieldData')->setTable($fieldsetInfo['table']);
        $extInfo = D('DuxCms/FieldData')->getInfo($dataId);
        if(empty($extInfo)){
            return ;
        }
        //获取字段列表
        $where = array();
        $where['A.fieldset_id'] = $fieldsetId;
        $fieldList = D('DuxCms/FieldExpand')->loadList($where); 
        if(empty($fieldList)){
            return ;
        }
        $extArray = array();
        foreach ($fieldList as $value) {
            $extArray[$value['field']] = D('DuxCms/FieldData')->revertField($extInfo[$value['field']],$value['type'],$value['config']);
        }
        return $extArray;

    }

}
