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
                'icon' => 'home',
                'order' => 1,
                'menu' => array(
                    array(
                        'name' => '栏目管理',
                        'url' => U('DuxCms/AdminCategory/index'),
                        'order' => 0
                    )
                )
            ),
            'Form' => array(
                'name' => '表单',
                'icon' => 'form',
                'order' => 2,
                'menu' => $formMenu,
            ),
            'Function' => array(
                'name' => '功能',
                'icon' => 'home',
                'order' => 3,
                'menu' => array(
                    
                    array(
                        'name' => '碎片管理',
                        'url' => U('DuxCms/AdminFragment/index'),
                        'order' => 0
                    ),
                    array(
                        'name' => '推荐位管理',
                        'url' => U('DuxCms/AdminPosition/index'),
                        'order' => 1
                    ),
                    array(
                        'name' => '扩展模型管理',
                        'url' => U('DuxCms/AdminExpand/index'),
                        'order' => 2
                    ),
                    array(
                        'name' => '表单管理',
                        'url' => U('DuxCms/AdminForm/index'),
                        'order' => 3
                    ),
                    array(
                        'name' => 'TAG管理',
                        'url' => U('DuxCms/AdminTags/index'),
                        'order' => 4
                    ),
                )
            ),
        );
	}
	


}
