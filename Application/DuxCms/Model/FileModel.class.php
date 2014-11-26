<?php
namespace DuxCms\Model;
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
    public function uploadData($files, $config = array())
    {
        //上传
        $upload = new \Think\Upload($config,'Local');
        $info = $upload->upload($files);
        $info = current($info);
        if($info){
            // 记录文件信息
            $fileDir = 'Uploads/'.$info['savepath'];
            $file = $fileDir.$info['savename'];
            $info['title'] = $info['name'];
            $info['original'] = __ROOT__ .'/'.$file;
            //处理图片数据
            $imgType = array('jpg','jpeg','png','gif','bmp');
            if(in_array($info['ext'], $imgType)){
                //设置图片驱动
                $image = new \Think\Image();
                //设置缩图
                if(C('THUMB_STATUS')){
                    $image->open(ROOT_PATH . $file);
                    $thumbFile = $fileDir.'thumb_'.$info['savename'];
                    $status = $image->thumb(C('THUMB_WIDTH'), C('THUMB_HEIGHT'), C('THUMB_TYPE'))->save(ROOT_PATH . $thumbFile);
                    if($status){
                        $file = $thumbFile;
                    }
                }
                //设置水印
                if(C('WATER_STATUS')){
                    $image->open(ROOT_PATH . $file);
                    $wateFile = $fileDir.'wate_'.$info['savename'];
                    $status = $image->water(ROOT_PATH . 'Public/watermark/'.C('WATER_IMAGE'),C('WATER_POSITION'))->save(ROOT_PATH . $wateFile);
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
