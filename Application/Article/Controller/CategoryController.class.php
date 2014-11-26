<?php
namespace Article\Controller;
use Home\Controller\SiteController;
/**
 * 栏目页面
 */

class CategoryController extends SiteController {

	/**
     * 栏目页
     */
    public function index(){
    	$classId = I('get.class_id',0,'intval');
        $brand = I('get.brand',0,'intval');

        //位置导航
        $crumb = array(
            array('name'=>'资讯','url'=>U('Article/Category/index')),
            );

        if(!empty($classId)){
            //获取栏目信息
            $model = D('CategoryArticle');
            $categoryInfo=$model->getInfo($classId);
            $classId = $categoryInfo['class_id'];
            //信息判断
            if (!is_array($categoryInfo)){
                $this->error404();
            }
            if($categoryInfo['app']<>MODULE_NAME){
                $this->error404();
            }
            //设置查询条件
            $where= array();
            if ($categoryInfo['type'] == 0) {
                $classId = D('DuxCms/Category')->getSubClassId($classId);
            }
            if(empty($classId)){
                $classId = $categoryInfo['class_id'];
            }
            $where['A.status'] = 1;
            $where['C.class_id'] = array('in',$classId);

            //位置导航
            $crumb[] = array('name' => $categoryInfo['name'], 'url'=>U('Article/Category/index',array('class_id'=>$categoryInfo['class_id'])));

            //查询上级栏目信息
            $parentCategoryInfo = $model->getInfo($categoryInfo['parent_id']);
            //获取顶级栏目信息
            $topCategoryInfo = $model->getInfo($crumb[0]['class_id']);

            //MEDIA信息
            $media = $this->getMedia($categoryInfo['name'],$categoryInfo['keywords'],$categoryInfo['description']);

        }else{
            //设置查询条件
            $where = array();
            $where['A.status'] = 1;
            //位置导航
        }
        if($brand){
            $where['B.brand'] = $brand;
            $brandInfo = D('Tea/TeaBrand')->getInfo($brand);
            $crumb[] = array('name' => $brandInfo['name'], 'url'=>U('Article/Category/index',array('brand'=>$brand)));
        }
        
        //分页参数
        $size = intval($categoryInfo['page']); 
        if (empty($size)) {
            $listRows = 20;
        } else {
            $listRows = $size;
        }
        //查询内容数据
        $modelContent = D('ContentArticle');
        $count = $modelContent->countList($where);
        $limit = $this->getPageLimit($count,$listRows);
        if(!empty($categoryInfo['content_order'])){

            $categoryInfo['content_order'] = $categoryInfo['content_order'].',';
        }
        $pageList = $modelContent->loadList($where,$limit,$categoryInfo['content_order'].'A.time desc,A.content_id desc');
        //URL参数
        $pageMaps = array();
        $pageMaps['class_id'] = $classId;
        $pageMaps['urlname'] = $urlName;
        //获取分页
        $page = $this->getPageShow($pageMaps);
        //模板赋值
        $this->assign('categoryInfo', $categoryInfo);
        $this->assign('parentCategoryInfo', $parentCategoryInfo);
        $this->assign('topCategoryInfo', $topCategoryInfo);
        $this->assign('crumb', $crumb);
        $this->assign('pageList', $pageList);
        $this->assign('count', $count);
        $this->assign('page', $page);
        $this->assign('media', $media);
        $this->assign('pageMaps', $pageMaps);
        if(!empty($classId)){
            $this->siteDisplay('news_list');
        }else{
            $this->siteDisplay('news_list');
        }
    }
}