<?php
namespace Admin\Service;
/**
 * 后台菜单接口
 */
class MenuService{
	/**
	 * 获取菜单结构
	 */
	public function getAdminMenu(){
		return array(
            'index' => array(
                'name' => '首页',
                'icon' => 'home',
                'order' => 0,
                'menu' => array(
                    array(
                        'name' => '管理首页',
                        'url' => U('Admin/Index/index'),
                        'order' => 0
                    )
                )
            ),
            'system' => array(
                'name' => '系统',
                'icon' => 'bars',
                'order' => 9,
                'menu' => array(
                    array(
                        'name' => '系统设置',
                        'url' => U('Admin/Setting/site'),
                        'order' => 0,
                        'divider' => true,
                    ),
                    array(
                        'name' => '缓存管理',
                        'url' => U('Admin/Manage/cache'),
                        'order' => 3,
                        'divider' => true,
                    ),
                    array(
                        'name' => '安全记录',
                        'url' => U('Admin/AdminLog/index'),
                        'order' => 5
                    ),
                    array(
                        'name' => '备份还原',
                        'url' => U('Admin/AdminBackup/index'),
                        'order' => 6
                    ),
                    array(
                        'name' => '用户管理',
                        'url' => U('Admin/AdminUser/index'),
                        'order' => 7,
                        'divider' => true,
                    ),
                    array(
                        'name' => '用户组管理',
                        'url' => U('Admin/AdminUserGroup/index'),
                        'order' => 8,
                    )
                )
            ),
        );
	}
	


}
