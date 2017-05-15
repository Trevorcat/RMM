<?php

/**
 * 版本号 1.1.1.20170506
 * 作者 陈科杰 
 * 联系方式 15520446187
 */
namespace App\model;

use Illuminate\Database\Eloquent\Model;

/**
 * @var getTheData theDatas 与数据库交互的核心库
 *
 * LoginOnLoad controller对应的model
 */
class loginOnLoad extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param array post 接受来自controller的过滤后数据
     * @param string database 接受来自controller传入的数据库名
     *
     * @var array where 存放查询条件
     *            isCheckeds 存放查询到符合条件的数据
     *            
     * @return returnCheck 存放查询得到的数据
     *
     * 请求数据库获取事件被检测的状态
     */
    public function getIsChecked($database, $post){
    	$where['OpenId'] = $post['UserInfo']['openId'];
    	$isCheckeds = $this->theDatas->getDataByTablenameAndDatabasename('', 'authority', $where, '');
    	$returnCheck = 0;
        /**
         * @var int tunnel 储存当前遍历下标
         * @var array check 储存确认信息
         *
         * 遍历查询到的查看状态，整理没有被查看过的事件存入returnCheck变量，并自动向数据库中更新查看数据为1
         */
    	foreach ($isCheckeds as $tunnel => $check) {
            //当传入数据库名为当前事件对应的隧道时
    		if ($database == $check->TunnelId) {
    			$returnCheck = $check->IsChecked;
                //查看状态是否为1
                if ($returnCheck == 0) {
                    $where['TunnelID'] = $database;
                    $this->theDatas->updateTheData('', 'authority', $where, '');
                }
    		}
    	}
    	return $returnCheck;
    }

    /**
     * @param string database 接受来自controller传入的数据库名
     *
     * @var array where 存放查询条件
     *            events 存放查询到符合条件的事件
     *            
     * @return events 存放整理好的事件
     *
     * 获取符合要求的事件信息
     */
    public function getEvents($database){
    	$events = $this->theDatas->getDataByTablenameAndDatabasename($database, 'tunnel_info', '', '');

        /**
         * @var int key 表示此次遍历的下标
         * @var array value 表示此次遍历的事件
         *
         * 遍历events，将存储图片路径的变量由数组拆分为两个变量
         */
        foreach ($events as $key => $value) {
            $where['TunnelId'] = $database;
            $value->PICsFilePath[0] = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->PICsFilePath;
            $value->PICsFilePath[1] = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->PICsFilePath2;
        }
    	return $events;
    }

    /**
     * @param string TunnelID 接受来自controller传入的隧道ID
     *
     * @var array where 存放查询条件
     *            
     * @return name 存放符合条件的隧道名称
     *
     * 获取符合要求的事件信息
     */
    public function getTunnelName($TunnelID){
        $where['TunnelId'] = $TunnelID;
        $name = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelName;
        return $name;
    }
}
