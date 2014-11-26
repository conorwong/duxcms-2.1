<?php
namespace Article\Controller;
use Home\Controller\SiteController;
/**
 * 文章搜索页面
 */
class SearchController extends SiteController {

    /**
     * 搜索结果
     */
    public function index() {
        $keyword = I('get.keyword');
        //解析关键词
        $keyword = len($keyword,0,20);
        $keywords = preg_replace ('/\s+/',' ',$keyword); 
        $keywords=explode(" ",$keywords);
        if(empty($keywords[0])){
            $this->error('没有输入关键词！');
        }
        $where = array();
        $where['A.status'] = 1;
        //获取栏目ID
        $classId = I('get.class_id',0,'intval');
        if($classId){
            $where['C.class_id'] = array('in',$classId);
        }
        //获取搜索类型
        $model=I('get.model',0,'intval');
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['class_id'] = $classId;
        $pageMaps['model'] = $model;
        //分页参数
        $size = I('get.pageNum',0,'intval');
        if (empty($size)) {
            $listRows = 20;
        } else {
            $listRows = $size;
        }
        //请求搜索结果
        foreach ($keywords as $value) {
            switch ($model) {
                //全文
                case '1':
                    $where['_string'] = ' (A.title = "'.$keyword.'") OR  (A.keywords = "'.$keyword.'")  OR  (A.description = "'.$keyword.'")   OR  (B.content = "'.$keyword.'") ';
                    break;
                //标题
                default:
                    $where['_string'] = ' (A.title = "'.$keyword.'") OR  (A.keywords = "'.$keyword.'")  OR  (A.description = "'.$keyword.'") ';
                    break;
            }
        }
        $count = D('ContentArticle')->countList($where);
        $limit = $this->getPageLimit($count,$listRows);
        $pageList = D('ContentArticle')->loadList($where, $limit);
        $list=array();
        if(!empty($pageList)){
            foreach ($pageList as $key=>$value) {
                $list[$key]=$value;
                $list[$key]['curl']=D('CategoryArticle')->getUrl($value);
                $list[$key]['aurl']=D('ContentArticle')->getUrl($value);
            }
        }
        //获取分页
        $page = $this->getPageShow($pageMaps);
        //位置导航
        $crumb = array(array('name'=>'文章搜索 - ' . $keyword,'url'=>U('',$pageMaps)));
        //MEDIA信息
        $media = $this->getMedia('文章搜索 - '.$keyword);
        //模板赋值
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('pageList',$data);
        $this->assign('page',$this->getPageShow());
        $this->assign('count', $count);
        $this->assign('keyword', $keyword);
         $this->siteDisplay(C('tpl_search').'_article');
    }
}