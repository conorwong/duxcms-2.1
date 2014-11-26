<?php
namespace Page\Model;
use Think\Model;
/**
 * 栏目操作
 */
class CategoryPageModel extends Model {
    //验证
    protected $_validate = array(
        array('content','empty', '请填写页面内容', 1 ,'function',3),
    );

    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId)
    {
        $map = array();
        $map['A.class_id'] = $classId;
        return $this->table("__CATEGORY__ as A")
                    ->join('__CATEGORY_PAGE__ as B ON A.class_id = B.class_id')
                    ->field('B.*,A.*')
                    ->where($map)
                    ->find();
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->table("__CATEGORY__ as A")
                    ->join('__CATEGORY_PAGE__ as B ON A.class_id = B.class_id')
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
        //事务总表处理
        $this->startTrans();
        $model = D('DuxCms/Category');
        $classId = $model->saveData($type);
        if(!$classId){
            $this->error = $model->getError();
            return false;
        }
        //分表处理
        $data = $this->create();
        if(!$data){
            $this->rollback();
            return false;
        }
        if($type == 'add'){
            $this->class_id = $classId;
            $status = $this->add();
            if($status){
                $this->commit();
            }else{
                $this->rollback();
            }
            return $status;
        }
        if($type == 'edit'){
            $status = $this->where('class_id='.$data['class_id'])->save();
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
     * @param int $classId ID
     * @return bool 删除状态
     */
    public function delData($classId)
    {
        //总表
        D('DuxCms/Category')->delData($classId);
        //分表
        $map = array();
        $map['class_id'] = $classId;
        return $this->where($map)->delete();
    }

}
