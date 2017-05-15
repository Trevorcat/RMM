<?php

/**
 * 版本号 1.1.1.20170506
 * 作者 陈科杰 
 * 联系方式 15520446187
 */

namespace App\model;

use Illuminate\Database\Eloquent\Model;

/**
 * @var getTheData theDatas 与数据库交互的核心库
 *
 * ScanSlided controller对应的model
 */
class scanSlided extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param array post 接受来自controller的过滤后数据
     *
     * @var array where 存放病害查询条件
     *            diseases 存放搜索到的病害信息
     *            result 存放整理后的病害集合
     * @var stdclass theDisease 用于存放符合要求的病害
     * @var int maxMileage 用于存放本条隧道离出口位置最远的病害
     *          resultNum 用于存放遍历下标
     * @var string database 存放需要搜索病害的隧道ID
     *            
     * @return data 存放查询得到的数据
     *
     * 根据具体条件请求数据库获取病害的详细信息
     */
    public function searchTheDisease($post){
        $database = $post['TunnelInfo']['TunnelId'];
        $where['col'] = 'Mileage';
        $where['start'] = $post['Mileage'];
        $where['range'] = 20;//初始化查询距离范围
        $diseases = array();//初始化diseases为数组

        $maxMileage = $this->theDatas->theMaxOfCol($database, 'disease', 'Mileage', '')[0]->max;
        if ($post['Mileage'] < 0) {
            $theDisease['DiseasesInfo'] = NULL;
        }elseif ($post['Mileage'] >= $maxMileage) {
            $theDisease['DiseasesInfo'] = NULL;
        }else{
            /**
             * @var array key 表示当前遍历的下标
             *            ExaminationTime 表示当前遍历到的检测时间
             *            whereCol 存放查询语句中搜索的字段
             *            rangeWhere 存放查询病害类型范围（即存放需要查询那些病害）
             *            theDiseases 存放符合要求的病害
             * 
             * 遍历需要查询的检测时间，整理需要查询的检测时间范围、病害类型范围
             */
            foreach ($post['TunnelInfo']['ExaminationTime'] as $key => $ExaminationTime) {
                $whereCol['FoundTime'] = $ExaminationTime;
                $rangeWhere = array();
                /**
                 * @var string type 表示当前遍历的选项名称
                 *             choose 表示当前选项需要查询的内容
                 * @var int typeNum 存放当前病害类型对应的编码
                 *
                 * 遍历filter字段，获取前端选择需要查询的内容
                 */
                foreach ($post['Filter'] as $type => $choose) {
                    if ($choose['Select'] == 1) {
                        switch ($type) {
                            case 'Crack'://裂缝类型
                                $typeNum = 0;
                                break;
                            case 'Leak'://漏洞类型
                                $typeNum = 1;
                                break;
                            case 'Drop'://掉块类型
                                $typeNum = 2;
                                break;
                            case 'Scratch'://刮擦类型
                                $typeNum = 3;
                                break;
                            default://异常类型
                                $typeNum = 4;
                                break;
                        }
                        array_push($rangeWhere, strtolower($typeNum));
                    }       
                }
                $theDiseases[$key] = $this->theDatas->rangeSearch($database, 'disease', $where, '', $whereCol);
            }
//
            $resultNum = 0;//初始化下标值
            $result = NULL;
            /**
             * @var int time 存放当前遍历下标
             * @var array diseases 存放当前遍历到的病害数组内容
             *
             * 遍历所有查询原始数据集合，将以不同时间分类的集合溶于一个集合
             */
            foreach ($theDiseases as $time => $diseases) {
                /**
                 * @var int key 存放当前下标
                 * @var array value 存放当前遍历到的病害
                 *
                 * 遍历内容集合，将内容整理至result中
                 */
                foreach ($diseases as $key => $value) {
                    $result[$resultNum] = $value;
                    $resultNum ++;
                }
            }
            unset($where);
            if ($result != NULL) {
                /**
                 * @var int diseasesNum 表示遍历整理后内容的下标
                 *          DiseaseType 表示当前遍历到病害的类型编码
                 * @var stdclass diseaseSearch 表示遍历到的病害内容
                 *               theDetail 表示查询到的病害详细信息
                 * @var string Examination 存放当前遍历病害的检测时间
                 *             type 存放需要查询的数据库表名“病害类型+disease”
                 *
                 * 遍历整理好的result，获取查询数据，至各个病害详细表中获取对应内容
                 */
                foreach ($result as $diseasesNum => $diseaseSearch) {
                    $where['DiseaseID'] = $diseaseSearch->DiseaseID;
                    $Examination = $diseaseSearch->FoundTime;
                    $DiseaseType = $diseaseSearch->DiseaseType;
                    /**
                     * 根据不同的病害类型，查询不同病害类型表
                     */
                    switch ($DiseaseType) {
                        case '0':                   //裂缝cracks
                        $type = 'crack_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                        case '1':                   //漏洞leaks
                        $type = 'leak_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                        case '2':                   //掉块drop
                        $type = 'drop_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;  
                        case '3':                   //划痕scratch
                        $type = 'scratch_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                        default:                    //异常exception
                        $type = 'exception_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                    }
                    $result[$diseasesNum]->PNGURL = $theDetail->PNGFile;
                    $theDetail->DiseasePosition['Mileage'] = $result[$diseasesNum]->Mileage;
                    $theDetail->DiseasePosition['Position'] = $result[$diseasesNum]->Position; 
                    $result[$diseasesNum]->Info = $theDetail;
                    unset($theDetail->PNGFile);
                    unset($result[$diseasesNum]->Position);
                    unset($result[$diseasesNum]->Mileage);
                }
            }
            $sureSearch = array();
            if ($result != NULL) {
                /**
                 * @var int key 表示遍历下标
                 * @var array value 表示当前遍历的值
                 * 
                 * 循环遍历查找范围得到value用于从数据库中获取的原始数据做对比
                 */
                foreach ($rangeWhere as $key => $value) {
                    /**
                     * @var int diseasesNum 当前遍历下标
                     * @var stdclass diseaseSearched 当前遍历的病害信息
                     * 
                     * 遍历从数据库中获取的原始数据，对比value将满足要求的病害压入sureSearch
                     */
                    foreach ($result as $diseasesNum => $diseaseSearched) {
                        if ($diseaseSearched->DiseaseType == $value) {                            array_push($sureSearch, $diseaseSearched);
                        }else{
                            continue;
                        }
                    }
                }
                
            }
            $NULL[0] = NULL;
            $theDisease['DiseasesInfo'] = $sureSearch == NULL ? $NULL : $sureSearch;
            $theDisease['StartMileage'] = $post['Mileage'];
            $theDisease['EndMileage'] = $post['Mileage'] + 20;
        }
        return $theDisease;
    }

}
