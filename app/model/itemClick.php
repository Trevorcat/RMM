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
 * ItemClick controller对应的model
 */
class itemClick extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param string database 用于确定需要查询的数据库
     * @param array start 用于确定病害的位置范围
     *
     * @var array where 存放查询条件
     *            
     * @return data 存放查询得到的数据
     */
    public function getDiseaseInfo($database, $start)
    {
        $where['start'] = $start->Mileage;
        $where['range'] = 40;
        $where['col'] = 'Mileage';
    	$data = $this->theDatas->rangeSearch($database, 'disease', $where, '');
    	return $data;
    }

    /**
     * @param string database 存放需要查询的数据库名
     *               type 存放需要查询的病害类型
     *               time 存放病害检测日期
     *               diseaseId 存放病害的ID
     *               start 存放查询位置范围的其实里程
     *
     * @var array where 存放整理的需要查询的条件
     *            detail 存放从数据库中查询相关条件得到的数据
     * @return detail 存放查询得到的查询数据
     */
    public function getDiseaseDetail($database, $type, $time, $diseaseId, $start = ''){
        //如果没有起始里程，则之间前往表查询相关病害
        if ($start == '') {
            $where['DiseaseId'] = $diseaseId;
            $detail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $time);
        }
        //如果存在起始里程，则添加起始里程条件查询
        else{
            $where['start'] = $start->Mileage;
            $where['range'] = $start->Mileage + 20;
            $where['col'] = 'Mileage';
            $detail = $this->theDatas->rangeSearch($database, $type, $where, $time, '');
        }
         
    	return $detail;

    }

    /**
     * @param string database 存放需要查询的数据库名
     *
     * @var array data 用于存放从数据库中查询得到距离入口最近的病害信息
     *            where 用于存放查询条件
     * 
     * @return array startDiseaseId 存放从数据库中查询到符合条件的数据
     */
    public function getTheStartMileage($database){
        $data = $this->theDatas->theMinOfCol($database, 'disease', 'Mileage', '');
        $where['Mileage'] = $data[0]->min;
        $startDiseaseId = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', $where, '');
        return $startDiseaseId[0];
    }
}
