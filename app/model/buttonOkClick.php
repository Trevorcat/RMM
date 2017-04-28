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
        $where = NULL;
        foreach ($post['TunnelInfo']['ExaminationTime'] as $num => $ExaminationTime) {
            foreach ($post['Filter'] as $type => $choose) {

                if ($choose['Select'] == 1 && $type != 'Scratch' && $type != 'Exception') {

                    foreach ($choose as $chooseType => $range) {
                        if ($chooseType != 'Select') {
                            foreach ($range as $name => $value) {
                                if ($value == NULL && $value != 0) {
                                    break;
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
                    $diseaseSelected = $this->theDatas->rangeSearchForOkClick($database, strtolower($type).'_disease', $where, $ExaminationTime, '');
                    if ($diseaseSelected == 0) {
                        continue;
                    }
                    unset($where);
                    $selectDiseaseNum = 0;
                    foreach ($diseaseSelected as $diseaseNum => $diseaseValue) {                    
                        $where['DiseaseID'] = $diseaseValue->DiseaseID;
                        $disease[$type][$diseaseNum] = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', $where == NULL? '':$where, '')[0];
                        unset($where);

                        if ($disease[$type][$diseaseNum]->Mileage > $post['EndMileage'] || $disease[$type][$diseaseNum]->Mileage < $post['StartMileage']) {
                            continue;
                        }
                        $diseaseValue->Position['Mileage'] = $disease[$type][$diseaseNum]->Mileage;
                        $diseaseValue->Position['Position'] = $disease[$type][$diseaseNum]->Position;
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
