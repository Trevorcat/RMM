<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

/**
 * @var getTheData theDatas 交互数据库的核心类
 * 此为ButtonOkClick controller 的model
 * @author 陈科杰 15520446187
 * @version 1.1.1.20170506
 */
class buttonOkClick extends Model
{
    /**
     * 构造函数
     */
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param array $post 用于接受post数据
     *
     * @var array   database 存放需要查询的数据库名
     *              diseases 存放筛选完成符合所有条件的病害
     *              where 存放查询条件
     *              num 表示当前遍历$post['TunnelInfo']['ExaminationTime']的下标
     *              ExaminationTime 表示当前遍历$post['TunnelInfo']['ExaminationTime']对应的检测时间
     *              type 表示当前选项的种类
     *              choose 表示当前选项的值
     *              result 整理已筛选的病害
     *        
     *
     * @return array $diseaseInfo 返回整理好的格式化数据
     *
     * 接受请求数据，自动解析请求数据格式，返回符合条件的数据
     *
     * @author 陈科杰 15520446187
     * @version 1.1.1.20170506
     */
    public function getTheDisease($post){
    	$database = $post['TunnelInfo']['TunnelId'];
        $diseases = NULL;
        $where = NULL;
        //遍历接收到的请求
        foreach ($post['TunnelInfo']['ExaminationTime'] as $num => $ExaminationTime) {
            //遍历请求中的filter字段，获取查询范围
            foreach ($post['Filter'] as $type => $choose) {
                //如果此次循环中的选项被选中
                if ($choose['Select'] == 1) {
                    /*遍历选项中的字段
                        @var array chooseType 选择字段的类型
                             array range 选择字段里的值
                    */
                    foreach ($choose as $chooseType => $range) {
                        //若选项字段不是select字段
                        if ($chooseType != 'Select') {
                            /*遍历该字段中的内容
                                @var array name 遍历到的内容字段名
                                     array value 便利到的内容字段的值
                            */
                            foreach ($range as $name => $value) {
                                //如果字段中的内容为空，则自动判断该字段为最大值字段或最小值字段并自动赋值
                                if ($value == NULL) {
                                    if (strstr($name, 'Max')) {
                                        $where[$type . $name] = 99999;
                                    }elseif (strstr($name, 'Min')) {
                                        $where[$type . $name] = 0;
                                    }
                                }
                                //如果该字段不为整型
                                elseif (!is_integer($value)) {
                                    $error['error'] = 1;
                                    $error['reason'] = 'the parameter is not integer type';
                                    return $error;
                                }
                                //将该字段的值赋值给where变量
                                else{
                                    $where[$type . $name] = $value;
                                }
                            }
                        }
                    }
                    //如果内存中不存在where变量或者 当前循环的type值为'Exception'则进入此程序块
                    /*
                        @var array diseaseSelected 存放已从数据库中查找到的所有数据
                    */
                    if (isset($where) || $type == 'Exception') {
                        //type 是否为'Exception'，若是，则直接查找符合条件的所有异常病害，若否，则通过where变量查找符合要求的相应病害
                        $diseaseSelected = $type == 'Exception' ? $this->theDatas->rangeSearchForOkClick($database, strtolower($type).'_disease', '', $ExaminationTime, '') : $this->theDatas->rangeSearchForOkClick($database, strtolower($type).'_disease', $where, $ExaminationTime, '');
                        //如果没有查询到数据，则跳过这个循环
                        if (count($diseaseSelected) == 0) {
                            continue;
                        }
                        
                        $selectDiseaseNum = 0; // 初始化selectDiseaseNum
                        
                        //遍历查询到的数据拼接sql语句并向数据库请求数据
                        /*
                            @var array diseaseNum 遍历时的下标
                                       diseaseValue 遍历到的值
                                       disease 用于存放查询到的值
                                       diseases 用于存放符合要求的值
                        */
                        foreach ($diseaseSelected as $diseaseNum => $diseaseValue) {
                            if (isset($where)) {
                                unset($where); 
                            }                
                            $where['DiseaseID'] = $diseaseValue->DiseaseID;
                            $disease[$type][$diseaseNum] = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', $where == NULL? '':$where, '')[0];

                            unset($where);
                            //如果查询到的数据不满足距离范围，则跳过此循环
                            if ($disease[$type][$diseaseNum]->Mileage >= $post['EndMileage'] || $disease[$type][$diseaseNum]->Mileage < $post['StartMileage']) {
                                continue;
                            }
                            $diseaseValue->DiseasePosition['Mileage'] = $disease[$type][$diseaseNum]->Mileage;
                            $diseaseValue->DiseasePosition['Position'] = $disease[$type][$diseaseNum]->Position;
                            $disease[$type][$diseaseNum]->PNGURL = $diseaseValue->PNGFile;
                            unset($disease[$type][$diseaseNum]->Mileage);
                            unset($disease[$type][$diseaseNum]->Position);
                            unset($diseaseValue->PNGFile);

                            $disease[$type][$diseaseNum]->Info = $diseaseValue;
                            $diseases[$type][isset($diseases[$type])?(count($diseases[$type]) ):0] = $disease[$type][$diseaseNum];
                            $selectDiseaseNum ++;
                        }
                    } 
                }   
            }
        }
        //如果存在整理好的diseases变量，则整理变量存入result变量中
        if ($diseases != NULL) {
            $counts = 0;
            foreach ($diseases as $types => $diseaseIn) {
                foreach ($diseaseIn as $key => $value) {
                    $result[$counts] = $value;
                    $counts ++;
                }
                 
            }
        }
    	//result变量是否被申明，若存在则赋值给diseaseInfo['DiseaseInfo']，若内存中不存在，则返回错误信息
    	$diseaseInfo['DiseaseInfo'] = isset($result) ? $result : NULL;
    	return $diseaseInfo;
    }
}
