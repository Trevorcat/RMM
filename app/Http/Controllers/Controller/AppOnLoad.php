<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppOnLoad extends Controller
{
    //
    public function __construct(){
    	$this->appOnLoad = new \App\model\appOnLoad();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function getAuthority(Request $request){
    	$authority['Authority'] = $this->appOnLoad->getAuthority($request);
    	return $authority;
    }

}
