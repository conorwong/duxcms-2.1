<?php
namespace DuxCms\Model;
use Think\Model;
/**
 * 标签关系表操作
 */
class TagsHasModel extends Model {
    //完成
    protected $_auto = array (
        array('content_id','intval',3,'function'),
        array('tag_id','intval',3,'function'),
     );

    /**
     * 获取统计
     * @param array $map 统计条件
     * @return array 列表
     */
    public function countList($map){
        return  $this->count($map);
    }

    /**
     * 增加信息
     * @return bool 状态
     * @param array $data 更新数据
     */
    public function addData($data){
        if(!$data){
            return false;
        }
        return $this->data($data)->add();
    }

    /**
     * 删除信息
     * @param array $map 删除条件
     * @return bool 删除状态
     */
    public function delData($map)
    {
        return $this->where($map)->delete();
    }



    /**
     * 获取内容列表
     * @return array 列表
     */
    public function loadContentList($where, $limit){
        $data   = $this->distinct(true)
                    ->field('C.*,D.name as class_name,D.app,D.urlname as class_urlname')
                    ->table("__TAGS_HAS__ as A")
                    ->join('__TAGS__ as B ON B.tag_id = A.tag_id')
                    ->join('__CONTENT__ as C ON C.content_id = A.content_id')
                    ->join('__CATEGORY__ as D ON D.class_id = C.class_id')
                    ->where($where)
                    ->limit($limit)
                    ->order('B.tag_id DESC')
                    ->select();
        return $data;
    }

    /**
     * 获取内容统计
     * @return array 列表
     */
    public function countContentList($where){
        $data   = $this->distinct(true)
                    ->table("__TAGS_HAS__ as A")
                    ->join('__TAGS__ as B ON B.tag_id = A.tag_id')
                    ->join('__CONTENT__ as C ON C.content_id = A.content_id')
                    ->join('__CATEGORY__ as D ON D.class_id = C.class_id')
                    ->where($where)
                    ->count();
        return $data;
    }

}
