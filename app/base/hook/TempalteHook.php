<?php
namespace app\base\hook;

/**
 * Dux模板标签类
 */
class TempalteHook
{
    private $__ldel = '{';
    private $__rdel = '}';
    private $__ltag = '<!--';
    private $__rtag = '-->';
    private $_template_preg = array();
    private $_template_replace = array();

    public function templateParse($template)
    {
        
        //替换判断
        $this->_template_preg[] = '/' . $this->__ltag . "if\{(.*?)\}" . $this->__rtag . '/i';
        $this->_template_preg[] = '/' . $this->__ltag . '\{else\}' . $this->__rtag . '/i';
        $this->_template_preg[] = '/' . $this->__ltag . '(else if|elseif)\{(.*?)\}' . $this->__rtag . '/i';

        $this->_template_replace[] = '<?php if (\\1){ ?>';
        $this->_template_replace[] = '<?php }else{ ?>';
        $this->_template_replace[] = '<?php }else if (\\2){ ?>';

        // empty标签 判断是否为空数组
        $this->_template_preg[] = '/{empty\s+name=\"(.*?)\"}/i';
        $this->_template_preg[] = '/{\/empty}/i';
        $this->_template_replace[] = '<?php if (true === empty(\\1)) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // noempty 判断是否不为空数组
        $this->_template_preg[] = '/{noempty\s+name=\"(.*?)\"}/i';
        $this->_template_preg[] = '/{\/noempty}/i';
        $this->_template_replace[] = '<?php if (false === empty(\\1)) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // defined 判断是否已定义常量
        $this->_template_preg[] = '/{defined\s+name=\"(.*?)\"}/i';
        $this->_template_preg[] = '/{\/defined}/i';
        $this->_template_replace[] = '<?php if (defined(\'\\1\')) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // nodefined 判断是否未定义常量
        $this->_template_preg[] = '/{nodefined\s+name=\"(.*?)\"}/i';
        $this->_template_preg[] = '/{\/nodefined}/i';
        $this->_template_replace[] = '<?php if (false === defined(\'\\1\')) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // isset 判断是否已定义变量
        $this->_template_preg[] = '/{isset\s+name=\"(.*?)\"}/i';
        $this->_template_preg[] = '/{\/isset}/i';
        $this->_template_replace[] = '<?php if (isset(\\1)) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // notset 判断是否未定义变量
        $this->_template_preg[] = '/{noset\s+name=\"(.*?)\"}/i';
        $this->_template_preg[] = '/{\/noset}/i';
        $this->_template_replace[] = '<?php if (false === isset(\\1)) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // between 区间判断
        $this->_template_preg[] = '/{between\s+name=\"(.*?)\"\s+value="(.*?),(.*?)"}/i';
        $this->_template_preg[] = '/{\/between}/i';
        $this->_template_replace[] = '<?php if (\\1 >= \\2 && \\1 < \\3) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // nobetween 区间判断
        $this->_template_preg[] = '/{nobetween\s+name=\"(.*?)\"\s+value="(.*?),(.*?)"}/i';
        $this->_template_preg[] = '/{\/nobetween}/i';
        $this->_template_replace[] = '<?php if (\\1 < \\2 || \\1 > \\3) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // in 是否存在数组
        $this->_template_preg[] = '/{in\s+name=\"(.*?)\"\s+value="(.*?)"}/i';
        $this->_template_preg[] = '/{\/in}/i';
        $this->_template_replace[] = '<?php if (in_array(\\1, [\\2])) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        // noin 不存在数组
        $this->_template_preg[] = '/{noin\s+name=\"(.*?)\"\s+value="(.*?)"}/i';
        $this->_template_preg[] = '/{\/noin}/i';
        $this->_template_replace[] = '<?php if (false === in_array(\\1, [\\2])) { ?>';
        $this->_template_replace[] = '<?php }; ?>';

        //替换循环
        $this->_template_preg[] = '/' . $this->__ltag . '(loop|foreach)\{(.*?)\}' . $this->__rtag . '/i';
        $this->_template_replace[] = '<?php foreach (\\2) { ?>';
        $this->_template_preg[] = '/' . $this->__ltag . 'for\{(.*?)\}' . $this->__rtag . '/i';
        $this->_template_replace[] = '<?php for (\\1) { ?>';

        //注释标签
        $this->_template_preg[] = '/' . $__ltag . '\{(\#|\*)(.*?)(\#|\*)\}' . $__rtag . '/';
        $this->_template_replace[] = '';

        //引入页面标签
        $this->_template_preg[] = '/<!--#include\s*file=[\"|\'](.*)\.(html|htm)[\"|\']-->/';

        // 多语言
        if (defined('LANG_OPEN') && LANG_OPEN) {
            $this->_template_replace[] = "<?php \$__Template->display(\"".THEME_NAME.'/'. TPL_NAME . '/'. APP_LANG ."/$1\"); ?>";
        } else {
            $this->_template_replace[] = "<?php \$__Template->display(\"".THEME_NAME.'/'. TPL_NAME . "/$1\"); ?>";
        }

        // 百度链接推送
        $this->_template_preg[] = '/<!--#pushBaidu-->/';
        $this->_template_replace[] = "<?php pushBaidu(); ?>";

        // 网站统计
        $this->_template_preg[] = '/<!--#tongji-->/';
        $this->_template_replace[] = "<?php echo tongji(); ?>";

        // 文章进度
        $this->_template_preg[] = '/{progress\s*container=\"(.*?)\"\s*parent="(.*?)"\s*child="(.*?)"\s*class="(.*?)"}/i';
        $this->_template_preg[] = '/{\/progress}/i';
        $this->_template_replace[] = '<?php $container = "\\1"; $parent="\\2"; $child="\\3"; $class="\\4" ?>';
        $this->_template_replace[] = '<?php echo showArticleProgress($container, $parent, $child, $class);?>';

        //替换图片CSS等路径
        $template = preg_replace_callback('/<(.*?)(src=|href=|value=|background=)[\"|\'](images\/|img\/|css\/|js\/|style\/)(.*?)[\"|\'](.*?)>/', array($this, 'parse_load'), $template);

        //替换变量标签(必须最后处理)
        $template = preg_replace_callback('/\$\w+((\.\w+)*)?/', array($this, 'parse_var'), $template);
        $this->_template_preg[] = '/' . $this->__ldel . '((( *(\+\+|--) *)*?(([_a-zA-Z][\w]*\(.*?\))|\$((\w+)((\[|\()(\'|")?\$*\w*(\'|")?(\)|\]))*((->)?\$?(\w*)(\((\'|")?(.*?)(\'|")?\)|))){0,})( *\.?[^ \.]*? *)*?){1,})' . $this->__rdel . '/i';
        $this->_template_replace[] = '<?php echo \\1;?>';

        //结束符号
        $this->_template_preg[] = '/' . $this->__ltag . '\{\/(.*?)\}' . $this->__rtag . '/i';
        $this->_template_replace[] = '<?php } ?>';

        // 替换所有标签
        $template = preg_replace($this->_template_preg, $this->_template_replace, $template);

        //替换通用循环
        $template = preg_replace_callback('/' . $this->__ltag . '(\w+){([^"].*)}' . $this->__rtag . '/i', array($this, 'parse_for'), $template);

        //替换常量
        $template = str_replace('__TPL__', ROOT_URL . THEME_NAME.'/'.TPL_NAME, $template);
        $template = str_replace('__PUBLIC__', __PUBLIC__, $template);
        $template = str_replace('__ROOT__', __ROOT__, $template);
        return $template;
    }

