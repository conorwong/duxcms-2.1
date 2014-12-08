<?php
namespace DuxCms\Model;
use Think\Model;
/**
 * 蜘蛛统计操作
 */
class TotalSpiderModel extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where,$limit){
        return  $this->where($where)->limit($limit)->order('time desc')->select();
    }

    /**
     * 获取统计
     * @return int 数量
     */
    public function countList($where){
        return  $this->where($where)->count();
    }

    /**
     * 判断蜘蛛爬行
     */
    public function addData(){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if(strpos($tmp, 'Googlebot') !== false){
            $boot = 'google';
        }
        if(strpos($tmp, 'Baiduspider') !== false){
            $boot = 'baidu';
        }
        if(strpos($tmp, 'Sosospider') !== false){
            $boot = 'soso';
        }
        if(empty($boot)){
            return ;
        }
        //当天时间
        $time = strtotime(date('Y-m-d'));
        $where = array();
        $where['time'] = $time;
        $info = $this->where($where)->find();
        if($info){
            $where = array();
            $where['id'] = $info['id'];
            $this->where($where)->setInc($boot);
        }else{
            $data = array();
            $data['time'] = $time;
            $data[$boot] = 1;
            $this->add($data);
        }
    }

    /**
     * 生成蜘蛛时间数据
     * @param int $num 数量
     * @param int $type 类型
     * @return array 信息
     */
    public function getJson($num , $type = 'day', $date = 'Y-m-d'){
        $jsonArray = array();
        $jsonArray['labels'] = array();
        $datasets[1] = D('TotalVisitor')->getChart('blue');
        $datasets[1]['label'] = '百度';
        $datasets[2] = D('TotalVisitor')->getChart('green');
        $datasets[2]['label'] = '谷歌';
        $datasets[3] = D('TotalVisitor')->getChart('orange');
        $datasets[3]['label'] = '搜搜';
        $timeArray = array();
        for ($i=0; $i < $num; $i++) {
            $timeNow = strtotime("-".$i." ".$type,strtotime(date('Y-m-d 0:0:0')));
            $jsonArray['labels'][] = date($date,$timeNow);
            $where = array();
            $where['time'] = array(array('EGT', $timeNow), array('LT', strtotime('+ 1 '.$type, $timeNow)),'and');

            $sum = $this->where($where)->sum('baidu');
            if($sum){
                $datasets[1]['data'][] = $sum;
            }else{
                $datasets[1]['data'][] = 0;
            }
            $sum = $this->where($where)->sum('google');
            if($sum){
                $datasets[2]['data'][] = $sum;
            }else{
                $datasets[2]['data'][] = 0;
            }
            $sum = $this->where($where)->sum('soso');
            if($sum){
                $datasets[3]['data'][] = $sum;
            }else{
                $datasets[3]['data'][] = 0;
            }

        }
        $jsonArray['labels'] = array_reverse($jsonArray['labels']);
        $datasets[1]['data'] = array_reverse($datasets[1]['data']);
        $datasets[2]['data'] = array_reverse($datasets[2]['data']);
        $datasets[3]['data'] = array_reverse($datasets[3]['data']);
        $jsonArray['datasets'] = $datasets;
        return json_encode($jsonArray);
        
    }

}
