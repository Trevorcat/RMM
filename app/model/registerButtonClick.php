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
 * RegisterButtonClick controller对应的model
 */
class registerButtonClick extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param Request request 接受来自controller的过滤后数据
     *
     * @var array where 存放查询条件
     *            post 存放需要使用的数据
     *            
     * @return data 存放查询得到的数据
     *
     * 请求数据库获取事件被检测的状态
     */
    public function getCompanyName($request){
        $post = $request->all();
    	$where['OpenId'] = $post['UserInfo']['openId'];
    	$data = $this->theDatas->getDataByTablenameAndDatabasename('', 'user_info', $where, '');
        //是否存在值，不存在则返回0
        if (count($data) == 0) {
            return 0;
        }else{
            return $data[0]->CompanyName;
        }
    }

    /**
     * @param Request request 接受来自controller的过滤后数据
     *
     * @var array where 存放查询条件
     *            post 存放需要使用的数据
     *            data 存放数据库中查询到的结果
     *            tunnels 存放权限内的所有隧道
     *
     * @var string insertUserInfoSql 插入sql语句变量
     *             deleteInviteCodeInfoSql 删除sql语句变量
     *
     * @var int insertUserInfoSuccess 是否插入成功
     *          deleteInviteCodeInfoSql 是否删除成功
     *            
     * @return confirm 存放查询得到的认证公司名
     *
     * 请求数据库获取事件被检测的状态
     */
    public function confirmInviteCode($request){
        $post = $request->all();
    	$where['InviteCode'] = $post['InviteCode'];
    	$data = $this->theDatas->getDataByTablenameAndDatabasename('', 'invite_code_info', $where, '');
        //是否存在值，不存在则返回0
    	if (count($data) == 0) {
    		return $confirm = 0;
    	}else{
            $insertUserInfoSql = "INSERT INTO `RMM`.`user_info` (`OpenId`, `CompanyName`, `LogPath`) VALUES ('".$post['UserInfo']['openId']."', '".$data[0]->CompanyName."', '');";
            $insertUserInfoSuccess = $this->theDatas->sql('RMM',$insertUserInfoSql);
            //插入是否成功，否则返回0
            if ($insertUserInfoSuccess == 0) {
                return 0;
            }
            $deleteInviteCodeInfoSql = "DELETE FROM `RMM`.`invite_code_info` WHERE InviteCode = '".$post['InviteCode']."'";
            $deleteSuccess = $this->theDatas->sql('RMM',$deleteInviteCodeInfoSql);
            //删除是否成功，否则返回0
            if ($deleteSuccess == 0 ) {
                return 0;
            }
            $tunnels = $this->theDatas->getDataByTablenameAndDatabasename('', 'invite_code', $where, '');

            /**
             * @var int key 当前遍历下标
             * 
             * @var array value 当前遍历隧道内容
             *
             * @var mix insertSql 插入sql语句、是否成功
             *
             * 遍历tunnels 向数据库写入此用户对应权限
             */
            foreach ($tunnels as $key => $value) {
                $insertSql = "INSERT INTO `RMM`.`authority` (`OpenId`, `TunnelId`, `IsChecked`) VALUES ('".$post['UserInfo']['openId']."', '".$value->TunnelId."', '0');";
                $insertSql = $this->theDatas->sql('RMM',$insertSql);
                //若插入失败，返回0
                if ($insertSql == 0) {
                    return 0;
                }
            }
            
    		$confirm = $data[0]->CompanyName;
            //若['InviteCode']为1231654，则为测试数据，使用过后立刻重新插入数据库
            if ($post['InviteCode'] == "1231654") {
                $recoverSql = "INSERT INTO `RMM`.`invite_code_info` (`InviteCode`, `CompanyName`) VALUES ('1231654', '四川隧唐科技股份有限公司');";
                $this->theDatas->sql('RMM', $recoverSql);
            }
    		return $confirm;
    	}
    }

    /**
     * @param Request request 接受来自controller的过滤后数据
     *
     * @var array where 存放查询条件
     *            post 存放需要使用的数据
     * @var stdClass data 存放查询到的权限
     *            
     * @return authority 存放查询得到的数据
     *
     * 请求数据库获取事件被检测的状态
     */
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
