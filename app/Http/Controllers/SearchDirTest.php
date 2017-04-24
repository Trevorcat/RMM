<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;

class SearchDirTest extends Controller
{
    //
    public function test()
    {
    	$dir = "public/".iconv('UTF-8', 'GBK', 'test').".xlsx";
    	Excel::load($dir, function($reader) {
	        $data = $reader->get();
	        var_dump($data);
	        dd($data);
	    });

    	return $file;
    }
}
