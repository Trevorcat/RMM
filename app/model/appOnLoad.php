<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

class appOnLoad extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getAuthority($request){
        $post = $request->json()->all();
        if (!isset($post['UserInfo']['openId'])) {
            return $error['error'] = 'There\'s no \'UserInfo => openId\' in POST';
        }
        $where['OpenId'] = $post['UserInfo']['openId'];
    	$data = $this->theDatas->getDataByTablenameAndDatabasename('', 'authority', $where,'');
    	if ($data[0]->OpenId == '000000') {
    		$return['IsTourist'] = 1;
    	}else{
    		$return['IsTourist'] = 0;
    	}

    	foreach ($data as $key => $value) {
    		$return['TunnelID'][$key] = $value->TunnelId;
    	}
    	return $return;
    }
}
