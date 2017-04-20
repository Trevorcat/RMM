<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class loginOnLoad extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getIsChecked($database, $post){
    	$where['OpenId'] = $post['UserInfo']['openId'];
    	$isCheckeds = $this->theDatas->getDataByTablenameAndDatabasename('', 'authority', $where, '');
    	$returnCheck = 0;
    	foreach ($isCheckeds as $tunnel => $check) {
    		if ($database == $check->TunnelId) {
    			$returnCheck = $check->IsChecked;
    		}
    	}
    	return $returnCheck;
    }

    public function getEvents($database){
    	$events = $this->theDatas->getDataByTablenameAndDatabasename($database, 'tunnel_info', '', '');
    	return $events;
    }

    public function getTunnelName($TunnelID){
        $where['TunnelId'] = $TunnelID;
        $name = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelName;
        return $name;
    }
}
