<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ButtonOkClick extends Controller
{
    //
    public function __construct(){
    	$this->buttonOkClick = new \App\model\buttonOkClick();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function returnDiseases(Request $request){
    	$post = $request->json()->all();
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            return $error['error'] = 'There\'s no \'TunnelInfo => TunnelID\' in POST';
        }else if(!isset($post['StartMileage'])){
            return $error['error'] = 'There\'s no \'StartMileage\' in POST';
        }else if (!isset($post['EndMileage'])) {
            return $error['error'] = 'There\'s no \'EndMileage\' in POST';
        }else if (!isset($post['Filter'])) {
            return $error['error'] = 'There\'s no \'Filter\' in POST';
        }else{
            $theDisease['DiseasesInfo'] = $this->buttonOkClick->getTheDisease($post);
            // var_dump($theDisease);
            return $theDisease;
        }
    }
}
