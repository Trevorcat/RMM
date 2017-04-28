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
            for($Diseasetype = 0; $Diseasetype <= 4; $Diseasetype ++){
                switch ($Diseasetype) {
                        case '0':                   //裂缝cracks
                        $type = 'crack_disease';
                        $theDetail[$time][$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $ExaminationTime);
                        break;
                        case '1':                   //漏洞leaks
                        $type = 'leak_disease';
                        $theDetail[$time][$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $ExaminationTime);
                        break;
                        case '2':                   //掉块drop
                        $type = 'drop_disease';
                        $theDetail[$time][$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $ExaminationTime);
                        break;  
                        case '3':                   //划痕scratch
                        $type = 'scratch_disease';
                        $theDetail[$time][$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $ExaminationTime);
                        break;
                        default:                    //异常exception
                        $type = 'exception_disease';
                        $theDetail[$time][$Diseasetype] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, '', $ExaminationTime);
                        break;
                    }   
                    
                }
            
            $time = 0;
            foreach ($theDetail as $Diseasestype => $Diseases) {
                foreach ($Diseases as $Diseasetype => $disease) {
                    foreach ($disease as $key => $value) {
                        $theDetails[$time] = $value;
                        $time++;
                    }
                }
            }
            if (count($theDetails) == 0) {
                return $error['error'] = 'can not find anything';
            }
            // var_dump($theDetails);
            return $theDetails;
            
            
        }
    }
