<?php
namespace Article\Service;
/**
 * 后台菜单接口
 */
class MenuService{
	/**
	 * 获取菜单结构
	 */
	public function getAdminMenu(){
		return array(
            'Content' => array(
                'menu' => array(
                    array(
                        'name' => '文章管理',
                        'url' => U('Article/AdminContent/index'),
                        'order' => 1
                    )
                )
            ),
        );
	}
	


}
