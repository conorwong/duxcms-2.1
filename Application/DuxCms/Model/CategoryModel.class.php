<?php
namespace DuxCms\Model;
use Think\Model;
/**
 * 栏目操作
 */
class CategoryModel extends Model {
    //完成
    protected $_auto = array (
        array('show','intval',3,'function'),
        array('sequence','intval',3,'function'),
        array('name','htmlspecialchars',3,'function'),
        array('urlname','getUrlName',3,'callback'),
        array('class_id','intval',2,'function'),
     );
    //验证
    protected $_validate = array(
        array('name','1,200', '栏目名称只能为1~200个字符', 1 ,'length'),
        array('class_tpl','1,200', '栏目模板未选择', 1 ,'length'),
        array('class_id','require', '栏目ID获取不正确', 1 ,'regex',2),
        array('parent_id','parentCheck', '上级栏目关系选择错误', 1 ,'callback',2),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $classId=0){
        import("Common.Util.Category");
        $data = $this->loadData($where);
        $cat = new \Common\Util\Category(array('class_id', 'parent_id', 'name', 'cname'));
        $data=$cat->getTree($data, intval($classId));
        //获取内容模型
        $modelList = getAllService('ContentModel','');
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $modelInfo = $modelList[$value['app']];
                $data[$key]['model_name'] = $modelInfo['name'];
            }
        }
        return $data;
    }

    /**
     * 获取列表(前台调用)
     * @return array 列表
     */
    public function loadData($where = array(), $limit = 0 ,$order="sequence ASC , class_id ASC"){
        $pageList = $this->where($where)->limit($limit)->order($order)->select();
        $list=array();
        if(!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value) {
                $list[$key]=$value;
                $list[$key]['curl'] = D('DuxCms/Category')->getUrl($value);
                $list[$key]['i'] = $i++;
            }
        }
        return $list;
    }

    /**
     * 获取栏目数量
     * @return array 列表
     */
    public function countList($where = array()){
        return $this->where($where)->count();
    }

    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId)
    {
        $map = array();
        $map['class_id'] = $classId;
        return $this->where($map)->find();
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
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
            return $this->add($data);
        }
        if($type == 'edit'){
            if(empty($data['class_id'])){
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
     * 删除信息
     * @param int $classId ID
     * @return bool 删除状态
     */
    public function delData($classId)
    {
        $map = array();
        $map['class_id'] = $classId;
        return $this->where($map)->delete();
    }

    /**
     * 栏目拼音转换
     * @return string 栏目拼音
     */
    public function getUrlName()
    {
        //获取变量
        $name = I('post.name');
        $urlName = I('post.urlname');
        $classId = I('post.class_id');
        //生成URL
        if (empty($urlName))
        {
            $pinyin = new \Common\Util\Pinyin();
            $name = preg_replace('/\s+/', '-', $name);
            $pattern = '/[^\x{4e00}-\x{9fa5}\d\w\-]+/u';
            $name = preg_replace($pattern, '', $name);
            $urlName = substr($pinyin->output($name, true),0,30);
            $urlName = trim($urlName,'-');
        }
        //返回数据
        $where = array();
        if (!empty($classId))
        {
            $where['class_id'] = array('NEQ',$classId);
        }
        $where['urlname'] = $urlName;
        $info = $this->getWhereInfo($where); 
        if (empty($info))
        {
            return $urlName;
        }
        else
        {
            return $urlName.substr(unique_number(),8);
        }
    }
    /**
     * 检查上级栏目
     * @return string 栏目拼音
     */

    public function parentCheck()
    {
        //获取变量
        $classId = I('post.class_id');
        $parentId = I('post.parent_id');

        //判断空上级
        if(!$parentId){
            return true;
        }
        // 分类检测
        if ($classId == $parentId){
            $this->error = '不可以将当前栏目设置为上一级栏目';
            return false;
        }

        $cat = $this->loadList('',$classId);
        if(empty($cat)){
            return true;
        }
        foreach ($cat as $vo) {
            if ($parentId == $vo['class_id']) {
                $this->error = '不可以将上一级栏目移动到子栏目';
                return false;
            }
        }
        return true;

    }

    /**
     * 获取菜单面包屑
     * @param int $classId 菜单ID
     * @return array 菜单表列表
     */
    public function loadCrumb($classId)
    {
        import("Common.Util.Category");
        $data = $this->loadData($where);
        $cat = new \Common\Util\Category(array('class_id', 'parent_id', 'name', 'cname'));
        $data = $cat->getPath($data, $classId);
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $data[$key] = $value;
                $data[$key]['url'] = $this->getUrl($value);
            }
        }
        return $data;
    }

    /**
     * 获取子栏目ID
     * @param array $classId 当前栏目ID
     * @return string 子栏目ID
     */
    public function getSubClassId($classId)
    {
        $data = $this->loadList('', $classId);
        if(empty($data)){
            return;
        }
        $list=array();
        foreach ($data as $value) {
            if($value['show']){
                $list[]=$value['class_id'];
            }
        }
        return implode(',', $list);
        
    }

    /**
     * 获取栏目URL
     * @param int $info 栏目信息
     * @return bool 删除状态
     */
    public function getUrl($info)
    {
        return url($info['app'].'/Category/index',array('class_id'=>$info['class_id'],'urlname'=>$info['urlname']));
    }

}
