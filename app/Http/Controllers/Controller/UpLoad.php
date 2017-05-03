<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpLoad extends Controller
{
    //
    private $up;
    public function __construct(){
    	$this->up = new \App\model\upload();

    	$this->up->set("path", "/public/upload");
    	$this->up->set("maxsize", 350000);
    	$this->up->set("allowtype", array("gif", "png", "jpg","jpeg","zip"));
    	$this->up->set("israndname", false);
    }

    public function upload(Request $request){

    	$zipFile = $request->all();
    	if ($zipFile == '') {
    		$error['reason'] = '上传失败';
    		return $error['code'] = 1;
    	}
    	var_dump($zipFile);
    	$success = $this->up->upload($zipFile);
       	if ($success) {
    		$zip = new ZipArchive;
    		if ($zip->open($this->up->getFileName() . '.zip')) {
    			$zip->extractTo('/public/unzip');
    			$zip->close();
    		}else{
    			return 'false';
    		}
    	}else{
    		return 'false';
    	}
    }
}
