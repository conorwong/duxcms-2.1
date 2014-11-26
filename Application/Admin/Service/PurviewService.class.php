<?php
namespace Admin\Service;
/**
 * 权限接口
 */
class PurviewService{
	/**
	 * 获取模块权限
	 */
	public function getAdminPurview(){
		return array(
            'Setting' => array(
                'name' => '系统设置',
                'auth' => array(
                    'admin' => '后台设置',
                    'performance' => '性能设置',
                    'upload' => '上传设置'
                )
            ),
            'Manage' => array(
                'name' => '系统管理',
                'auth' => array(
                    'cache' => '缓存管理'
                )
            ),
            'AppManage' => array(
                'name' => 'APP管理',
                'auth' => array(
                    'index' => '浏览'
                )
            )
        );
	}
	


}
