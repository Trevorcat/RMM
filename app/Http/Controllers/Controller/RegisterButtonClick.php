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
 * @var registerButtonClick register 实例化registerButtonClick 类
 *
 * 此接口接受来自外部post请求并接受json数据，将自动整理条件等向数据库请求数据并返回
 */ 
class RegisterButtonClick extends Controller
{
    //
    public function __construct(){
    	$this->register = new \App\model\registerButtonClick();
    	date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param Request $request 用于接受post数据
     *
     * @var array   post 存放外部请求的数据
     *              error 存放错误信息
     *              confirm 存放是否在库中存在认证
     *              company 存放用户所属公司
     *              authority 存放存数据库中获取的权限
     *
     * @return array $TunnelID 返回整理好的格式化数据
     *
     * 接受请求数据，自动解析请求数据格式，返回符合条件的数据
     *
     * @author 陈科杰 15520446187
     * @version 1.1.1.20170506
     */
    public function authority(Request $request){
        $post = $request->json()->all();
        //若post变量不存在['UserInfo']['openId']字段，返回错误信息
        if (!isset($post['UserInfo']['openId'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There is no \'UserInfo => openId\'';
        }
        //若post变量不存在['InviteCode']字段，返回错误信息
        else if (!isset($post['InviteCode'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There is no \'InviteCode\'';
        }
        $confirm = $this->confirmInviteCode($request);
    	$company = $this->getCompanyName($request);
        //若公司名与认证都不为字符串，则返回游客身份
        if (!is_string($company) && !is_string($confirm)) {
            $TunnelID['Authority']['IsTourist'] = 1; 
            return $TunnelID;
        }
        //若公司名与认证相等，则获取权限，并返回
        else if ($company === $confirm) {
    		$authority = $this->getAuthority($request);
    	}
        else{//若以上都不满足，则返回游客身份
    		$TunnelID['Authority']['IsTourist'] = 1; 
            return $TunnelID;
    	}
        /**
         * @var int key 表示此次遍历的下标
         * @var array value 表示此次遍历的权限
         *
         * 遍历authority，将权限数组转换为隧道ID数组
         */
    	foreach ($authority as $key => $value) {
    		$tunnelId['TunnelID'][$key] = $value; 
    	}
        //若post变量['UserInfo']['openId']为000000 则为游客身份，将['IsTourist']赋值为1
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
