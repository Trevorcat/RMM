<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;

/**
 * 此为AppOnLoad controller 的model
 * @author 陈科杰 15520446187
 * @version 1.1.1.20170506
 */

class appOnLoad extends Model
{
    //
    public function __construct(){
        $this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param Request request 接受来自controller的请求类
     * 
     * @var array post 接受request中的json
     *            error 存放错误信息
     *            where 存放查询条件
     *            data 存放来自数据库请求到的数据
     *
     * @return    return 存放格式化的数据用于返回
     */
    public function getAuthority($request){
        $post = $request->json()->all();
        //如果接受的内容中不存在userInfo=>openId则返回错误信息
        if (!isset($post['UserInfo']['openId'])) {
            return $error['error'] = 'There\'s no \'UserInfo => openId\' in POST';
        }
        $where['OpenId'] = $post['UserInfo']['openId'];
        //根据变量where变量向数据库请求数据
        $data = $this->theDatas->getDataByTablenameAndDatabasename('', 'authority', $where,'');
        //如果数据库中不存在此openId
        if (count($data) == 0) {
            $return['IsTourist'] = 1;
            unset($where);
            $where['OpenId'] = '000000';
            $data = $this->theDatas->getDataByTablenameAndDatabasename('', 'authority', $where,'');
            //遍历得到的数据向返回变量压入数据
            foreach ($data as $key => $value) {
                $return['TunnelID'][$key] = $value->TunnelId;
            }
            return $return;
        }else{
            $return['IsTourist'] = 0;
            //遍历得到的数据向返回变量压入数据
            foreach ($data as $key => $value) {
                $return['TunnelID'][$key] = $value->TunnelId;
            }
            return $return;
        }
    }
}