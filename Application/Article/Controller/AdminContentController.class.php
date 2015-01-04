<?php
namespace Article\Controller;
use Admin\Controller\AdminController;
/**
 * 文章列表
 */

class AdminContentController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '文章管理',
                'description' => '管理网站的所有文章',
                ),
            'menu' => array(
                    array(
                        'name' => '文章列表',
                        'url' => U('index'),
                        'icon' => 'list',
                    ),
                    
                ),
            'add' => array(
                    array(
                        'name' => '添加文章',
                        'url' => U('add'),
                        'icon' => 'plus',
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        //筛选条件
        $where = array();
        $keyword = I('request.keyword','');
        $classId = I('request.class_id',0,'intval');
        $positionId = I('request.position_id',0,'intval');
        $status = I('request.status',0,'intval');
        if(!empty($keyword)){
            $where['A.title'] = array('like','%'.$keyword.'%');
        }
        if(!empty($classId)){
            $where['C.class_id'] = $classId;
        }
        if(!empty($positionId)){
            $where['_string'] = 'find_in_set('.$positionId.',position) ';
        }
        if(!empty($status)){
            switch ($status) {
                case '1':
                    $where['A.status'] = 1;
                    break;
                case '2':
                    $where['A.status'] = 0;
                    break;
            }
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['status'] = $status;
        $pageMaps['class_id'] = $classId;
        $pageMaps['position_id'] = $positionId;
        //查询数据
        $count = D('ContentArticle')->countList($where);
        $limit = $this->getPageLimit($count,30);
        $list = D('ContentArticle')->loadList($where,$limit);
        //位置导航
        $breadCrumb = array('文章列表'=>U());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->assign('categoryList',D('DuxCms/Category')->loadList());
        $this->assign('positionList',D('DuxCms/Position')->loadList());
        $this->assign('pageMaps',$pageMaps);
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('文章列表'=>U('index'),'文章添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('categoryList',D('DuxCms/Category')->loadList());
            $this->assign('tplList',D('Admin/Config')->tplList());
            $this->assign('positionList',D('DuxCms/Position')->loadList());
            $this->assign('default_config',current_config());
            $this->adminDisplay('info');
        }else{
            if(D('ContentArticle')->saveData('add')){
                $this->success('文章添加成功！');
            }else{
                $msg = D('ContentArticle')->getError();
                if(empty($msg)){
                    $this->error('文章添加失败');
                }else{
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(!IS_POST){
            $contentId = I('get.content_id','','intval');
            if(empty($contentId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $model = D('ContentArticle');
            $info = $model->getInfo($contentId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('文章列表'=>U('index'),'文章修改'=>U('',array('content_id'=>$contentId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',$info);
            $this->assign('categoryList',D('DuxCms/Category')->loadList());
            $this->assign('tplList',D('Admin/Config')->tplList());
            $this->assign('positionList',D('DuxCms/Position')->loadList());
            $this->adminDisplay('info');
        }else{
            if(D('ContentArticle')->saveData('edit')){
                $this->success('文章修改成功！');
            }else{
                $msg = D('ContentArticle')->getError();
                if(empty($msg)){
                    $this->error('文章修改失败');
                }else{
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $contentId = I('post.data',0,'intval');
        if(empty($contentId)){
            $this->error('参数不能为空！');
        }
        if(D('ContentArticle')->delData($contentId)){
            $this->success('文章删除成功！');
        }else{
            $this->error('文章删除失败！');
        }
    }

    /**
     * 批量操作
     */
    public function batchAction(){
        
        $type = I('post.type',0,'intval');
        $ids = I('post.ids');
        $classId = I('post.class_id',0,'intval');
        if(empty($type)){
            $this->error('请选择操作！');
        }
        if(empty($ids)){
            $this->error('请先选择操作项目！');
        }
        if($type == 3){
            if(empty($classId)){
                $this->error('请选择操作栏目！');
            }
        }
        foreach ($ids as $id) {
            $data = array();
            $data['content_id'] = $id;
            switch ($type) {
                case 1:
                    //发布
                    $data['status'] = 1;
                    D('DuxCms/Content')->editData($data);
                    break;
                case 2:
                    //草稿
                    $data['status'] = 0;
                    D('DuxCms/Content')->editData($data);
                    break;
                case 3:
                    $data['class_id'] = $classId;
                    D('DuxCms/Content')->editData($data);
                    break;
                case 4:
                    //删除
                    D('ContentArticle')->delData($id);
                    break;
            }
        }
        $this->success('批量操作执行完毕！');

    }


}

