<?php
namespace Article\Controller;
use Admin\Controller\AdminController;
/**
 * 栏目管理
 */
class AdminCategoryController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        $menu = A('DuxCms/AdminCategory');
        $menu = $menu->infoModule;
        $menu['cutNav'] = array(
                'url' => U('DuxCms/AdminCategory/index'),
                'complete' => false,
                );
        return $menu;
        
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('栏目列表'=>U('DuxCms/AdminCategory/index'),'文章栏目添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('categoryList',D('DuxCms/Category')->loadList());
            $this->assign('tplList',D('Admin/Config')->tplList());
            $this->assign('expandList',D('DuxCms/FieldsetExpand')->loadList());
            $this->assign('uploadList',D('Admin/ConfigUpload')->loadList());
            $this->assign('default_config',current_config());
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = MODULE_NAME;
            if(D('CategoryArticle')->saveData('add')){
                $this->success('栏目添加成功！');
            }else{
                $msg = D('CategoryArticle')->getError();
                if(empty($msg)){
                    $this->error('栏目添加失败');
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
            $classId = I('get.class_id','','intval');
            if(empty($classId)){
                $this->error('参数不能为空！');
            }
            $model = D('CategoryArticle');
            $info = $model->getInfo($classId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('栏目列表'=>U('DuxCms/AdminCategory/index'),'文章栏目修改'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('categoryList',D('DuxCms/Category')->loadList());
            $this->assign('tplList',D('Admin/Config')->tplList());
            $this->assign('expandList',D('DuxCms/FieldsetExpand')->loadList());
            $this->assign('uploadList',D('Admin/ConfigUpload')->loadList());
            $this->assign('default_config',current_config());
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = MODULE_NAME;
            if(D('CategoryArticle')->saveData('edit')){
                $this->success('栏目修改成功！');
            }else{
                $msg = D('CategoryArticle')->getError();
                if(empty($msg)){
                    $this->error('栏目修改失败');
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
        $classId = I('post.data');
        if(empty($classId)){
            $this->error('参数不能为空！');
        }
        //判断子栏目
        if(D('DuxCms/Category')->loadList('', $classId)){
            $this->error('请先删除子栏目！');
        }
        //判断栏目下内容
        $where = array();
        $where['A.class_id'] = $classId;
        $contentNum = D('ContentArticle')->countList($where);
        if(!empty($contentNum)){
            $this->error('请先删除该栏目下的内容！');
        }
        //删除栏目操作
        if(D('CategoryArticle')->delData($classId)){
            $this->success('栏目删除成功！');
        }else{
            $msg = D('CategoryArticle')->getError();
            if(empty($msg)){
                $this->error('栏目删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }

}

