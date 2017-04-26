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
        $post = $request->json()->all();
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            return $error['error'] = 'There is no \'TunnelId\' in POST';
        }
    	$database = $post['TunnelInfo']['TunnelId'];

        

    	$theDiseaseInfo = $this->diseaseInfo($database, $this->itemclick->getTheStartMileage($database));
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
    		$detail = $this->getDiseaseDetail($database, $type, $disease->FoundTime, $disease->DiseaseID, $this->itemclick->getTheStartMileage($database));
            $detail[0]->DiseasePostion['Mileage'] = $theDiseaseInfo[$diseaseNum]->Mileage;
            $detail[0]->DiseasePostion['Position'] = $theDiseaseInfo[$diseaseNum]->Position;
            unset($theDiseaseInfo[$diseaseNum]->Mileage);
            unset($theDiseaseInfo[$diseaseNum]->Position);
            $theDiseaseInfo[$diseaseNum]->PNGURL = $detail[0]->PNGFile;
            unset($detail[0]->PNGFile);
    		$theDiseaseInfo[$diseaseNum]->Info = $detail[0];
    	}
        $theDiseaseInfo['StartMileage'] = $this->itemclick->getTheStartMileage($database)->DiseaseID;
    	$TheDiseaseInfo['DiseasesInfo'] = $theDiseaseInfo;
        return $TheDiseaseInfo;
    }

    public function diseaseInfo($database, $start){
    	$diseaseInfo = $this->itemclick->getDiseaseInfo($database, $start);
    	return $diseaseInfo;
    }

    public function getDiseaseDetail($database, $type, $time, $diseaseId){
    	$detail = $this->itemclick->getDiseaseDetail($database, $type, $time, $diseaseId);
    	unset($detail[0]->DiseaseID);
    	return $detail;
    }
}
