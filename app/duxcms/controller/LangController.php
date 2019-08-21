<?php
namespace app\duxcms\controller;

use app\home\controller\SiteController;

/**
 * 语言切换
 * 
 */
class LangController extends SiteController
{
    public function index()
    {
        if (!LANG_OPEN) {
            $this->redirect('/');
            exit;
        }

        $lang_config = LANG_CONFIG;
        $lang = request('get.lang');

        // 多语言设置
        if (false === array_key_exists($lang, $lang_config['LANG_LIST'])) {
            $this->redirect('/');
            exit;
        }
    
        cookie('APP_LANG', $lang, 24 * 3);
        $this->redirect('/');
    }
}