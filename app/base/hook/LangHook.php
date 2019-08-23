<?php
namespace app\base\hook;

class LangHook
{
    public function CheckLang()
    {
        $config = include CONFIG_PATH . 'lang.php';
        
        if ($config['LANG_OPEN']) {
            define('LANG_OPEN', true);
            define('LANG_CONFIG', serialize($config));

            $name = config('COOKIE_PREFIX') . 'APP_LANG';
            $clang = $_COOKIE[$name] ? $_COOKIE[$name] : $config['LANG_DEFAULT'];
            $lang = request('get.lang', null, function($lang) use ($config, $clang){
                $langs = array_keys($config['LANG_LIST']);
                $rewrite = config('REWRITE_RULE');
                foreach($langs as $item)
                {
                    $rewrite[$item] = 'duxcms/Lang/index';
                }

                config('REWRITE_RULE', $rewrite);

                if ($lang && in_array($lang, $langs) && $lang !== $clang) {
                    cookie('APP_LANG', $lang, 3);
                    $url = defined('ADMIN_STATUS') ? '/admin.php' : '/';
                    header('location:' .$url, true, $code);
                    exit;
                } else {
                    return $clang;
                }
            });

            define('APP_LANG', $lang);
        }
    }
}
