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
class ScanSlided extends Controller
{
    //

    public function __construct(){
    	$this->scanslided = new \App\model\scanSlided();
    	date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param Request $request 用于接受post数据
     *
     * @var array   post 存放外部请求的数据
     *              error 存放错误信息
     *
     * @return array theDiseases 返回整理好的格式化数据
     *
     * 接受请求数据，自动解析请求数据格式，返回符合条件的数据
     *
     * @author 陈科杰 15520446187
     * @version 1.1.1.20170506
     */
    public function returnDisease(Request $request){
    	$post = $request->json()->all();
        //若post变量不存在['TunnelInfo']['TunnelId']字段，返回错误信息
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'TunnelInfo => TunnelId\'';
            return $error;
        }
        //若post变量不存在['Mileage']字段，返回错误信息
        else if (!isset($post['Mileage'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'Mileage\'';
            return $error;
        }
        //若post变量不存在['TunnelInfo']['ExaminationTime']字段，返回错误信息
        else if (!isset($post['TunnelInfo']['ExaminationTime'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'TunnelInfo => ExaminationTime\'';
            return $error;
        }
        //若post变量不存在['Filter']字段，返回错误信息
        else if (!isset($post['Filter'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'Filter\'';
            return $error;
        }else{
            $theDiseases = $this->scanslided->searchTheDisease($post);
            return $theDiseases;
        }
        
    }
}
