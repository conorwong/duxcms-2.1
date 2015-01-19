<?php
namespace app\duxcms\controller;
use app\home\controller\SiteController;
/**
 * TAG列表
 */

class TagsController extends SiteController {

	/**
     * 列表
     */
    public function index(){
        //URL参数
        $pageMaps = array();
        //查询数据
        $where = array();
        $count = target('Tags')->countList($where);
        $limit = $this->getPageLimit($count,20);
        $list = target('Tags')->loadList($where,$limit);
        //位置导航
        $crumb = array(array('name'=>'标签列表','url'=>url('duxcms/Tags/index')));
        //MEDIA信息
        $media = $this->getMedia($formInfo['name']);
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('pageList',$list);
        $this->assign('page',$this->getPageShow());
        $this->siteDisplay(config('tpl_tags').'_list');
    }

}

