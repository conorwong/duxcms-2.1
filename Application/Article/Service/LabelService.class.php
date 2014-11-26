<?php
namespace Article\Service;
/**
 * 标签接口
 */
class LabelService{
	/**
	 * 栏目列表
	 */
	public function categoryList($data){
        $where='';
        //上级栏目
        if(isset($data['parent_id'])){
            $where['A.parent_id'] = $data['parent_id'];
        }
        //指定栏目
        if(!empty($data['class_id'])){
            $where['A.class_id'] = array('in',$data['class_id']);
        }
        //栏目属性
        if(isset($data['type'])){
            if($data['type']){
                $where['B.type'] = 1;
            }else{
                $where['B.type'] = 0;
            }
        }
        //其他条件
        if(!empty($data['where'])){
            $where['_string'] = $data['where'];
        }
        //排序
        if(!empty($data['order'])){
            $order=$data['order'];
        }
        //其他属性
        $where['show'] = 1;
        $model = D('Article/CategoryArticle');
        return $model->loadData($where,$data['limit'],$order);
	}
	


}
