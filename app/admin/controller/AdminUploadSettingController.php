<?php
namespace app\admin\controller;
use app\admin\controller\AdminController;
/**
 * 上传配置
 */

class AdminUploadSettingController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '上传配置',
                'description' => '配置多种上传规则',
                ),
            'menu' => array(
                    array(
                        'name' => '配置列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '添加配置',
                        'url' => url('add'),
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('配置列表'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',target('ConfigUpload')->loadList());
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('配置列表'=>url('index'),'添加'=>url());
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','添加');
            $this->adminDisplay('info');
        }else{
            $model = target('ConfigUpload');
            if($model->saveData('add')){
                $this->success('配置添加成功！');
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('配置添加失败');
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
        $model = target('ConfigUpload');
        if(!IS_POST){
            $id = request('get.id',0,'intval');
            if(empty($id)){
                $this->error('参数不能为空！');
            }
            //获取记录
            $info = $model->getInfo($id);
            if(!$info){
                $this->error($model->getError());
            }
            $breadCrumb = array('配置列表'=>url('index'),'修改'=>url('',array('id'=>$id)));
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','修改');
            $this->assign('info',$info);
            $this->adminDisplay('info');
        }else{
            if($model->saveData('edit')){
                $this->success('配置修改成功！');
            }else{
                $msg = $model->getError();
                if(empty($msg)){
                    $this->error('配置修改失败');
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
        $id = request('post.data');
        if(empty($id)){
            $this->error('参数不能为空！');
        }
        if($id == 1) {
            $this->error('默认配置无法删除！');
        }
        $map = array();
        $map['id'] = $id;
        if(target('ConfigUpload')->delData($id)){
            $this->success('配置删除成功！');
        }else{
            $this->error('配置删除失败！');
        }
    }
    


}

