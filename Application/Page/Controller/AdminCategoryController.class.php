<?php
namespace Page\Controller;
use Admin\Controller\AdminController;
/**
 * 页面管理
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
            $breadCrumb = array('页面列表'=>U('DuxCms/AdminCategory/index'),'页面添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->assign('categoryList',D('DuxCms/Category')->loadList());
            $this->assign('tplList',D('Admin/Config')->tplList());
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = MODULE_NAME;
            $model = D('CategoryPage');
            if($model->saveData('add')){
                $this->success('页面添加成功！');
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('页面添加失败');
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
            $model = D('CategoryPage');
            $info = $model->getInfo($classId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('页面列表'=>U('DuxCms/AdminCategory/index'),'页面修改'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('categoryList',D('DuxCms/Category')->loadList());
            $this->assign('tplList',D('Admin/Config')->tplList());
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            $_POST['app'] = MODULE_NAME;
            $model = D('CategoryPage');
            if($model->saveData('edit')){
                $this->success('页面修改成功！');
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('页面修改失败');
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
        //判断子页面
        if(D('DuxCms/Category')->loadList('', $classId)){
            $this->error('请先删除子页面！');
        }
        //删除页面操作
        $model = D('CategoryPage');
        if($model->delData($classId)){
            $this->success('页面删除成功！');
        }else{
            $msg = $model->getError();
            if(empty($msg)){
                $this->error('页面删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }

}

