<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchDirTest extends Controller
{
    //
    public function test()
    {
    	$dir = "./";
    	$file = scandir($dir);
    	return $file;
    }
}
