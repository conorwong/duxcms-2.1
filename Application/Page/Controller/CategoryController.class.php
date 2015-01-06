<?php
namespace Page\Controller;
use Home\Controller\SiteController;
/**
 * 单页面
 */
class CategoryController extends SiteController {

	/**
     * 栏目页
     */
    public function index(){
    	$classId = I('get.class_id',0,'intval');
        $urlName = I('get.urlname');
        if (empty($classId)&&empty($urlName)) {
            $this->error404();
        }
        //获取栏目信息
        $model = D('Page/CategoryPage');
        if(!empty($classId)){
            $categoryInfo=$model->getInfo($classId);
        }else if(!empty($urlName)){
        	$map = array();
        	$map['urlname'] = $urlName;
            $categoryInfo=$model->getWhereInfo($map);
        }else{
            $this->error404();
        }

        $classId = $categoryInfo['class_id'];
        //信息判断
        if (!is_array($categoryInfo)){
            $this->error404();
        }
        if($categoryInfo['app']<>MODULE_NAME){
            $this->error404();
        }
        //位置导航
        $crumb = D('DuxCms/Category')->loadCrumb($classId);
        //内容处理
        $categoryInfo['content'] = html_out($categoryInfo['content']);
        //查询上级栏目信息
        $parentCategoryInfo = D('DuxCms/Category')->getInfo($categoryInfo['parent_id']);
        //获取顶级栏目信息
        $topCategoryInfo = D('DuxCms/Category')->getInfo($crumb[0]['class_id']);
        //MEDIA信息
        $media = $this->getMedia($categoryInfo['name'],$categoryInfo['keywords'],$categoryInfo['description']);
        //模板赋值
        $this->assign('categoryInfo', $categoryInfo);
        $this->assign('parentCategoryInfo', $parentCategoryInfo);
        $this->assign('topCategoryInfo', $topCategoryInfo);
        $this->assign('crumb', $crumb);
        $this->assign('media', $media);
        $this->siteDisplay($categoryInfo['class_tpl']);
    }
}