<?php
namespace DuxCms\Service;
/**
 * 后台菜单接口
 */
class MenuService{
	/**
	 * 获取菜单结构
	 */
	public function getAdminMenu(){
        //获取表单列表
        $formList = D('DuxCms/FieldsetForm')->loadList();
        $formMenu = array();
        if(!empty($formList)){
            foreach ($formList as $key => $value) {
                $formMenu[] = array(
                    'name' => $value['name'],
                    'url' => U('DuxCms/AdminFormData/index',array('fieldset_id'=>$value['fieldset_id'])),
                    'order' => $key,
                    );
            }
        }
        //返回菜单
		return array(
            'Content' => array(
                'name' => '内容',
                'icon' => 'bars',
                'order' => 1,
                'menu' => array(
                    array(
                        'name' => '栏目管理',
                        'icon' => 'sitemap',
                        'url' => U('DuxCms/AdminCategory/index'),
                        'order' => 0
                    )
                )
            ),
            'Form' => array(
                'name' => '表单',
                'icon' => 'file-text-o',
                'order' => 2,
                'menu' => $formMenu,
            ),
            'index' => array(
                'menu' => array(
                    array(
                        'name' => '站点统计',
                        'icon' => 'tachometer',
                        'url' => U('DuxCms/AdminStatistics/index'),
                        'order' => 1
                    ),
                    array(
                        'name' => '安全中心',
                        'icon' => 'shield',
                        'url' => U('DuxCms/AdminSafe/index'),
                        'order' => 2
                    ),
                    array(
                        'name' => '系统更新',
                        'icon' => 'bug',
                        'url' => U('DuxCms/AdminUpdate/index'),
                        'order' => 3
                    ), 
                )
            ),
            'Function' => array(
                'name' => '功能',
                'icon' => 'wrench',
                'order' => 3,
                'menu' => array(    
                    array(
                        'name' => '碎片管理',
                        'icon' => 'leaf',
                        'url' => U('DuxCms/AdminFragment/index'),
                        'order' => 1
                    ),
                    array(
                        'name' => '推荐位管理',
                        'icon' => 'crosshairs',
                        'url' => U('DuxCms/AdminPosition/index'),
                        'order' => 2
                    ),
                    array(
                        'name' => '扩展模型管理',
                        'icon' => 'puzzle-piece',
                        'url' => U('DuxCms/AdminExpand/index'),
                        'order' => 3
                    ),
                    array(
                        'name' => '表单管理',
                        'icon' => 'file-text-o',
                        'url' => U('DuxCms/AdminForm/index'),
                        'order' => 4
                    ),
                    array(
                        'name' => 'TAG管理',
                        'icon' => 'tags',
                        'url' => U('DuxCms/AdminTags/index'),
                        'order' => 5
                    ),
                )
            ),
        );
	}
	


}
