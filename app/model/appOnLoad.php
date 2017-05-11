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
        var_dump($where,$data);
        if (count($data) == 0) {
            $return['IsTourist'] = 1;
            unset($where);
            $where['OpenId'] = '000000';
            $data = $this->theDatas->getDataByTablenameAndDatabasename('', 'authority', $where,'');
            foreach ($data as $key => $value) {
                $return['TunnelID'][$key] = $value->TunnelId;
            }
            return $return;
        }else{
            $return['IsTourist'] = 0;
            foreach ($data as $key => $value) {
                $return['TunnelID'][$key] = $value->TunnelId;
            }
            return $return;
        }
    }
}