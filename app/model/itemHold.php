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
        if (!isset($post['DiseaseInfo']['TunnelID'])) {
            return $error['error'] = 'There is no \'DiseaseInfo => TunnelID\'';
        }elseif (!isset($post['DiseaseInfo']['DiseaseType'])) {
            return $error['error'] = 'There is no \'DiseaseInfo => DiseaseType\'';
        }elseif (!isset($post['DiseaseInfo']['FoundTime'])) {
            return $error['error'] = 'There is no \'DiseaseInfo => FoundTime\'';
        }elseif (!isset($post['DiseaseInfo']['DiseaseID'])) {
            return $error['error'] = 'There is no \'DiseaseInfo => DiseaseID\'';
        }else{
            $database = $post['DiseaseInfo']['TunnelID'];
            switch ($post['DiseaseInfo']['DiseaseType']) {
                case '0':                   //裂缝cracks
                        $type = 'crack_disease';
                        break;
                    case '1':                   //漏洞leaks
                        $type = 'leak_disease';
                        break;
                    case '2':                   //掉块drop
                        $type = 'drop_disease';
                        break;  
                    case '3':                   //划痕scratch
                        $type = 'scratch_disease';
                    break;
                    default:                    //异常exception
                        $type = 'exception_disease';
                        break;
            }
            $where['DiseaseID'] = $post['DiseaseInfo']['DiseaseID'];
            $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $post['DiseaseInfo']['FoundTime']);
            if (count($theDetail) == 0) {
                return $error['error'] = 'can not find anything';
            }
            return $theDetail[0];
        }
    	
    }
}
