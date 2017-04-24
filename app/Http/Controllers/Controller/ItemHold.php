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
    	$diseaseDetail['DiseaseDetail'] = $this->itemhold->getTheDetail($post);
    	return $diseaseDetail;
    }
}
