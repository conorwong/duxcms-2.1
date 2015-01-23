<?php
namespace app\home\controller;
use app\base\controller\BaseController;
/**
 * 前台公共类
 */
class SiteController extends BaseController {

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    /**
     * 控制器初始化
     */
    protected function initialize(){
        
        //设置手机版参数
        if(MOBILE){
            config('tpl_name' , config('mobile_tpl'));
        }
        //访问统计
        target('duxcms/TotalVisitor')->addData();
        //蜘蛛爬行统计
        target('duxcms/TotalSpider')->addData();
    }

    /**
     * 前台模板显示 调用内置的模板引擎
     * @access protected
     * @param string $name 模板名
     * @param bool $type 模板输出
     * @return void
     */
    protected function siteDisplay($name='',$type = true) {
        $tpl = 'themes/'.config('tpl_name').'/'.$name;
        if($type){
            $this->display($tpl);
        }else{
            return $this->display($tpl,true);
        }
    }

    /**
     * 页面Meda信息组合
     * @return array 页面信息
     */
    protected function getMedia($title='',$keywords='',$description='')
    {
        if(empty($title)){
            $title=config('site_title').' - '.config('site_subtitle');
        }else{
            $title=$title.' - '.config('site_title').' - '.config('site_subtitle');
        }
        if(empty($keywords)){
            $keywords=config('site_keywords');
        }
        if(empty($description)){
            $description=config('site_description');
        }
        return array(
            'title'=>$title,
            'keywords'=>$keywords,
            'description'=>$description,
        );
    }

    //分页结果显示
    protected function getPageShow($map = array())
    {
        $pageArray = $this->pager;
        $html = '
        <div class="dux-page">
          <a class="first-page" href="'.$this->createPageUrl($map,$pageArray['firstPage']).'">首页</a>
          <a class="prev-page" href="'.$this->createPageUrl($map,$pageArray['prevPage']).'">上一页</a> ';
            foreach ($pageArray['allPages'] as $value) {
                if($value == $pageArray['page']){
                    $html .= '<a class="num-page active-page"';
                }else{
                    $html .= '<a class="num-page"';
                }
                $html .= ' href="'.$this->createPageUrl($map,$value).'">'.$value.'</a>';
           }
         $html .= '<a class="next-page" href="'.$this->createPageUrl($map,$pageArray['nextPage']).'">下一页</a>
          <a class="last-page" href="'.$this->createPageUrl($map,$pageArray['lastPage']).'">末页</a>
        </div>';
        return $html;

    }
}
