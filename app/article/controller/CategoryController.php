<?php
namespace app\article\controller;

use app\home\controller\SiteController;

/**
 * 栏目页面
 */

class CategoryController extends SiteController
{

    /**
     * 栏目页
     */
    public function index()
    {
        $classId = request('get.class_id', 0, 'intval');
        $urlName = request('get.urlname');
        if (empty($classId)&&empty($urlName)) {
            $this->error404();
        }
        //获取栏目信息
        $model = target('CategoryArticle');
        if (!empty($classId)) {
            $categoryInfo=$model->getInfo($classId);
        } elseif (!empty($urlName)) {
            $map = array();
            $map['urlname'] = $urlName;
            $categoryInfo=$model->getWhereInfo($map);
        } else {
            $this->error404();
        }
        $classId = $categoryInfo['class_id'];
        //信息判断
        if (!is_array($categoryInfo)) {
            $this->error404();
        }

        // 多语言
        if (defined('LANG_OPEN') && $categoryInfo['lang'] !== APP_LANG) {
            changeLang($categoryInfo['lang'], $_REQUEST['s']);
        }

        if (strtolower($categoryInfo['app'])<>APP_NAME) {
            $this->error404();
        }
        //位置导航
        $crumb = target('duxcms/Category')->loadCrumb($classId);
        //设置查询条件
        $where=[];
        if ($categoryInfo['type'] == 0) {
            $classIds = target('duxcms/Category')->getSubClassId($classId);
        }
        if (empty($classIds)) {
            $classIds = $categoryInfo['class_id'];
        }
        $where['A.status'] = 1;
        $where[] = 'C.class_id in ('.$classIds.')';

        $mustParams = [];

        // 多条件筛选
        if ($categoryInfo['fieldset_id']) {
            $fieldsetInfo = target('duxcms/Fieldset')->getInfoClassId($categoryInfo['class_id']);
            $map = array();
            $map['A.fieldset_id'] = $fieldsetInfo['fieldset_id'];
            $fieldList=target('duxcms/FieldExpand')->loadList($map);
            if (empty($fieldList)||!is_array($fieldList)) {
                return;
            }

            $duowei = [];
            $fields = array_column($fieldList, 'field');

            foreach($fields as $field)
            {
                $param = request('get.' . $field, 0, 'intval');
                if ($param) {
                    $where['D.' . $field] = $param;
                    $mustParams[$field] = $param;
                }
            }

            foreach($fieldList as $key=>$item) {
                $params = request('get.' . $item['field'], 0, 'intval');
                $field = $item['field'];
                $data = ['name' => $item['name'], 'field' => $field];
                $child = explode(',', $item['config']);
                foreach($child as $k=>$name) {
                    $id = $k + 1;
                    $data['child'][] = [
                        'name' => $name,
                        'value' => $id,
                        'field' => $field,
                        'selected' => $params == $id ? true : false,
                        'url' => \buildScreenUri(true, $fields, $item['field'], $classId, $id),
                        'durl' => \buildScreenUri(false, $fields, $item['field'], $classId),
                    ];
                }

                $duowei[$key] = $data;
            }

            $this->assign('duowei', $duowei);
        }

        //分页参数
        $size = intval($categoryInfo['page']);
        if (empty($size)) {
            $listRows = 20;
        } else {
            $listRows = $size;
        }
        //查询内容数据
        $modelContent = target('ContentArticle');
        if (!empty($categoryInfo['content_order'])) {
            $categoryInfo['content_order'] = $categoryInfo['content_order'].',';
        }
        $pageList = $modelContent->page($listRows)->loadList($where, $limit, $categoryInfo['content_order'].'A.time desc,A.content_id desc', $categoryInfo['fieldset_id']);
        $this->pager = $modelContent->pager;
        
        //URL参数
        $pageMaps = array();
        $pageMaps['class_id'] = $classId;
        $pageMaps['urlname'] = $urlName;

        //获取分页
        $page = $this->getPageShow($pageMaps, $mustParams);
        
        //查询上级栏目信息
        $parentCategoryInfo = target('duxcms/Category')->getInfo($categoryInfo['parent_id']);
        //获取顶级栏目信息
        $topCategoryInfo = target('duxcms/Category')->getInfo($crumb[0]['class_id']);
        //MEDIA信息
        $media = $this->getMedia($categoryInfo['name'], $categoryInfo['keywords'], $categoryInfo['description']);

        // 统计分类下的文章数
        $categoryInfo['article_count'] = articleSumByCid($categoryInfo['class_id']);

        //模板赋值
        $this->assign('categoryInfo', $categoryInfo);
        $this->assign('parentCategoryInfo', $parentCategoryInfo);
        $this->assign('topCategoryInfo', $topCategoryInfo);
        $this->assign('crumb', $crumb);
        $this->assign('pageList', $pageList);
        $this->assign('page', $page);
        $this->assign('media', $media);
        $this->assign('pageMaps', $pageMaps);
        $this->siteDisplay($categoryInfo['class_tpl']);
    }
}
