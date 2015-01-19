<?php
namespace app\duxcms\controller;
use app\admin\controller\AdminController;
/**
 * 更新管理
 */

class AdminUpdateController extends AdminController {

    public $domain = 'http://www.duxcms.com/index.php?s=/';
    /**
     * 当前模块参数
     */
    protected function _infoModule(){
        return array(
            'info'  => array(
                'name' => '更新管理',
                'description' => '升级当前网站到最新版',
                )
            );
    }
	/**
     * 列表
     */
    public function index(){
        $breadCrumb = array('更新管理'=>url());
        $this->assign('breadCrumb',$breadCrumb);
        $this->adminDisplay();
    }

    /**
     * 获取最新版本
     */
    public function getVer(){
        $verTime = config('DUX_TIME');
        if(empty($verTime)){
            $this->error('没有发现版本号！');
        }
        $url = $this->domain . 'Service/Update/index';
        $data = array();
        $data['ver_time'] = $verTime;
        $info = \Common\Util\Http::doPost($url,$data,10);
        $info = json_decode($info,true);
        if(empty($info)){
            $this->error('无法获取版本信息，请稍后再试！');
        }
        if($info['status']){
            $this->success($info['data']);
        }else{
            $this->error($info['data']);
        }
        
    }

    /**
     * 下载更新
     */
    public function dowload(){
        $url = request('post.url');
        if(empty($url)){
            $this->error('没有发现更新地址，请稍后重试！');
        }
        $updateDir = RUNTIME_PATH.'update/';
        if (!file_exists($updateDir)){
            if(!mkdir($updateDir)){
                $this->error('抱歉，无法为您创建更新文件夹，请手动创建目录【'.$updateDir.'】');
            }
        }
        $fileName = explode('/', $url);
        $fileName = end($fileName);
        //开始下载文件
        $fileName = $updateDir.$fileName;
        $file = \Common\Util\Http::doGet($url,30);
        if(!file_put_contents($fileName, $file)){
            $this->error('无法保存更新文件请检查目录【'.$updateDir.'】是否有写入权限！');
        }
        $this->success('文件下载成功，正在执行解压操作！');
    }

    /**
     * 解压文件
     */
    public function unzip(){
        $url = request('post.url');
        $fileName = explode('/', $url);
        $fileName = end($fileName);
        $updateDir = RUNTIME_PATH.'update/';
        $file = $updateDir.$fileName;
        if(!is_file($file)){
             $this->error('没有发现更新文件，请稍后重试！');
        }
        $dir = $updateDir.'tmp_'.config('DUX_TIME');
        $zip = new \Common\Util\Zip();
        if($zip->decompress($file,$dir)){
            $this->success('文件解压成功，等待更新操作！');
        }else{
            $this->error('解压文件失败请检查目录【'.$dir.'】是否有写入权限！');
        }
        
    }

    /**
     * 更新文件
     */
    public function upfile(){
        $updateDir = RUNTIME_PATH.'update/';
        $dir = $updateDir.'tmp_'.config('DUX_TIME');
        if(!copy_dir($dir,ROOT_PATH)){
            $this->error('无法复制更新文件，请检查网站是否有写入权限！');
        }
        //更新SQL文件
        $sql = file_get_contents(ROOT_PATH.'Update/'.config('DUX_TIME').'.sql');
        if($sql){
            $db = new \Think\Model();
            $sql = str_replace("\r", "\n", $sql);
            $sql = explode(";\n", $sql);
            //替换表前缀
            $sql = str_replace(" `dux_", " `".config('DB_PREFIX'), $sql);
            //开始导入数据库
            foreach ($sql as $value) {
                $value = trim($value);
                if(empty($value)) continue;
                if(substr($value, 0, 12) == 'CREATE TABLE') {
                    $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
                    $msg  = "创建数据表{$name}";
                    if(false === $db->execute($value)){
                        $this->error($msg . '...失败！');
                    }
                } else {
                    $db->execute($value);
                }
            }
        }
        //清理更新文件
        del_dir(ROOT_PATH.'Update');
        del_dir(ROOT_PATH.$dir);
        $this->success('更新成功，稍后为您刷新！');       
        
    }

    /**
     * 查询授权
     */
    public function Authorize(){
        $url = 'http://www.duxcms.com/index.php?s=Service/Authorize/index';
        $info = \Common\Util\Http::doGet($url,30);
        echo $info;
    }

}

