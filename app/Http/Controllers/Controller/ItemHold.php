<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemHold extends Controller
{
    //
	public function __construct(){
    	$this->itemhold = new \App\model\itemHold();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function returnDiseaseDetail(Request $request){
    	$post = $request->json()->all();
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There is no \'TunnelInfo => TunnelId\'';
        }else if (!isset($post['DiseaseInfo']['FoundTime'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There is no \'DiseaseInfo => ExaminationTime\'';
        }else{
            $diseaseDetail['DiseaseDetail'] = $this->itemhold->getTheDetail($post);
        }
    	return $diseaseDetail;
    }
}
