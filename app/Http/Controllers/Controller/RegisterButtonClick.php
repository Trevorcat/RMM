<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

header("Content-Type: text/html;charset=utf-8");  
class RegisterButtonClick extends Controller
{
    //
    public function __construct(){
    	$this->register = new \App\model\registerButtonClick();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function authority(Request $request){
        $post = $request->all();
        if (!isset($post['UserInfo']['openId'])) {
            return $error['error'] = 'There is no \'UserInfo => openId\'';
        }else if (!isset($post['InviteCode'])) {
            return $error['error'] = 'There is no \'InviteCode\'';
        }
    	$company = $this->getCompanyName($request);
    	$confirm = $this->confirmInviteCode($request);
        if (!is_string($company) && !is_string($confirm)) {
            $TunnelID['Authority']['IsTourist'] = 1; 
            return $TunnelID;
        }else if ($company == $confirm) {
    		$authority = $this->getAuthority($request);
    	}else{
    		$TunnelID['Authority']['IsTourist'] = 1; 
            return $TunnelID;
    	}
    	foreach ($authority as $key => $value) {
    		$tunnelId['TunnelID'][$key] = $value; 
    	}
    	if ($post['UserInfo']['openId'] == '000000') {
    		$tunnelId['IsTourist'] = 1;
    	}else{
            $tunnelId['IsTourist'] = 0;
        }
    	$TunnelID['Authority'] = $tunnelId;
        return $TunnelID;
    }

    public function getCompanyName($request){					//返回openid对应所在公司
    	$company = $this->register->getCompanyName($request);
    	return $company;
    }

    public function confirmInviteCode($request){				//返回邀请码对应公司
    	$confirm = $this->register->confirmInviteCode($request);
    	return $confirm;
    }

    public function getAuthority($request){						//返回邀请码对应的可用权限
    	$authority = $this->register->getAuthority($request);
    	return $authority;
    }
}
