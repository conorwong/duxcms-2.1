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
        $langs = $lang_config['LANG_LIST'];
        $lang = request('get.s', null, function($request) use ($langs) {
            $lang = end(explode('/', $request));
            $langs = array_keys($langs);
            if (in_array($lang, $langs)) {
                return $lang;
            } else {
                return false;
            }
        });

        // 多语言设置
        if (false === $lang) {
            $this->redirect('/');
            exit;
        }
    
        cookie('APP_LANG', $lang, 24 * 3);
        $this->redirect('/');
    }
}