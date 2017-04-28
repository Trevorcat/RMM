<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class itemHold extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getTheDetail($post){
        $database = $post['TunnelInfo']['TunnelId'];
        $ExaminationTime = $post['DiseaseInfo']['FoundTime'];
        $where['DiseaseID'] = $post['DiseaseInfo']['DiseaseID'];
            for($Diseasetype = 0; $Diseasetype <= 4; $Diseasetype ++){
                switch ($Diseasetype) {
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
                    if (count($theDetail) != 0) {
                        break;
                    }
                }
            if (count($theDetail) == 0) {
                return $error['error'] = 'can not find anything';
            }
            
            return $theDetail;
            
            
        }
    }
