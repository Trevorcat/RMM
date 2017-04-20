<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemClick extends Controller
{
    //
    public function __construct(){
    	$this->itemclick = new \App\model\itemClick();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function returnDiseaseInfo(Request $request){
        $post = $request->all();
        if (!isset($post['TunnelId'])) {
            return $error['error'] = 'There is no \'TunnelId\' in POST';
        }
    	$database = $post['TunnelId'];
    	$theDiseaseInfo = $this->diseaseInfo($database);
    	foreach ($theDiseaseInfo as $diseaseNum => $disease) {
    		switch ($disease->DiseaseType) {
    			case '0':					//裂缝cracks
    				$type = 'crack_disease';
    				break;
    			case '1':					//漏洞leaks
    				$type = 'leak_disease';
    				break;
    			case '2':					//掉块drop
    				$type = 'drop_disease';
    				break;	
    			case '3':					//划痕scratch
    				$type = 'scratch_disease';
    			break;
    			default:					//异常exception
    				$type = 'exception_disease';
    				break;
    		}
    		$detail = $this->getDiseaseDetail($database, $type, $disease->FoundTime, $disease->DiseaseID);
    		$theDiseaseInfo[$diseaseNum]->DiseaseInfo = $detail[0];
    	}
        $theDiseaseInfo['StartMileage'] = $this->itemclick->getTheStartMileage($database);
    	$TheDiseaseInfo['DiseaseInfo'] = $theDiseaseInfo;
        return $TheDiseaseInfo;
    }

    public function diseaseInfo($database){
    	$diseaseInfo = $this->itemclick->getDiseaseInfo($database);
    	return $diseaseInfo;
    }

    public function getDiseaseDetail($database, $type, $time, $diseaseId){
    	$detail = $this->itemclick->getDiseaseDetail($database, $type, $time, $diseaseId);
    	unset($detail[0]->DiseaseID);
    	return $detail;
    }
}