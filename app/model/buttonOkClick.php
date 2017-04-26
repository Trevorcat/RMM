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
    	$database = $post['TunnelInfo']['TunnelID'];
    	foreach ($post['Filter'] as $type => $choose) {
    		if ($choose['select'] == 1) {
    			foreach ($choose as $key => $value) {
    				if ($key != 'select') {
    					$where[$key] = $value;
    				}
    			}
    			$where['start'] = $post['StartMileage'];
    			$where['range'] = $post['EndMileage'] - $post['StartMileage'];
    			$where['col'] = 'Mileage';
    			switch ($type) {
    				case 'Crack':					//裂缝cracks
	    				$DiseaseType = 0;
	    				break;
	    			case 'Leak':					//漏洞leaks
	    				$DiseaseType = 1;
	    				break;
	    			case 'Drop':					//掉块drop
	    				$DiseaseType = 2;
	    				break;	
	    			case 'Scratch':					//划痕scratch
	    				$DiseaseType = 3;
	    			break;
	    			default:					//异常exception
	    				$DiseaseType = 4;
	    				break;
    			}
    			$whereCol['DiseaseType'] = $DiseaseType;
    			$whereCol['FoundTime'] = $post['TunnelInfo']['ExaminationTime'];
    			$diseaseSelected = $this->theDatas->rangeSearch($database, 'disease', $where, '', $whereCol);
    			unset($where);
    			foreach ($diseaseSelected as $diseaseNum => $diseaseValue) {    				$where['DiseaseID'] = $diseaseValue->DiseaseID;
    				$disease[$type][$diseaseNum] = $this->theDatas->getDataByTablenameAndDatabasename($database, $type . '_disease', $where, $post['TunnelInfo']['ExaminationTime'])[0];
    			}
    			
    		}	
    	}
    	$diseaseInfo['DiseaseInfo'] = $disease;
    	return $diseaseInfo;
    }
}
