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
     * 获取信息
     * @param int $fragmentId ID
     * @return array 信息
     */
    public function getInfo($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        $data = $this->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            return $this->add();
        }
        if($type == 'edit'){
            if(empty($data['fragment_id'])){
                return false;
            }
            $status = $this->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除信息
     * @param int $fragmentId ID
     * @return bool 删除状态
     */
    public function delData($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
        return $this->where($map)->delete();
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
