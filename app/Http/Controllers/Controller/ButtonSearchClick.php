<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ButtonSearchClick extends Controller
{
    //
     public function __construct(){
    	$this->buttonSearchClick = new \App\model\buttonSearchClick();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function returnDiseases(Request $request){
    	$post = $request->json()->all();
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There\'s no \'TunnelInfo => TunnelID\' in POST';
        }else if(!isset($post['StartMileage'])){
            $error['error'] = 1;
            return $error['reason'] = 'There\'s no \'StartMileage\' in POST';
        }else if (!isset($post['EndMileage'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There\'s no \'EndMileage\' in POST';
        }else if (!isset($post['Filter'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There\'s no \'Filter\' in POST';
        }else{
            $theDisease['DiseasesInfo'] = $this->buttonSearchClick->getTheDisease($post);
            // var_dump($theDisease);
            return $theDisease;
        }
    }
}
