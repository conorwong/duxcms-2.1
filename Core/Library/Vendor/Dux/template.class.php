<?php
/**
 * Dux模板引擎类 
 */
class template {

	private $subtemplates = array();
    private $__ldel = '{';
    private $__rdel = '}';
    private $__ltag = '<!--';
    private $__rtag = '-->';
    private $_template_preg = array(), $_template_replace = array();

    /**
     * 模板编译核心
     */
	public function complie($template) {
       
        //替换判断
        $this->_template_preg[] = '/' . $this->__ltag . "if\{(.*?)\}" . $this->__rtag . '/i';
        $this->_template_preg[] = '/' . $this->__ltag . '\{else\}' . $this->__rtag . '/i';
        $this->_template_preg[] = '/' . $this->__ltag . '(else if|elseif)\{(.*?)\}' . $this->__rtag . '/i';

        $this->_template_replace[] = '<?php if (\\1){ ?>';
        $this->_template_replace[] = '<?php }else{ ?>';
        $this->_template_replace[] = '<?php }else if (\\2){ ?>';

        //替换循环
        $this->_template_preg[] = '/' . $this->__ltag . '(loop|foreach)\{(.*?)\}' . $this->__rtag . '/i';
        $this->_template_replace[] = '<?php foreach (\\2) {?>';
        $this->_template_preg[] = '/' . $this->__ltag . 'for\{(.*?)\}' . $this->__rtag . '/i';
        $this->_template_replace[] = '<?php for (\\1) { ?>';

        //注释标签
        $this->_template_preg[] = '/' . $__ltag . '\{(\#|\*)(.*?)(\#|\*)\}' . $__rtag . '/';
        $this->_template_replace[] = '';

        //引入页面标签
        $this->_template_preg[] = '/<!--#include\s*file=[\"|\'](.*)\.(html|htm)[\"|\']-->/';
        $this->_template_replace[] = '<?php include \Think\Template\Driver\Dux :: template(C(\'VIEW_PATH\'). C(\'TPL_NAME\').\'/\\1\'.C(\'TMPL_TEMPLATE_SUFFIX\')); ?>';


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
        $template = str_replace('__TPL__', substr(C('VIEW_PATH'),1). C('TPL_NAME'), $template);

        // 添加安全代码
        $template = '<?php if (!defined(\'THINK_PATH\')) exit();?>'.$template;
		// 优化生成的php代码
		$template = str_replace('?><?php', '', $template);
		// 模版编译过滤标签
		\Think\Hook :: listen('template_filter', $template);
		$template = strip_whitespace($template);
        return $template;
	}

    /**
     * 解析循环标签
     * @param array $var
     * @return string
     */
    private function parse_for($var) {
        $tpl = trim($var[2]);
        $item = trim($var[1]);
        $tpl = ' ' . $tpl;
        $tpl = preg_replace("/\s([_a-zA-Z]+)=/", ', "\1"=>', $tpl);
        $tpl = substr($tpl, 1);
        //匹配必要参数
        $dataArray = array();
        if(preg_match_all('/\s"([_a-zA-Z]+)"=>"(.+?)"/', $tpl, $result)){
            foreach ($result[1] as $key => $value) {
                $dataArray[$value] = $result[2][$key];
            }
        }
        //生成模块调用
        $html = '<?php $'.$item.'List = service("'.$dataArray['app'].'","Label","'.$dataArray['label'].'",array('.$tpl.')); ';
        switch ($item) {
            case 'echo':
                $html .= ' echo $'.$item.'List; ?>'; 
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
    private function parse_var($var) {
        if (empty($var[0])){
            return;
        }
        $vars = explode('.', $var[0]);
        $var = array_shift($vars);
        $name = $var;
        foreach ($vars as $val){
            $name .= '["' . $val . '"]';
        }
        return $name;
    }

    /**
     * 解析文件路径标签
     * @param array $var
     * @return string
     */
    private function parse_load($var) {
        $file = $var[3].$var[4];
        $url = C('VIEW_PATH'). C('TPL_NAME');
        if(substr($url, 0,1) == '.'){
            $url = substr($url, 1);
        }
        $url = str_replace('\\', '/', $url);
        $url = __ROOT__ . $url . '/' . $file;
        $html = '<'.$var[1].$var[2].'"'.$url.'"'.$var[5].'>';
        return $html;
    }


}

?>