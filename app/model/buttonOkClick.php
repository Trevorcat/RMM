<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class buttonOkClick extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getTheDisease($post){
    	$database = $post['TunnelInfo']['TunnelId'];
        $diseases = NULL;
    	foreach ($post['Filter'] as $type => $choose) {
    		if ($choose['Select'] == 1 && $type != 'Scratch' && $type != 'Exception') {
    			foreach ($choose as $chooseType => $range) {
    				if ($chooseType != 'Select') {
    					foreach ($range as $name => $value) {
                            var_dump($value);
                            if (!is_integer($value)) {
                                $error['error'] = 1;
                                $error['reason'] = 'the parameter is not integer type';
                                return $error;
                            }
                            $where[$type . $name] = $value;
                        }
    				}
    			}
    			$diseaseSelected = $this->theDatas->rangeSearchForOkClick($database, strtolower($type).'_disease', $where, $post['TunnelInfo']['ExaminationTime'], '');
    			unset($where);
                $selectDiseaseNum = 0;
    			foreach ($diseaseSelected as $diseaseNum => $diseaseValue) {    				
                    $where['DiseaseID'] = $diseaseValue->DiseaseID;
    				$disease[$type][$diseaseNum] = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', $where, '')[0];
                    unset($where);
                    if ($disease[$type][$diseaseNum]->Mileage > $post['EndMileage'] || $disease[$type][$diseaseNum]->Mileage < $post['StartMileage']) {
                        continue;
                    }
                    $diseases[$type][$selectDiseaseNum] = $diseaseValue;
                    $selectDiseaseNum ++;
    			}
    			
    		}	
    	}
    	$diseaseInfo['DiseaseInfo'] = isset($diseases) ? $diseases : 'Nothing been searched by the select';
    	return $diseaseInfo;
    }
}
