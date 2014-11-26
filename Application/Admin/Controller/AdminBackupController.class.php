<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
/**
 * 备份还原
 */
class AdminBackupController extends AdminController {

    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '备份还原',
                'description' => '备份还原整站数据库',
                ),
            'menu' => array(
                    array(
                        'name' => '备份列表',
                        'url' => U('index'),
                        'icon' => 'list',
                    ),
                ),
            'add' => array(
                    array(
                        'name' => '新建备份',
                        'url' => U('add'),
                    ),
                ),
            );
    }

	/**
     * 列表
     */
    public function index(){
        //查询数据
        $list = D('Database')->backupList();
        //位置导航
        $breadCrumb = array('备份列表'=>U());
        //模板传值
        $this->assign('breadCrumb',$breadCrumb);
        $this->assign('list',$list);
        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add(){
        if(!IS_POST){
            $breadCrumb = array('备份列表'=>U('index'),'新建'=>U());
            //查询数据
            $list = D('Database')->loadTableList();
            $this->assign('breadCrumb',$breadCrumb);
            $this->assign('name','新建');
            $this->assign('list',$list);
            $this->adminDisplay('info');
        }else{
            $type = I('post.type');
            switch ($type) {
                case 1:
                    $action = 'optimizeData';
                    break;
                case 2:
                    $action = 'repairData';
                    break;
                default:
                    $action = 'backupData';
                    break;
            }
            if(D('Database')->$action()){
                $this->success('数据库操作执行完毕！');
            }else{
                $msg = D('Database')->error;
                if(empty($msg)){
                    $this->error('数据库操作执行失败');
                }else{
                    $this->error($msg);
                }
                
            }
        }
    }

    /**
     * 导入
     */
    public function import(){
        $time = I('post.data');
        if(empty($time)){
            $this->error('参数不能为空！');
        }
        //获取备份数量
        if(D('Database')->importData($time)){
            $this->success('备份恢复成功！');
        }else{
            $msg = D('Database')->error;
            if(empty($msg)){
                $this->error('备份恢复失败！');
            }else{
                $this->error($msg);
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $time = I('post.data');
        if(empty($time)){
            $this->error('参数不能为空！');
        }
        //获取备份数量
        if(D('Database')->delData($time)){
            $this->success('备份删除成功！');
        }else{
            $msg = D('Database')->error;
            if(empty($msg)){
                $this->error('备份删除失败！');
            }else{
                $this->error($msg);
            }
        }
    }


}

