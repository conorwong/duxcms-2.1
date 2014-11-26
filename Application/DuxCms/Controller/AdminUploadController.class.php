<?php
namespace DuxCms\Controller;
use Admin\Controller\AdminController;
/**
 * 文件上传
 */
class AdminUploadController extends AdminController
{
    /**
     * 文件上传
     */
    public function upload()
    {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => '');
        $file = D('File');
        $info = $file->uploadData($_FILES);
        if ($info)
        {
            $return['data'] = $info;
        }
        else
        {
            $return['status'] = 0;
            $return['info'] = $file->getError();
        }
        $this->ajaxReturn($return);
    }

    /**
     * 编辑器上传
     */
    public function editor()
    {
        $file = D('File');
        $info = $file->uploadData($_FILES);
        if ($info){
            $return = array(
                'error' => 0,
                'url' => $info['url'],
                'info' => $info,
                );
        }else{
            $return = array(
                'error' => 1,
                'message' => $file->getError(),
                );
        }
        $this->ajaxReturn($return);
    }
}

