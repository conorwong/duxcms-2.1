<?php
namespace DuxCms\Controller;
use Admin\Controller\AdminController;
/**
 * 标签管理
 */

class AdminTagsController extends AdminController {
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '标签管理',
                'description' => '管理网站内容标签',
                ),
            'menu' => array(
                    array(
                        'name' => '标签列表',
                        'url' => U('index'),
                        'icon' => 'list',
                    ),
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        //查询数据
        $count = D('Tags')->countList($where);
        $limit = $this->getPageLimit($count,20);
        $list = D('Tags')->loadList($where,$limit);
        $breadCrumb = array('标签列表'=>U());
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->assign('page',$this->getPageShow($pageMaps));
        $this->adminDisplay();
    }

    /**
     * 批量操作
     */
    public function batchAction(){
        
        $type = I('post.type',0,'intval');
        $ids = I('post.ids');
        if(empty($type)){
            $this->error('请选择操作！');
        }
        if(empty($ids)){
            $this->error('请先选择操作项目！');
        }
        foreach ($ids as $id) {
            switch ($type) {
                case 1:
                    //删除
                    D('DuxCms/Tags')->delData($id);
                    break;
            }
        }
        $this->success('批量操作执行完毕！');
    }
    


}

