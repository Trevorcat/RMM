<?php

/**
 * 版本号 1.1.1.20170506
 * 作者 陈科杰 
 * 联系方式 15520446187
 */
namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @param Request request 接受来自外部的请求类
 *
 * @var appOnLoad appOnload 实例化model类
 *
 * @return array authority 存放查询到的结果
 *
 * 此接口接受来自外部post请求并接受json数据，将自动整理条件等向数据库请求登录数据并返回
 */

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
