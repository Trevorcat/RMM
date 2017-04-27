<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScanSlided extends Controller
{
    //

    public function __construct(){
    	$this->scanslided = new \App\model\scanSlided();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function returnDisease(Request $request){
    	$post = $request->json()->all();
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            return $error['error'] = 'There is no \'TunnelInfo => TunnelId\'';
        }else if (!isset($post['Mileage'])) {
            return $error['error'] = 'There is no \'Mileage\'';
        }else if (!isset($post['TunnelInfo']['ExaminationTime'])) {
            return $error['error'] = 'There is no \'TunnelInfo => ExaminationTime\'';
        }else{
            $theDiseases = $this->scanslided->searchTheDisease($post);
            return $theDiseases;
        }
        
    }
}
