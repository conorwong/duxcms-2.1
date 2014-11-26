<?php
namespace Think\Template\Driver;
/**
 * Dux模板引擎驱动 
 */
class Dux {
    /**
     * 渲染模板输出
     * @access public
     * @param string $templateFile 模板文件名
     * @param array $var 模板变量
     * @return void
     */
    public function fetch($templateFile,$var) {
        extract($var, EXTR_OVERWRITE);
        if(is_file($templateFile)) {
            \Think\Storage::load($this->template($templateFile),$var,null,'tpl');
        }else{
            $tmplContent =  $templateFile;
            vendor('Dux.template#class');
            $template = new \template();
            $tmplContent = $template -> complie($tmplContent);
            eval('?>' .$tmplContent);
        }
    }
    public function checkTplRefresh($maintpl, $subtpl, $timecompare, $cachefile) {
        if(empty($timecompare) || @filemtime($subtpl) > $timecompare) {
            //获取模板文件
            if(file_exists(!$maintpl)){
                E(L('_TEMPLATE_NOT_EXIST_') . ':' . $maintpl);
            }
            $tmplContent = file_get_contents($maintpl);
            //编译模板
            vendor('Dux.template#class');
            $template = new \template();
            $tmplContent = $template -> complie($tmplContent);
            //写入模板缓存
            \Think\Storage :: put($cachefile, trim($tmplContent), 'tpl');
            return true;
        }
        return false;
    }
    
    public function template($templateFile) {
        $cachefile = C('CACHE_PATH') . str_replace('/', '_', substr($templateFile, strlen(THEME_PATH))) . C('TMPL_CACHFILE_SUFFIX');
        $this->checkTplRefresh($templateFile, $templateFile, @filemtime($cachefile), $cachefile);
        return $cachefile;
    }
}