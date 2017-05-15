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
 * @var itemHold itemhold 实例化itemHold类
 *
 * 此接口接受来自外部post请求并接受json数据，将自动整理条件等向数据库请求当前隧道所有病害数据并返回
 */
class ItemHold extends Controller
{
    //
	public function __construct(){
    	$this->itemhold = new \App\model\itemHold();
    	date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param Request $request 用于接受post数据
     *
     * @var array   post 存放外部请求的数据
     *              error 存放错误信息
     *
     * @return array $diseaseDetail 返回整理好的格式化数据
     *
     * 接受请求数据，自动解析请求数据格式，返回符合条件的数据
     *
     */
    public function returnDiseaseDetail(Request $request){
    	$post = $request->json()->all();
        //判断post是否存在['TunnelInfo']['TunnelId']，若不存在返回错误信息
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'TunnelInfo => TunnelId\'';
            return $error;
        }
        //判断post是否存在['DiseaseInfo']['FoundTime']，若不存在返回错误信息
        else if (!isset($post['DiseaseInfo']['FoundTime'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'DiseaseInfo => FoundTime\'';
            return $error;
        }else{
            $diseaseDetail['DiseaseDetail'] = $this->itemhold->getTheDetail($post);
        }
    	return $diseaseDetail;
    }
}
