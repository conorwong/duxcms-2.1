<?php
namespace Admin\Model;
use Think\Model;
/**
 * 网站配置
 */
class ConfigModel extends Model {
    /**
     * 获取信息
     * @return array 网站配置
     */
    public function getInfo()
    {
        $list = $this->select();
        $config = array();
        foreach ($list as $key => $value) {
            $config[$value['name']] = $value['data'];
        }
        return $config;
    }

    /**
     * 更新信息
     * @param int $siteId 站点配置ID
     * @return bool 更新状态
     */
    public function saveData(){
        $data = I('post.');
        if(empty($data)){
            $this->error = '数据创建失败！';
            return false;
        }
        foreach ($data as $key => $value) {
            $currentData = array();
            $currentData['data'] = $value;
            $status = $this->data($currentData)->where('name = "'.$key.'"')->save();
            if($status === false){
                return false;
            }
        }
        return true;
    }

    /**
     * 获取当前模板文件
     * @return array 文件列表
     */
    public function tplList()
    {
        $config = $this->getInfo();
        $tplDir=realpath('./Themes/'.$config['tpl_name']);
        if(!is_dir($tplDir)){
            return false;
        }
        $listFile=scandir($tplDir);
        if(is_array($listFile)){
            $list=array();
            foreach ($listFile as $key => $value) {
                if ($value != "." && $value != "..") {
                    $list[$key]['file']=$value;
                    $list[$key]['name']=substr($value, 0, -5);
                }
            }
        }
        return $list;
    }

    /**
     * 获取模板路径
     * @return array 主题列表
     */
    public function themesList()
    {
        $tplDir=realpath('./Themes');
        if(!is_dir($tplDir)){
            return false;
        }
        $listFile=scandir($tplDir);
        if(is_array($listFile)){
        $list=array();
            foreach ($listFile as $key => $value) {
                if ($value != "." && $value != ".."&&!strpos($value,".")) {
                    $list[$key]['file']=$value;
                    $list[$key]['name']=$value;
                }
            }
        }
        return $list;
    }


}
