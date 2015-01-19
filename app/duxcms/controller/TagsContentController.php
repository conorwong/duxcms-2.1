<?php
namespace app\duxcms\controller;
use app\home\controller\SiteController;
/**
 * TAG内容列表
 */

class TagsContentController extends SiteController {

	/**
     * 列表
     */
    public function index(){
        $tag = request('get.name');
        $tag = len($tag,0,20);
        if(empty($tag)){
            $this->error404();
        }
        //获取TAG信息
        $where = array();
        $where['name'] = $tag;
        $tagInfo = target('Tags')->getWhereInfo($where);
        if(empty($tagInfo)){
            $this->error404();
        }
        //更新点击量
        target('Tags')->where(array('tag_id' => $tagInfo['tag_id']))->setInc('click', 1);
        //URL参数
        $pageMaps = array();
        //查询数据
        $where = array();
        $where['B.tag_id'] = $tagInfo['tag_id'];
        $count = target('TagsHas')->countContentList($where);
        $limit = $this->getPageLimit($count,20);
        $list = target('TagsHas')->loadContentList($where,$limit);
        if(!empty($list)){
            $data=array();
            foreach ($list as $key => $value) {
                $data[$key]=$value;
                $data[$key]['curl']=target('duxcms/Category')->getUrl($value);
                $data[$key]['aurl']=target('duxcms/Content')->getUrl($value);

            }
        }
        //位置导航
        $crumb = array(
            array('name'=>'标签列表','url'=>url('duxcms/Tags/index')),
            array('name'=>$tagInfo['name'],'url'=>url('duxcms/TagsContent/index',array('name'=>$tagInfo['name']))),
            );
        //MEDIA信息
        $media = $this->getMedia($formInfo['name']);
        $this->assign('crumb',$crumb);
        $this->assign('media', $media);
        $this->assign('pageList',$data);
        $this->assign('page',$this->getPageShow());
        $this->assign('count', $count);
        $this->siteDisplay(config('tpl_tags').'_content');
    }

}

