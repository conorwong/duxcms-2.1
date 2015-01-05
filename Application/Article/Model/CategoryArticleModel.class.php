<?php
namespace Article\Model;
use Think\Model;
/**
 * 栏目操作
 */
class CategoryArticleModel extends Model {
    //验证
    protected $_validate = array(
        array('content_tpl','1,200', '内容模板未选择', 0 ,'length',3),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $classId=0){
        
        $data = $this->loadData($where);
        import("Common.Util.Category");
        $cat = new \Common\Util\Category(array('class_id', 'parent_id', 'name', 'cname'));
        $data = $cat->getTree($data, intval($classId));
        return $data;
    }

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadData($where = array(), $limit = 0 ,$order="A.sequence ASC , A.class_id ASC"){
        $pageList = $this->table("__CATEGORY__ as A")
                    ->join('__CATEGORY_ARTICLE__ as B ON A.class_id = B.class_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->limit($limit)
                    ->order($order)
                    ->select();
        //处理数据类型
        $list=array();
        if(!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value) {
                $list[$key]=$value;
                $list[$key]['curl'] = D('Article/CategoryArticle')->getUrl($value);
                $list[$key]['i'] = $i++;
            }
        }
        return $list;
    }

    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId)
    {
        $map = array();
        $map['A.class_id'] = $classId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->table("__CATEGORY__ as A")
                    ->join('__CATEGORY_ARTICLE__ as B ON A.class_id = B.class_id')
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
        $classId = D('DuxCms/Category')->saveData($type);
        if(!$classId){
            $this->error = D('DuxCms/Category')->getError();
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

    /**
     * 获取栏目URL
     * @param int $info 栏目信息
     * @return bool 删除状态
     */
    public function getUrl($info)
    {
        return url('Article/Category/index',array('class_id'=>$info['class_id']));
    }

    

}
