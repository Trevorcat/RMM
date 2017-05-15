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
 * @var registerLoaded registerLoaded 实例化registerLoaded 类
 *
 * 此接口接受来自外部post请求并接受json数据，将自动整理条件等向数据库请求数据并返回
 */ 
class RegisterLoaded extends Controller
{
    //
    public function __construct(){
    	$this->registerLoaded = new \App\model\registerLoaded();
    	date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param Request $request 用于接受post数据
     *
     * @var array   post 存放外部请求的数据
     *              error 存放错误信息
     *
     * @return array tunnelInfo 返回整理好的格式化数据
     *
     * 接受请求数据，自动解析请求数据格式，返回符合条件的数据
     *
     * @author 陈科杰 15520446187
     * @version 1.1.1.20170506
     */
    public function tunnelInfo(Request $request){
        $post = $request->json()->all();

        //若post变量不存在['UserInfo']['openId']字段，返回错误信息
        if (!isset($post['UserInfo']['openId'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'UserInfo => openId\'';
        	return $error;
        }
        //若post变量不存在['Authority']['TunnelID']字段，返回错误信息
        else if (!isset($post['Authority']['TunnelID'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'Authority => TunnelID\'';
        	return $error;
        }else{
        	$tunnelInfo['TunnelsInfo'] = $this->registerLoaded->tunnelInfo($post);
        	return $tunnelInfo;
        }
    }
}
