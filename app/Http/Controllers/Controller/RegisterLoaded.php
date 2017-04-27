<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegisterLoaded extends Controller
{
    //
    public function __construct(){
    	$this->registerLoaded = new \App\model\registerLoaded();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function tunnelInfo(Request $request){
        $post = $request->json()->all();

        if (!isset($post['UserInfo']['openId'])) {
            $error['error'] = 1;
        	return $error['reason'] = 'There is no \'UserInfo => openId\'';
        }else if (!isset($post['Authority']['TunnelID'])) {
            $error['error'] = 1;
        	return $error['reason'] = 'There is no \'Authority => TunnelID\'';
        }else{
        	$tunnelInfo['TunnelsInfo'] = $this->registerLoaded->tunnelInfo($post);
        	return $tunnelInfo;
        }
    }
}
