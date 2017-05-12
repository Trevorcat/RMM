<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @param Request request 接受来自外部的请求类
 * 
 * @var array post 存放外部请求的数据
 *      array error 存放错误信息
 *
 * @return array theDisease 存放查询到的结果
 *
 * 此接口接受来自外部post请求并接受json数据，将自动整理条件等向数据库请求数据并返回
 *
 * @author 陈科杰 15520446187 admin@trevorscat.com 
 * @version 1.1.1.20170506
 */
class ButtonOkClick extends Controller
{
    //
    public function __construct(){
    	$this->buttonOkClick = new \App\model\buttonOkClick();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function returnDiseases(Request $request){
    	$post = $request->json()->all();
        //在接受的请求数据中如果不存在['TunnelInfo']['TunnelId']则返回错误
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            $error['code'] = 1;
            return $error['reason'] = 'There\'s no \'TunnelInfo => TunnelID\' in POST';
        }
        //在接受的请求数据中如果不存在['StartMileage']则返回错误
        else if(!isset($post['StartMileage'])){
            $error['code'] = 1;
            return $error['reason'] = 'There\'s no \'StartMileage\' in POST';
        }
        //在接受的请求数据中如果不存在['EndMileage']则返回错误
        else if (!isset($post['EndMileage'])) {
            $error['code'] = 1;
            return $error['reason'] = 'There\'s no \'EndMileage\' in POST';
        }
        //在接受的请求数据中如果不存在['Filter']则返回错误
        else if (!isset($post['Filter'])) {
            $error['code'] = 1;
            return $error['reason'] = 'There\'s no \'Filter\' in POST';
        }else{
            $theDisease['DiseasesInfo'] = $this->buttonOkClick->getTheDisease($post);
            //如果查询结果为空，则返回错误报告
            if ($theDisease['DiseasesInfo']['DiseaseInfo'] == NULL) {
                $error['code'] = 1;
                return $error['reason'] = 'Can not search anything by the Filter';
            }
            return $theDisease;
        }
    }
}
