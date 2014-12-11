<?php
namespace Article\Controller;
use Home\Controller\SiteController;
/**
 * 栏目页面
 */

class ContentController extends SiteController {

	/**
     * 栏目页
     */
    public function index()
    {
        $contentId = I('get.content_id',0,'intval');
        $urlTitle = I('get.urltitle');
        if (empty($contentId)&&empty($urlTitle)) {
            $this->error404();
        }
        $model = D('ContentArticle');
        //获取内容信息
        if(!empty($contentId)){
            $contentInfo=$model->getInfo($contentId);
        }else if(!empty($urlTitle)){
            $contentInfo=$model->getInfoUrl($urlTitle);
        }else{
            $this->error404();
        }
        $contentId = $contentInfo['content_id'];
        //信息判断
        if (!is_array($contentInfo)){
            $this->error404();
        }
        if(!$contentInfo['status']){
            $this->error404();
        }
        //获取栏目信息
        $modelCategory = D('CategoryArticle');
        $categoryInfo=$modelCategory->getInfo($contentInfo['class_id']);
        if (!is_array($categoryInfo)){
            $this->error404();
        }
        if($categoryInfo['app']<>MODULE_NAME){
            $this->error404();
        };
        //判断跳转
        if (!empty($contentInfo['url']))
        {
            ob_start();
            $this->show($contentInfo['url']);
            $link = ob_get_clean();
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$link."");
            exit;
        }
        //位置导航
        $crumb = D('DuxCms/Category')->loadCrumb($contentInfo['class_id']);
        //查询上级栏目信息
        $parentCategoryInfo = $modelCategory->getInfo($categoryInfo['parent_id']);
        //获取顶级栏目信息
        $topCategoryInfo = $modelCategory->getInfo($crumb[0]['class_id']);
        //更新访问计数
        $viewsData=array();
        $viewsData['views'] = array('exp','views+1');
        $viewsData['content_id'] = $contentInfo['content_id'];
        D('DuxCms/Content')->editData($viewsData);
        //内容处理
        $contentInfo['content'] = html_out($contentInfo['content']);
        //扩展模型
        if($categoryInfo['fieldset_id']){
            $extInfo = D('DuxCms/FieldsetExpand')->getDataInfo($categoryInfo['fieldset_id'],$contentId);
            $contentInfo = array_merge($contentInfo , $extInfo);
        }
        //上一篇
        $prevWhere = array();
        $prevWhere['A.status'] = 1;
        $prevWhere['A.time'] = array('lt',$contentInfo['time']);
        $prevWhere['C.class_id'] = $categoryInfo['class_id'];
        $prevInfo=$model->getWhereInfo($prevWhere,' A.time DESC,A.content_id DESC');
        if(!empty($prevInfo)){
            $prevInfo['aurl']=$model->getUrl($prevInfo,$appConfig);
            $prevInfo['curl']=$modelCategory->getUrl($prevInfo,$appConfig);
        }
        //下一篇
        $nextWhere = array();
        $nextWhere['A.status'] = 1;
        $nextWhere['A.time'] = array('gt',$contentInfo['time']);
        $nextWhere['C.class_id'] = $categoryInfo['class_id'];
        $nextInfo=$model->getWhereInfo($nextWhere,' A.time ASC,A.content_id ASC');
        if(!empty($nextInfo)){
            $nextInfo['aurl']=$model->getUrl($nextInfo,$appConfig);
            $nextInfo['curl']=$modelCategory->getUrl($nextInfo,$appConfig);
        }
        //MEDIA信息
        $media = $this->getMedia($contentInfo['title'],$contentInfo['keywords'],$contentInfo['description']);
        //模板赋值
        $this->assign('contentInfo', $contentInfo);
        $this->assign('categoryInfo', $categoryInfo);
        $this->assign('parentCategoryInfo', $parentCategoryInfo);
        $this->assign('topCategoryInfo', $topCategoryInfo);
        $this->assign('crumb', $crumb);
        $this->assign('count', $count);
        $this->assign('page', $page);
        $this->assign('media', $media);
        $this->assign('prevInfo', $prevInfo);
        $this->assign('nextInfo', $nextInfo);
        if($contentInfo['tpl']){
            $this->siteDisplay($contentInfo['tpl']);
        }else{
            $this->siteDisplay($categoryInfo['content_tpl']);
        }
    }
}