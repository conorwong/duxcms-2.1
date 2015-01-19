<?php
namespace app\admin\model;
use app\base\model\BaseModel;
/**
 * 文件操作
 */
class FileModel extends BaseModel {
    //完成
    protected $_auto = array (
        array('time','time',3,'function'),
     );

    /**
     * 上传数据
     * @param array $files 上传$_FILES信息
     * @param array $config 上传配置信息可选
     * @return array 文件信息
     */
    public function uploadData($files, $configId = 1)
    {
        $config = target('admin/ConfigUpload')->getInfo($configId);
        if(empty($config)){
            $config = target('admin/ConfigUpload')->getInfo(1);
        }
        $path = 'uploads/'.date('Y-m-d') . '/';
        //上传
        $upload = new \framework\ext\UploadFile();
        $upload->savePath = ROOT_PATH.$path;
        $upload->allowExts = explode(',', $config['upload_exts']);
        $upload->maxSize = intval($config['upload_size'])*1024*1024;
        $upload->saveRule = 'md5_file';
        if (!$upload->upload()) {
            $this->error = $upload->getErrorMsg();
            return false;
        }
        //上传信息
        $info = $upload->getUploadFileInfo();
        $info = current($info);
        //设置基本信息
        $file = $path . $info['savename'];
        $fileUrl = ROOT_URL . $file;
        $fileName = pathinfo($info['savename']);
        $fileName = $fileName['filename'];
        $fileTitle = pathinfo($info['name']);
        $fileTitle = $fileTitle['filename'];
        $fileExt = $info['extension'];
        //处理图片数据
        $imgType = array('jpg','jpeg','png','gif','bmp');
        if(in_array(strtolower($fileExt), $imgType)){
            //设置图片驱动
            $image = new \app\base\util\ThinkImage();
            //设置缩图
            if($config['thumb_status']){
                $image->open(ROOT_PATH . $file);
                $thumbFile = $path.'thumb_'.$info['savename'];
                $status = $image->thumb($config['thumb_width'], $config['thumb_height'], $config['thumb_type'])->save(ROOT_PATH . $thumbFile);
                if($status){
                    $file = $thumbFile;
                }
            }
            //设置水印
            if($config['water_status']){
                $image->open(ROOT_PATH . $file);
                $wateFile = $path.'wate_'.$info['savename'];
                $status = $image->water(ROOT_PATH . 'public/watermark/'.$config['water_image'],$config['water_position'])->save(ROOT_PATH . $wateFile);
                if($status){
                    $file = $wateFile;
                }
            }
        }
        //录入文件信息
        $data = array();
        $data['url'] = ROOT_URL . $file;
        $data['original'] = $fileUrl;
        $data['title'] = $fileTitle;
        $data['ext'] = $fileExt;
        $data['size'] = $info['size'];
        $data['time'] = time();
        $this->add($data);
        return $data;
    }

}