    /**
     * 解析循环标签
     * @param array $var
     * @return string
     */
    private function parse_for($var)
    {
        $tpl = trim($var[2]);
        $item = trim($var[1]);
        $tpl = ' ' . $tpl;
        $tpl = preg_replace("/\s([_a-zA-Z]+)=/", ', "\1"=>', $tpl);
        $tpl = substr($tpl, 1);
        //匹配必要参数
        $dataArray = array();
        if (preg_match_all('/\s"([_a-zA-Z]+)"=>"(.+?)"/', $tpl, $result)) {
            foreach ($result[1] as $key => $value) {
                $dataArray[$value] = $result[2][$key];
            }
        }
        //生成模块调用
        $html = '<?php $'.$item.'List = service("'.strtolower($dataArray['app']).'","Label","'.$dataArray['label'].'",array('.$tpl.')); ';
        switch ($item) {
            case 'echo':
                $html .= ' echo $'.$item.'List; ?>';
                break;
            case 'assign':
                $html .= '$'.$dataArray['list'].' = $'.$item.'List; ?>';
                break;
            default:
                $html .= ' if(is_array($'.$item.'List)) foreach($'.$item.'List as $'.$item.'){ ?>';
                break;
        }
        return $html;
    }

    /**
     * 解析变量
     * @param array $var
     * @return string
     */
    private function parse_var($var)
    {
        if (empty($var[0])) {
            return;
        }
        $vars = explode('.', $var[0]);
        $var = array_shift($vars);
        $name = $var;
        foreach ($vars as $val) {
            $name .= '["' . $val . '"]';
        }
        return $name;
    }

    /**
     * 解析文件路径标签
     * @param array $var
     * @return string
     */
    private function parse_load($var)
    {
        $file = $var[3].$var[4];
        $url = THEME_NAME.'/'.TPL_NAME;

        if (defined('LANG_OPEN') && LANG_OPEN) {
            $url = THEME_NAME . '/' . TPL_NAME . '/' . APP_LANG;
        }
        
        if (substr($url, 0, 1) == '.') {
            $url = substr($url, 1);
        }
        $url = str_replace('\\', '/', $url);
        $url = ROOT_URL . $url . '/' . $file;
        $html = '<'.$var[1].$var[2].'"'.$url.'"'.$var[5].'>';
        return $html;
    }
}
