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
 * ItemHold controller对应的model
 */
class itemHold extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param array post 接受来自controller的过滤后数据
     *
     * @var array where 存放查询条件
     *            theDetail 存放查询到符合条件的数据
     * @var string ExaminationTime 存放查询时间
     *             database 存放查询数据库名
     *            
     * @return data 存放查询得到的数据
     */
    public function getTheDetail($post){
        $database = $post['TunnelInfo']['TunnelId'];
        $ExaminationTime = $post['DiseaseInfo']['FoundTime'];
        $where['DiseaseID'] = $post['DiseaseInfo']['DiseaseID'];
        //遍历5中病害类型代码
        /**
         * @var int DiseaseType 表示病害类型代码 
         * @var string type 存放病害详细表的后缀与名字的拼接字符串
         */
        for($DiseaseType = 0; $DiseaseType <= 4; $DiseaseType ++){
            //按病害类型编码查询不同的详细表
            switch ($DiseaseType) {
                case '0':                   //裂缝cracks
                $type = 'crack_disease';
                $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $ExaminationTime);
                    
                break;
                case '1':                   //漏洞leaks
                $type = 'leak_disease';
                $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $ExaminationTime);
                    
                break;
                case '2':                   //掉块drop
                $type = 'drop_disease';
                $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $ExaminationTime);
                        
                break;  
                case '3':                   //划痕scratch
                $type = 'scratch_disease';
                $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $ExaminationTime);
                        
                break;
                default:                    //异常exception
                $type = 'exception_disease';
                $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $ExaminationTime);
                        
                break;
            }
            //如果当次循环查询到数据，则中断此循环
            if (count($theDetail) != 0) {
                break;
            }
        }
        //若经过循环遍历后依然没有查到数据，则返回错误信息
        if (count($theDetail) == 0) {
            $error['error'] = 1;
            $error['error'] = 'can not find anything'
            return $error;
        }
            
        return $theDetail;
            
            
    }
}
