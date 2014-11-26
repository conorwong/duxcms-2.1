<?php
namespace DuxCms\Controller;
use Admin\Controller\AdminController;
/**
 * 碎片管理
 */

class AdminFragmentController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '碎片管理',
                'description' => '管理网站调用自定义变量',
                ),
            'menu' => array(
                    array(
                        'name' => '碎片列表',
                        'url' => U('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '添加碎片',
                        'url' => U('add'),
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('碎片列表'=>U());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',D('Fragment')->loadList());
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('碎片列表'=>U('index'),'添加'=>U());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->adminDisplay('info');
        }else{
            $model = D('Fragment');
            if($model->saveData('add')){
                $this->success('碎片添加成功！');
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('碎片添加失败');
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
        $model = D('Fragment');
        if(!IS_POST){
            $fragmentId = I('get.fragment_id',0,'intval');
            if(empty($fragmentId)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $info = $model->getInfo($fragmentId);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('碎片列表'=>U('index'),'修改'=>U('',array('fragment_id'=>$fragmentId)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            if($model->saveData('edit')){
                $this->success('碎片修改成功！');
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('碎片修改失败');
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
        $fragmentId = I('post.data');
        if(empty($fragmentId)){
            $this->error('参数不能为空！');
        }
        //获取用户数量
        $map = array();
        $map['fragment_id'] = $fragmentId;
        if(D('Fragment')->delData($fragmentId)){
            $this->success('碎片删除成功！');
        }else{
            $this->error('碎片删除失败！');
        }
    }
    


}

