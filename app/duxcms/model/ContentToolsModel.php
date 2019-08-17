<?php
namespace app\duxcms\model;

use app\base\model\BaseModel;

/**
 * 内容工具
 */
class ContentToolsModel
{

    /**
     * 获取内容指定图片
     * @param string $content 内容
     * @param int $num 第N张图片
     * @return string 图片URL
     */
    public function getImage($content, $num = 1)
    {
        $content = html_out($content);
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"]/i', $content, $matches);
        $num = $num - 1;
        $img = $matches[1][$num];
        return $img;
    }

    /**
     * 获取关键词
     * @param string $title 标题
     * @return string keywords
     * 群众们应该感谢厂长的分词服务 weibo @梁斌penny https://weibo.com/pennyliang
     */
    public function getKerword($title)
    {
        $word = urlencode($title);
        $url = "http://api.pullword.com/get.php?source={$word}&param1=0&param2=1&json=1";

        $data= \framework\ext\Http::doGet($url, 5);
        $list = json_decode($data, true);
        if (empty($data)) {
            return;
        }

        $keywords = array();
        foreach ($list as $value) {
            $keywords[] = $value['t'];
        }
        return implode(',', $keywords);
    }

    /**
     * 远程抓图
     * @param string $content 内容
     * @return string 抓取后内容
     */
    public function getRemoteImage($content)
    {
        if (empty($content)) {
            return $content;
        }
        $filesName = date('Y-m-d').'/';
        //文件路径
        $filePath = './uploads/'.$filesName;
        //文件URL路径
        $fileUrl = __ROOT__ .'/uploads/'. $filesName;
        $body=htmlspecialchars_decode($content);
        $imgArray = array();
        preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|bmp|png))/isU", $body, $imgArray);
        $imgArray = array_unique($imgArray[2]);
        set_time_limit(0);
        $milliSecond = date("dHis") . '_';
        if (!is_dir($filePath)) {
            @mkdir($filePath, 0777, true);
        }
        $http = new \framework\ext\Http;
        foreach ($imgArray as $key =>$value) {
            $value = trim($value);
            $ext=explode('.', $value);
            $ext=end($ext);
            $getFile = $http->doGet($value, 5);
            $getfileName = $milliSecond.$key.'.'.$ext;
            $getFilePath = $filePath.$getfileName;
            $getFileUrl = $fileUrl.$getfileName;
            if ($getFile) {
                if (@file_put_contents($getFilePath, $getFile)) {
                    $body = str_replace($value, $getFileUrl, $body);
                }
            }
        }
        return $body;
    }
}
