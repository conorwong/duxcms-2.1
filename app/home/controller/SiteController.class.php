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
}
