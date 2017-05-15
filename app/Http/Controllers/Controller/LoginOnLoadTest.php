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
 * @var loginOnLoad event 实例化loginOnLoad 类
 *
 * 此接口接受来自外部post请求并接受json数据，将自动整理条件等向数据库请求登录加载数据并返回
 */
class LoginOnLoadTest extends Controller
{
    //
    public function __construct(){
    	$this->event = new \App\model\loginOnLoad();
    	date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param Request $request 用于接受post数据
     *
     * @var array   post 存放外部请求的数据
     *              error 存放错误信息
     *              databases 存放需要搜索的数据库名
     *
     * @return array $returnEventInfo 返回整理好的格式化数据
     *
     * 接受请求数据，自动解析请求数据格式，返回符合条件的数据
     */
    public function returnEventInfo(Request $request){
    	$post = $request->json()->all();
        //$post是否存在['Authority']['TunnelID']字段，如否，则返回错误信息
        if (!isset($post['Authority']['TunnelID'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'Authority => TunnelID\'';
            return $error; 
        }
        //$post是否存在['UserInfo']['openId']字段，如否，则返回错误信息
        else if (!isset($post['UserInfo']['openId'])) {
            $error['error'] = 1;
            $error['reason'] = 'There is no \'UserInfo => openId\'';
            return $error;
        }else{
            $databases = $post['Authority']['TunnelID'];
            /**
             * @var int databaseNum 储存当前遍历下标
             * @var string events 储存所有事件
             *             database 对应数据库名
             *             isChecked 是否查看过
             *             tunnelName 储存隧道名称
             *             eventInfo 储存当前事件信息
             *             returnEventInfo 储存整理后符合要求的事件信息
             *             isDely 检测是否超时
             *
             * 遍历数据库名称，获取这些数据库中的事件信息
             */
            foreach ($databases as $databaseNum => $database) {
                $events = $this->event->getEvents($database);
                $isChecked = $this->event->getIsChecked($database, $post);
                $tunnelName = $this->event->getTunnelName($database);

                /**
                 * @var int eventNum 当前遍历下标
                 * @var array event 当前事件信息
                 *
                 *  遍历已查询到的事件进行整理操作
                 */
                foreach ($events as $eventNum => $event) {
                    $event->IsChecked = $isChecked;
                    $event->DiseaseCount['CountofCrack'] = $event->CountofCrack;
                    $event->DiseaseCount['CountofLeak'] = $event->CountofLeak;
                    $event->DiseaseCount['CountofDrop'] = $event->CountofDrop;
                    $event->DiseaseCount['CountofScratch'] = $event->CountofScratch;
                    $event->DiseaseCount['CountofException'] = $event->CountofException;
                    unset($event->CountofCrack);
                    unset($event->CountofLeak);
                    unset($event->CountofDrop);
                    unset($event->CountofScratch);
                    unset($event->CountofException);
                    //当事件为已查看状态，则不予返回
                    // if ($event->IsChecked == 1) {
                    //     unset($event);
                    //     continue;
                    // }
                    $eventInfo['TunnelPicURL'] = $event->PICsFilePath;
                }
                $eventInfo['TunnelName'] = $tunnelName;
                $isDely = 0;//初始化变量

                /**
                 * @var int key 表示当前遍历下标
                 * @var array value 表示当前遍历的事件内容
                 * @var string nextTime 储存下一次预计检测时间
                 * 
                 * 通过遍历检测当前时间是否越过预定检测时间返回isDely
                 */
                foreach ($events as $key => $value) {
                    $nextTime = $key + 1 == count($events) ? strtotime(date("y-m-d")) : strtotime($events[$key + 1]->ExaminationTime);
                    //如果上一次检测时间距今超过一年则视为检测超时
                    if (ceil(strtotime($value->ExaminationTime - $nextTime)) > 365) {
                        $isDely = 1;
                    }
                    $value->IsDely = $isDely;
                }
                $eventInfo['Events'] = $events;
                $returnEventInfo['EventInfo'][$databaseNum] = $eventInfo;
            }
            return $returnEventInfo;
        }
    }

}
