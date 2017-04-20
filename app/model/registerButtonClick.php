<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class registerButtonClick extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getCompanyName($request){
        $post = $request->all();
    	$where['OpenId'] = $post['UserInfo']['openId'];
    	$data = $this->theDatas->getDataByTablenameAndDatabasename('', 'user_info', $where, '');
        if (count($data) == 0) {
            return 0;
        }else{
            return $data[0]->CompanyName;
        }
    }

    public function confirmInviteCode($request){
        $post = $request->all();
    	$where['InviteCode'] = $post['InviteCode'];
    	$data = $this->theDatas->getDataByTablenameAndDatabasename('', 'invite_code_info', $where, '');
    	if (count($data) == 0) {
    		return $confirm = 0;
    	}else{
    		$confirm = $data[0]->CompanyName;
    		return $confirm;
    	}
    }

    public function getAuthority($request){
        $post = $request->all();
    	$where['InviteCode'] = $post['InviteCode'];
    	$data = $this->theDatas->getDataByTablenameAndDatabasename('', 'invite_code', $where, '');
    	$authority = NULL;
    	foreach ($data as $key => $value) {
    		$authority[$key] = $value->TunnelId;
    	}
    	return $authority;
    }
}
