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
            $insertUserInfoSql = "INSERT INTO `RMM`.`user_info` (`OpenId`, `CompanyName`, `LogPath`) VALUES ('".$post['UserInfo']['openId']."', '".$data[0]->CompanyName."', '');";
            $insertUserInfoSuccess = $this->theDatas->sql('RMM',$insertUserInfoSql);
            $deleteInviteCodeInfoSql = "DELETE FROM `RMM`.`invite_code_info` WHERE InviteCode = '".$post['InviteCode']."'";
            $deleteSuccess = $this->theDatas->sql('RMM',$deleteInviteCodeInfoSql);
            if ($deleteSuccess == 0 || $insertUserInfoSuccess == 0) {
                return 0;
            }
            $tunnels = $this->theDatas->getDataByTablenameAndDatabasename('', 'invite_code', $where, '');
            foreach ($tunnels as $key => $value) {
                $insertSql = "INSERT INTO `RMM`.`authority` (`OpenId`, `TunnelId`, `IsChecked`) VALUES ('".$post['UserInfo']['openId']."', '".$value->TunnelId."', '0');";
                $insertSql = $this->theDatas->sql('RMM',$insertSql);
                if ($insertSql == 0) {
                    return 0;
                }
            }
            
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
