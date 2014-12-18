<?php
namespace Admin\Model;
use Think\Model;
/**
 * 网站管理
 */
class ManageModel {
    /**
     * 获取缓存列表
     * @param int $key 缓存key
     * @return array 用户信息
     */
    public function getCacheList($key = '')
    {
        $list = array(
                'tpl' => array('id'=>'tpl','name'=>'模板缓存', 'dir'=>CACHE_PATH, 'size'=>(dir_size(CACHE_PATH)%1024).'KB'),
                'html' => array('id'=>'html','name'=>'静态缓存', 'dir'=>HTML_PATH, 'size'=>(dir_size(HTML_PATH)%1024).'KB'),
                'data' => array('id'=>'data','name'=>'数据缓存', 'dir'=>DATA_PATH, 'size'=>(dir_size(DATA_PATH)%1024).'KB'),
                'run' => array('id'=>'data','name'=>'运行缓存', 'dir'=>RUNTIME_PATH.'common~runtime.php', 'size'=>(filesize(RUNTIME_PATH.'common~runtime.php')%1024).'KB'),
                );
        if($key){
            return $list[$key];
        }
        return $list;
    }
    /**
     * 清空指定缓存
     * @param int $key 缓存key
     * @return array 用户信息
     */
    public function delCache($key)
    {
        $info = $this->getCacheList($key);
        if(empty($info)){
            return;
        }
        $file = $info['dir'];
        if(is_dir($file)){
            del_dir($file);
        }elseif(is_file($file)){
            unlink($file);
        }
        return true;
    }


}
