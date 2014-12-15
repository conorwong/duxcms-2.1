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
                        'icon' => 'home',
                        'url' => U('Admin/Index/index'),
                        'order' => 0
                    )
                )
            ),
            'system' => array(
                'name' => '系统',
                'icon' => 'tachometer',
                'order' => 9,
                'menu' => array(
                    array(
                        'name' => '系统设置',
                        'icon' => 'sliders',
                        'url' => U('Admin/Setting/site'),
                        'order' => 0,
                        'divider' => true,
                    ),
                    array(
                        'name' => '上传配置',
                        'icon' => 'upload',
                        'url' => U('Admin/AdminUploadSetting/index'),
                        'order' => 1
                    ),
                    array(
                        'name' => '缓存管理',
                        'icon' => 'upload',
                        'url' => U('Admin/Manage/cache'),
                        'order' => 3,
                        'divider' => true,
                    ),
                    array(
                        'name' => '应用管理',
                        'icon' => 'flash',
                        'url' => U('Admin/Functions/index'),
                        'order' => 4,
                        'divider' => true,
                    ),
                    array(
                        'name' => '安全记录',
                        'icon' => 'qrcode',
                        'url' => U('Admin/AdminLog/index'),
                        'order' => 5
                    ),
                    array(
                        'name' => '备份还原',
                        'icon' => 'database',
                        'url' => U('Admin/AdminBackup/index'),
                        'order' => 6
                    ),
                    array(
                        'name' => '用户管理',
                        'icon' => 'user',
                        'url' => U('Admin/AdminUser/index'),
                        'order' => 7,
                        'divider' => true,
                    ),
                    array(
                        'name' => '用户组管理',
                        'icon' => 'group',
                        'url' => U('Admin/AdminUserGroup/index'),
                        'order' => 8,
                    )
                )
            ),
        );
	}
	


}
