<?php
namespace DuxCms\Service;
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
            $where['parent_id'] = $data['parent_id'];
        }
        //指定栏目
        if(!empty($data['class_id'])){
            $where['class_id'] = array('in',$data['class_id']);
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
        $model = D('DuxCms/Category');
        return $model->loadData($where,$data['limit'],$order);
	}

    /**
     * 内容列表
     */
    public function contentList($data){
        $where=array();
        //指定栏目内容
        if(!empty($data['class_id'])){
            $where['A.class_id'] = array('in',$data['class_id']);
        }
        //指定栏目下子栏目内容
        if ($data['type']=='sub'&&!empty($data['class_id'])) {
            $classIds = D('DuxCms/Category')->getSubClassId($data['class_id']);
            $where['A.class_id'] = array('in',$classIds);
        }
        //是否带形象图
        if (isset($data['image'])) {
            if($data['image'] == true)
            {
                $where['A.image'] = array('neq','');
            }else{
                $where['A.image'] = '';
            }
        }
        //调用APP内容
        if(!empty($data['module'])){
            $where['B.app'] = $data['module'];
        }
        //排除ID
        if(!empty($data['not_id'])){
            $where['A.content_id'] = array('not in',$data['not_id']);
        }
        //调用推荐位
        if(!empty($data['pos_id'])){
            $where['_string'] = 'find_in_set('.$data['pos_id'].',A.position) ';
        }
        //其他条件
        if (!empty($data['where'])) {
            $where['_string'] = $data['where'];
        }
        //调用数量
        if (empty($data['limit'])) {
            $data['limit'] = 10;
        }
        //内容排序
        if(empty($data['order'])){
            $data['order']='A.time DESC,A.content_id DESC';
        }
        //其他属性
        $where['status'] = 1;
        $model = D('DuxCms/Content');
        return D('DuxCms/Content')->loadList($where,$data['limit'],$data['order']);
    }

    /**
     * 碎片调用
     */
    public function fragment($data){
        $where=array();
        if(empty($data['mark'])){
            return ;
        }
        $where['label'] = $data['mark'];
        $info = D('DuxCms/Fragment')->getWhereInfo($where);
        if(empty($info)){
            return ;
        }
        return html_out(htmlspecialchars_decode($info['content']));
    }

    /**
     * 表单token
     */
    public function formToken($data){
        $where=array();
        if(empty($data['table'])){
            return ;
        }
        $where = array();
        $where['table'] = $data['table'];
        $formInfo = D('DuxCms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            return ;
        }
        return D('DuxCms/FieldsetForm')->setToken($data['table']);
    }

    /**
     * 内容链接调用
     */
    public function aurl($data){
        if(empty($data['content_id'])){
            return ;
        }
        $where=array();
        $where['content_id'] = $data['content_id'];
        $info = D('DuxCms/Content')->getWhereInfo($where);
        if(empty($info)){
            return ;
        }
        return D('DuxCms/Content')->getUrl($info);
    }

    /**
     * 栏目链接调用
     */
    public function curl($data){
        if(empty($data['class_id'])){
            return ;
        }
        $where=array();
        $where['class_id'] = $data['class_id'];
        $info = D('DuxCms/Category')->getWhereInfo($where);
        if(empty($info)){
            return ;
        }
        return D('DuxCms/Category')->getUrl($info);
    }

    /**
     * TAG列表调用
     */
    public function tagsList($data){

        $where = array();
        //解析TAG文字
        if(!empty($data['name'])){
            $name = explode(',', $data['name']);
            $whereName = array();
            foreach ($name as $value) {
                $whereName[] = array('eq', $value);
            }
            $where['name'] = array_merge($whereName, array('and'));
        }
        //数量
        if(empty($data['limit'])){
            $data['limit'] = 10;
        }
        //默认排序
        if(empty($data['order'])){
            $data['order'] = 'tag_id DESC';
        }
        return D('DuxCms/Tags')->loadList($where, $data['limit'], $data['order']);
    }

    /**
     * 表单列表调用
     */
    public function formList($data)
    {
        if(empty($data['table'])){
            return array();
        }
        //获取表单信息
        $where = array();
        $where['table'] = $data['table'];
        $formInfo = D('DuxCms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            return array();
        }
        //设置模型
        $model = D('DuxCms/FieldData');
        $model->setTable($formInfo['table']);
        //获取条件
        $where = array();
        if(!empty($formInfo['list_where'])){
            $where['_string'] = $formInfo['list_where'];
        }
        if(!empty($data['where'])){
            $where['_string'] = $data['where'];
        }
        if(empty($data['order'])){
            $data['order'] = 'data_id DESC';
        }
        if(empty($data['limit'])){
            $data['limit'] = 10;
        }
        //查询内容
        $list = $model->loadList($where,$data['limit'],$data['order']);
        //字段列表
        $where = array();
        $where['A.fieldset_id'] = $formInfo['fieldset_id'];
        $fieldList = D('DuxCms/FieldForm')->loadList($where);
         //格式化表单内容为基本数据
        $data = array();

        if(!empty($list)){
            foreach ($list as $key => $value) {
                $data[$key]=$value;
                foreach ($fieldList as $v) {
                    $data[$key][$v['field']] = D('DuxCms/FieldData')->revertField($value[$v['field']],$v['type'],$v['config']);
                }                
            }
        }
        return $data;
    }

}
