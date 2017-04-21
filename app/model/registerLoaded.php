<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class registerLoaded extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function tunnelInfo($post){
        $postData['OpenId'] = $post['UserInfo']['openId'];
        $tunnelInfo = $post['Authority']['TunnelID'];
        var_dump($tunnelInfo);
        foreach ($tunnelInfo as $key => $value) {
            $tunnel[$key] = $value;
        }
        $info = NULL;
        foreach ($tunnel as $key => $value) {
            $time = 0;
            if ($info == NULL) {                
                for ($count = 0; $count < count($tunnel); $count++) {
                    $nums = count($this->theDatas->getDataByTablenameAndDatabasename($value, 'tunnel_info', '', ''));
                    if ($count == 0) {
                        for ($infoTime = 0; $infoTime < $nums; $infoTime++) { 
                            $info[$time] = $this->theDatas->getDataByTablenameAndDatabasename($value, 'tunnel_info', '', '')[$infoTime];
                            $where['TunnelId'] = $value;
                            $info[$time]->TunnelName = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelName;
                            $time++;
                        }
                    }
                }
            }
            else{
                $xxx = 0;
                $countOfInfo = count($info);
                for ($infoTime = $countOfInfo; $infoTime < $nums + $countOfInfo; $infoTime++) {
                    $info[$infoTime] = $this->theDatas->getDataByTablenameAndDatabasename($value, 'tunnel_info', '', '')[$xxx];
                    $xxx ++;
                    $where['TunnelId'] = $value;
                    $info[$infoTime]->TunnelName = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelName;
                }
            }
        }
        foreach ($info as $infoKey => $infoValue) {
                for ($type = 0; $type < 5; $type++) { 
                    switch ($type) {
                        case '0':
                            $countName = 'CracksStatics';
                            break;
                        case '1':
                            $countName = 'LeaksStatics';
                            break;
                        case '2':
                            $countName = 'DropStatics';
                            break;
                        case '3':
                            $countName = 'ScratchStatics';
                            break;
                        default:
                            $countName = 'ExceptionStatics';
                            break;
                    }
                    if ($type != 4) {
                        unset($where);
                        switch ($type) {
                            case '0':
                                $diseaseName = 'crack_disease';
                                break;
                            case '1':
                                $diseaseName = 'leak_disease';
                                break;
                            case '2':
                                $diseaseName = 'drop_disease';
                                break;
                            default:
                                $diseaseName = 'scratch_disease';
                                break;
                        }
                        $DiseaseIDs = $this->theDatas->getDataByTablenameAndDatabasename($tunnel[$infoKey], 'disease', 'FoundTime = ' . '\'' . $infoValue->ExaminationTime . '\'', '');
                        for($theSeverity = 0; $theSeverity <= 4; $theSeverity++){
                            $where['SeverityClassfication'] = $theSeverity;
                            $where['DiseaseID'] = $DiseaseIDs;                            $theCount['CountOfLevel' . $theSeverity] = $this->theDatas->countTheDetails( $tunnel[$infoKey], $diseaseName, $this->theDatas->countLevelWhere($where), $infoValue->ExaminationTime)[0]->count;
                        }
                    }else{
                        unset($theCount);
                    }
                    unset($where);
                    $where['FoundTime'] = $infoValue->ExaminationTime;
                    $where['DiseaseType'] = $type;
                    $where['Position'] = 0;         //CountOfSideLeft
                    $theCount['CountOfSideLeft'] = $this->theDatas->countTheDetails($tunnel[$infoKey], 'disease', $where)[0]->count;
                    $where['Position'] = 4;         //CountOfSideRight
                    $theCount['CountOfSideRight'] = $this->theDatas->countTheDetails($tunnel[$infoKey], 'disease', $where)[0]->count;
                    $where['Position'] = 2;         //CountOfTop
                    $theCount['CountOfTop'] = $this->theDatas->countTheDetails($tunnel[$infoKey], 'disease', $where)[0]->count;
                    $where['Position'] = 3;         //CountOfHanceRight
                    $theCount['CountOfHanceRight'] = $this->theDatas->countTheDetails($tunnel[$infoKey], 'disease', $where)[0]->count;
                    $where['Position'] = 1;         //CountOfHanceLeft
                    $theCount['CountOfHanceLeft'] = $this->theDatas->countTheDetails($tunnel[$infoKey], 'disease', $where)[0]->count;
                    $infoValue->$countName = $theCount;
                }
                
            }
            return $info;
    }
    
}
