<?php
namespace Admin\Model;
use Think\Model;
/**
 * 文件操作
 */
class FileModel extends Model {
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
        $config = D('Admin/ConfigUpload')->getInfo($configId);
        if(empty($config)){
            $config = D('Admin/ConfigUpload')->getInfo(1);
        }
        //上传
        $upload = new \Think\Upload($config,'Local');
        $upload->maxSize = intval($config['upload_size'])*1024*1024;
        $upload->exts = explode(',', $config['upload_exts']);
        $upload->replace = $config['upload_replace'];
        $info = $upload->upload($files);
        $info = current($info);
        if($info){
            // 记录文件信息
            $fileDir = 'Uploads/'.$info['savepath'];
            $file = $fileDir.$info['savename'];
            $info['title'] = $info['name'];
            $info['original'] = __ROOT__ .'/'.$file;
            $info['ext'] = strtolower($info['ext']);
            //处理图片数据
            $imgType = array('jpg','jpeg','png','gif','bmp');
            if(in_array($info['ext'], $imgType)){
                //设置图片驱动
                $image = new \Think\Image();
                //设置缩图
                if($config['thumb_status']){
                    $image->open(ROOT_PATH . $file);
                    $thumbFile = $fileDir.'thumb_'.$info['savename'];
                    $status = $image->thumb($config['thumb_width'], $config['thumb_height'], $config['thumb_type'])->save(ROOT_PATH . $thumbFile);
                    if($status){
                        $file = $thumbFile;
                    }
                }
                //设置水印
                if($config['water_status']){
                    $image->open(ROOT_PATH . $file);
                    $wateFile = $fileDir.'wate_'.$info['savename'];
                    $status = $image->water(ROOT_PATH . 'Public/watermark/'.$config['water_image'],$config['water_position'])->save(ROOT_PATH . $wateFile);
                    if($status){
                        $file = $wateFile;
                    }
                }
            }
            $info['url'] = __ROOT__ .'/'.$file;
            //入库文件信息
            $this->create($info);
            $this->add();
            return $info;
        } else {
            $this->error = $upload->getError();
            return false;
        }
    }

}
