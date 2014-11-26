<?php
namespace DuxCms\Controller;
use Admin\Controller\AdminController;
/**
 * 栏目管理
 */
class AdminCategoryController extends AdminController
{
    /**
     * 当前模块参数
     */
    public function _infoModule()
    {
        $data = array('info' => array('name' => '栏目管理',
                'description' => '管理网站全部栏目',
                ),
            'menu' => array(
                array('name' => '栏目列表',
                    'url' => U('DuxCms/AdminCategory/index'),
                    'icon' => 'list',
                    ),
                )
            );
        $modelList = getAllService('ContentModel', '');
        $contentMenu = array();
        if (!empty($modelList))
        {
            $i = 0;
            foreach ($modelList as $key => $value)
            {
                $i++;
                $data['add'][$i]['name'] = '添加' . $value['name'] . '栏目';
                $data['add'][$i]['url'] = U($key . '/AdminCategory/add');
                $data['add'][$i]['icon'] = 'plus';
            }
        }
        return $data;
    }
    /**
     * 列表
     */
    public function index()
    {
        $breadCrumb = array('栏目列表' => U());
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', D('Category')->loadList());
        $this->adminDisplay();
    }
}

