<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class buttonSearchClick extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getTheDisease($post){
    	$database = $post['TunnelInfo']['TunnelId'];
        $diseases = NULL;
        $where = NULL;
        foreach ($post['TunnelInfo']['ExaminationTime'] as $num => $ExaminationTime) {
            foreach ($post['Filter'] as $type => $choose) {

                if ($choose['Select'] == 1) {
                    foreach ($choose as $chooseType => $range) {
                        if ($chooseType != 'Select') {
                            foreach ($range as $name => $value) {
                                if ($value == NULL) {
                                    if (strstr($name, 'Max')) {
                                        $where[$type . $name] = 99999;
                                    }elseif (strstr($name, 'Min')) {
                                        $where[$type . $name] = 0;
                                    }
                                }elseif (!is_integer($value)) {
                                    $error['error'] = 1;
                                    $error['reason'] = 'the parameter is not integer type';
                                    return $error;
                                }else{
                                    $where[$type . $name] = $value;
                                }
                            }
                        }
                    }
                    if (isset($where) || $type == 'Exception') {
                        $diseaseSelected = $type == 'Exception' ? $this->theDatas->rangeSearchForOkClick($database, strtolower($type).'_disease', '', $ExaminationTime, '') : $this->theDatas->rangeSearchForOkClick($database, strtolower($type).'_disease', $where, $ExaminationTime, '');
                        if (count($diseaseSelected) == 0) {
                            continue;
                        }
                        
                        $selectDiseaseNum = 0;
                        foreach ($diseaseSelected as $diseaseNum => $diseaseValue) {
                            if (isset($where)) {
                                unset($where); 
                            }                
                            $where['DiseaseID'] = $diseaseValue->DiseaseID;
                            $disease[$type][$diseaseNum] = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', $where == NULL? '':$where, '')[0];

                            unset($where);

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
        if ($diseases != NULL) {
            $counts = 0;
            foreach ($diseases as $types => $diseaseIn) {
                foreach ($diseaseIn as $key => $value) {
                    $resoult[$counts] = $value;
                    $counts ++;
                }
                 
            }
        }
    	
    	$diseaseInfo['DiseaseInfo'] = isset($resoult) ? $resoult : 'Nothing been searched by the select';
    	return $diseaseInfo;
    }
}
