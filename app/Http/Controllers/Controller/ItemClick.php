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
 * @var itemClick itemclick 实例化ItemClick类
 *
 * @return array TheDiseaseInfo 存放查询到的结果
 *
 * 此接口接受来自外部post请求并接受json数据，将自动整理条件等向数据库请求相关隧道的所有基本数据并返回
 */
class ItemClick extends Controller
{
    //
    public function __construct(){
    	$this->itemclick = new \App\model\itemClick();
    	date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param Request $request 用于接受post数据
     *
     * @var array   post 存放外部请求的数据
     *              error 存放错误信息
     *              database 存放需要查询的数据库名
     *              detail 存放查询到的病害详情
     * @var stdClass theDiseaseInfo 存放来自model的查询结果
     *
     * @return array $TheDiseaseInfo 返回整理好的格式化数据
     *
     * 接受请求数据，自动解析请求数据格式，返回符合条件的数据
     *
     * @author 陈科杰 15520446187
     * @version 1.1.1.20170506
     */
    public function returnDiseaseInfo(Request $request){
        $post = $request->json()->all();
        //如果接受的数据不存在['TunnelInfo']['TunnelId']字段，返回错误信息
        if (!isset($post['TunnelInfo']['TunnelId'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'TunnelId\' in POST';
            return $error;
        }
    	$database = $post['TunnelInfo']['TunnelId'];

    	$theDiseaseInfo = $this->diseaseInfo($database, $this->itemclick->getTheStartMileage($database));
        /**
         * @var array diseaseNum 表示当前遍历的下标
         *            disease 表示当前遍历到的病害值
         *
         * 遍历得到的病害信息数组，按病害ID查询详情
         */
    	foreach ($theDiseaseInfo as $diseaseNum => $disease) {
            //将病害类型代码转换成字符型，用于确定查询表
    		switch ($disease->DiseaseType) {
    			case '0':					//裂缝cracks
    				$type = 'crack_disease';
    				break;
    			case '1':					//漏洞leaks
    				$type = 'leak_disease';
    				break;
    			case '2':					//掉块drop
    				$type = 'drop_disease';
    				break;	
    			case '3':					//划痕scratch
    				$type = 'scratch_disease';
                    break;
    			default:					//异常exception
    				$type = 'exception_disease';
    				break;
    		}
    		$detail = $this->getDiseaseDetail($database, $type, $disease->FoundTime, $disease->DiseaseID, $this->itemclick->getTheStartMileage($database));
            $detail[0]->DiseasePosition['Mileage'] = $theDiseaseInfo[$diseaseNum]->Mileage;
            $detail[0]->DiseasePosition['Position'] = $theDiseaseInfo[$diseaseNum]->Position;
            unset($theDiseaseInfo[$diseaseNum]->Mileage);
            unset($theDiseaseInfo[$diseaseNum]->Position);
            $theDiseaseInfo[$diseaseNum]->PNGURL = $detail[0]->PNGFile;
            unset($detail[0]->PNGFile);
    		$theDiseaseInfo[$diseaseNum]->Info = $detail[0];
    	}
        $theDiseaseInfo['StartMileage'] = $this->itemclick->getTheStartMileage($database)->DiseaseID;
    	$TheDiseaseInfo['DiseasesInfo'] = $theDiseaseInfo;
        return $TheDiseaseInfo;
    }

    /**
     * @param array start 存放病害位置的起始里程
     *
     * @param string database 存放需要查询的所有数据
     *
     * @return array diseaseInfo 返回符合要求的病害信息
     *
     * 用于得到规定里程类的病害信息
     */
    public function diseaseInfo($database, $start){
    	$diseaseInfo = $this->itemclick->getDiseaseInfo($database, $start);
    	return $diseaseInfo;
    }

    /**
     * @param array type 存放需要查询的病害类型
     *              time 存放需要查询的时间
     *              diseaseId 存放需要查询的病害ID
     *
     * @param string database 存放需要查询的所有数据
     *
     * @return array detail 返回查询到的详细内容
     */
    public function getDiseaseDetail($database, $type, $time, $diseaseId){
    	$detail = $this->itemclick->getDiseaseDetail($database, $type, $time, $diseaseId);
    	unset($detail[0]->DiseaseID);
    	return $detail;
    }
}
