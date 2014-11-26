<?php
namespace Home\Controller;
use Common\Controller\BaseController;
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
            C('TPL_NAME' , C('MOBILE_TPL'));
        }
    }

    /**
     * 前台模板显示 调用内置的模板引擎显示方法
     * @access protected
     * @param string $name 模板名
     * @param bool $type 模板输出
     * @return void
     */
    protected function siteDisplay($name='',$type = true) {
        C('TAGLIB_PRE_LOAD','Dux');
        C('TAGLIB_BEGIN','<!--{');
        C('TAGLIB_END','}-->');
        C('VIEW_PATH','./themes/');
        $data = $this->view->fetch(C('TPL_NAME').'/'.$name);
        $this->incTpl($data);
        $data = $this->tplData;
        //替换资源路径
        $tplReplace=array(
            //普通转义
            'search' => array(
                //转义路径
                "/<(.*?)(src=|href=|value=|background=)[\"|\'](images\/|img\/|css\/|js\/|style\/)(.*?)[\"|\'](.*?)>/",
            ),
            'replace' => array(
                "<$1$2\"".__ROOT__."/themes/".C('TPL_NAME')."/"."$3$4\"$5>",
            ),      
        );
        $data = preg_replace( $tplReplace['search'] , $tplReplace['replace'] , $data);
        if($type){
            echo $data;
        }else{
            return $data;
        }
        
    }

    protected function incTpl($data)
    {
        //模板包含
        if(preg_match_all('/<!--#include\s*file=[\"|\'](.*)[\"|\']-->/', $data, $matches)){
            foreach ($matches[1] as $k => $v) {
                $ext=explode('.', $v);
                $ext=end($ext);
                $file=substr($v, 0, -(strlen($ext)+1));
                $phpText = $this->view->fetch(C('TPL_NAME').'/'.$file);
                $data = str_replace($matches[0][$k], $phpText, $data);
            }
            $this->incTpl($data);
        }else{
            $this->tplData = $data;
        }
    }

    /**
     * 页面Meda信息组合
     * @return array 页面信息
     */
    protected function getMedia($title='',$keywords='',$description='')
    {
        if(empty($title)){
            $title=C('SITE_TITLE').' - '.C('SITE_SUBTITLE');
        }else{
            $title=$title.' - '.C('SITE_TITLE').' - '.C('SITE_SUBTITLE');
        }
        if(empty($keywords)){
            $keywords=C('SITE_KEYWORDS');
        }
        if(empty($description)){
            $description=C('SITE_DESCRIPTION');
        }
        return array(
            'title'=>$title,
            'keywords'=>$keywords,
            'description'=>$description,
        );
    }
}
