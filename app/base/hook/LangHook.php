<?php
namespace app\base\hook;

class LangHook
{
    /**
     * 默认配置
     *
     * @var array
     */
    protected $config = [
        'LANG_OPEN' => 0,
        'LANG_DEFAULT' => 'en-us',
        'LANG_AUTO_DETEC' => 1,
        'LANG_LIST' => [
            'en-us' => [
                'label' => '英文',
            ],
            'zh-cn' => [
                'label' => '中文-简体'
            ]
        ]
    ];

    protected $cookie_name = 'APP_LANG';

    /**
     * 保存cookie日期
     *
     * @var integer
     */
    protected $save_day = 24;

    public function __construct()
    {
        $c_config = include CONFIG_PATH . 'lang.php';
        $this->config = array_merge($this->config, $c_config);
    }

    public function CheckLang()
    {
        // 开启多语言
        if ($this->config['LANG_OPEN']) {
            define('LANG_OPEN', true);
            define('LANG_CONFIG', serialize($this->config));

            $defaultLang = $this->getDefaultLang();
            
            $lang = request('get.l', null, function ($lang) use ($defaultLang) {
                $langs = array_keys($this->config['LANG_LIST']);
                if ($lang && in_array($lang, $langs)) {
                    $url = defined('ADMIN_STATUS') ? '/admin.php' : '/';
                    $this->setCookie($lang);
                    header('location:' .$url, true, $code);
                    exit();
                } 

                if (in_array($defaultLang, $langs)){
                    return $defaultLang;
                } else {
                    return $this->config['LANG_DEFAULT'];
                }
            });

            define('APP_LANG', $lang);
        }
    }

    /**
     * 保存cookie
     *
     * @param [type] $lang
     * @return void
     */
    public function setCookie($lang)
    {
        cookie($this->cookie_name, $lang, $this->save_day * 24);
    }

    /**
     * 获取默认语言
     *
     * @return void
     */
    protected function getDefaultLang()
    {
        if (cookie($this->cookie_name)) {
            return cookie($this->cookie_name);
        } elseif ($this->config['LANG_AUTO_DETEC']) {
            // 自动侦测语言
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                return strtolower($matches[1]);
            }
        }

        return $this->config['LANG_DEFAULT'];
    }
}
