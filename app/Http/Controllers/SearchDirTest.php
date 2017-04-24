<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchDirTest extends Controller
{
    //
    public function test()
    {
    	$dir = "/usr/share/nginx/html/RMM/public/";
    	$file = scandir($dir);
    	return $file;
    }
}
