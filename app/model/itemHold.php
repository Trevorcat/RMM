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
        if (!isset($post['DiseaseInfo']['TunnelId'])) {
            return $error['error'] = 'There is no \'DiseaseInfo => TunnelId\'';
        }elseif (!isset($post['DiseaseInfo']['ExaminationTime'])) {
            return $error['error'] = 'There is no \'DiseaseInfo => ExaminationTime\'';
        }else{
            $database = $post['DiseaseInfo']['TunnelId'];
            for($Diseasetype = 0; $Diseasetype <= 4; $Diseasetype ++){
                switch ($Diseasetype) {
                    case '0':                   //裂缝cracks
                        $type = 'crack_disease';
                        $theDetail[$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $post['DiseaseInfo']['ExaminationTime']);
                        break;
                    case '1':                   //漏洞leaks
                        $type = 'leak_disease';
                        $theDetail[$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $post['DiseaseInfo']['ExaminationTime']);
                        break;
                    case '2':                   //掉块drop
                        $type = 'drop_disease';
                        $theDetail[$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $post['DiseaseInfo']['ExaminationTime']);
                        break;  
                    case '3':                   //划痕scratch
                        $type = 'scratch_disease';
                        $theDetail[$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $post['DiseaseInfo']['ExaminationTime']);
                    break;
                    default:                    //异常exception
                        $type = 'exception_disease';
                        $theDetail[$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $post['DiseaseInfo']['ExaminationTime']);
                        break;
                }   
            
            }
            $time = 0;
            foreach ($theDetail as $Diseasestype => $Diseases) {
                foreach ($Diseases as $key => $value) {
                    $theDetails[$time] = $value;
                    $time++;
                }
            }
            if (count($theDetails) == 0) {
                return $error['error'] = 'can not find anything';
            }
            return $theDetail;
        }
    	
    }
}
