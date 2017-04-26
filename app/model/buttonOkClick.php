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
    	foreach ($post['Filter'] as $type => $choose) {
    		if ($choose['Select'] == 1 && $type != 'Scratch' && $type != 'Exception') {
    			foreach ($choose as $chooseType => $range) {
    				if ($chooseType != 'Select') {
    					foreach ($range as $name => $value) {
                            $where[$type . $name] = $value;
                        }
    				}
    			}
    			
    			$diseaseSelected = $this->theDatas->rangeSearchForOkClick($database, strtolower($type).'_disease', $where, $post['TunnelInfo']['ExaminationTime'], '');
    			unset($where);
    			foreach ($diseaseSelected as $diseaseNum => $diseaseValue) {    				
                    $where['DiseaseID'] = $diseaseValue->DiseaseID;
    				$disease[$type][$diseaseNum] = $this->theDatas->getDataByTablenameAndDatabasename($database, strtolower($type) . '_disease', $where, $post['TunnelInfo']['ExaminationTime'])[0];
                    unset($where);
    			}
    			
    		}	
    	}
        
    	$diseaseInfo['DiseaseInfo'] = isset($disease) ? $disease : 'Nothing been searched by the select';
    	return $diseaseInfo;
    }
}
