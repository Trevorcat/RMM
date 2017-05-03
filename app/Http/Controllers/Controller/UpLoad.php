<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZipArchive;

class UpLoad extends Controller
{
    //
    private $up;

    public $path;

    public function __construct(){
    	$this->up = new \App\model\upload();

    	$this->path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

    	$this->up->set('path', $this->path.'/public/uploads');
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
    		$zip = new ZipArchive();
    		var_dump($zip);
       		if ($zip->open('uploads/'.$this->up->getFileName()[0], ZIPARCHIVE::CREATE)) {
       			var_dump($zip);
    			$zip->extractTo($this->path.'/public/unzip');
    			$zip->close();
    		}else{
    			return 'false';
    		}
    	}else{
    		return 'false';
    	}
    }
}
