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
        foreach ($tunnelInfo as $key => $value) {
            $tunnel[$key] = $value;
        }
        $info = NULL;
        $time = 0;
        foreach ($tunnel as $key => $value) {
            if ($info == NULL) {
                for ($count = 0; $count < count($tunnel); $count++) {
                    $nums = count($this->theDatas->getDataByTablenameAndDatabasename($value, 'tunnel_info', '', ''));
                    if ($count == 0) {
                        for ($infoTime = 0; $infoTime < $nums; $infoTime++) { 
                            $info[$time] = $this->theDatas->getDataByTablenameAndDatabasename($value, 'tunnel_info', '', '')[$infoTime];
                            $where['TunnelId'] = $value;
                            $info[$time]->TunnelName = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelName;
                            $info[$infoTime]->TunnelID = $value;
                            $info[$infoTime]->TunnelDescription = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelDescription;
                            $time++;
                        }
                    }
                }
            }
            else{
                $xxx = 0;
                $countOfInfo = count($info);
                $nums = count($this->theDatas->getDataByTablenameAndDatabasename($value, 'tunnel_info', '', ''));
                for ($infoTime = $countOfInfo; $infoTime < $nums + $countOfInfo; $infoTime++) {
                    $info[$infoTime] = $this->theDatas->getDataByTablenameAndDatabasename($value, 'tunnel_info', '', '')[$xxx];
                    $xxx ++;
                    $where['TunnelId'] = $value;
                    $info[$infoTime]->TunnelName = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelName;
                    $info[$infoTime]->TunnelID = $value;
                    $info[$infoTime]->TunnelDescription = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0]->TunnelDescription;
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
                    $theCount['CountOfSideLeft'] = 0;
                    $theCount['CountOfSideRight'] = 0;
                    $theCount['CountOfTop'] = 0;
                    $theCount['CountOfHanceRight'] = 0;
                    $theCount['CountOfHanceLeft'] = 0;
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
                        $DiseaseIDs = $this->theDatas->getDataByTablenameAndDatabasename($infoValue->TunnelID , 'disease', 'FoundTime = ' . '\'' . $infoValue->ExaminationTime . '\'', '');                        $theSeverity = 0;
                        for(; $theSeverity <= 4; $theSeverity++){
                            $where['SeverityClassfication'] = $theSeverity;
                            $where['DiseaseID'] = $DiseaseIDs;              
                            if (empty($where['DiseaseID'])) {
                                if (isset($theCount['CountOfLevel' . $theSeverity])) {
                                    continue;
                                }
                                $theCount['CountOfLevel' . $theSeverity] = 0;
                                continue;
                            }
                            if (isset($theCount['CountOfLevel' . $theSeverity])) {
                                $theCount['CountOfLevel' . $theSeverity] += $this->theDatas->countTheDetails( $infoValue->TunnelID, $diseaseName, $this->theDatas->countLevelWhere($where), $infoValue->ExaminationTime)[0]->count;
                            }else{
                                $theCount['CountOfLevel' . $theSeverity] = $this->theDatas->countTheDetails( $infoValue->TunnelID, $diseaseName, $this->theDatas->countLevelWhere($where), $infoValue->ExaminationTime)[0]->count;
                            }
                        }
                    }
                    unset($where);
                    $where['FoundTime'] = $infoValue->ExaminationTime;
                    $where['DiseaseType'] = $type;
                    $where['Position'] = 0;         //CountOfSideLeft
                    $theCount['CountOfSideLeft'] += $this->theDatas->countTheDetails($infoValue->TunnelID, 'disease', $where)[0]->count;
                    $where['Position'] = 4;         //CountOfSideRight
                    $theCount['CountOfSideRight'] += $this->theDatas->countTheDetails($infoValue->TunnelID, 'disease', $where)[0]->count;
                    $where['Position'] = 2;         //CountOfTop
                    $theCount['CountOfTop'] += $this->theDatas->countTheDetails($infoValue->TunnelID, 'disease', $where)[0]->count;
                    $where['Position'] = 3;         //CountOfHanceRight
                    $theCount['CountOfHanceRight'] += $this->theDatas->countTheDetails($infoValue->TunnelID, 'disease', $where)[0]->count;
                    $where['Position'] = 1;         //CountOfHanceLeft
                    $theCount['CountOfHanceLeft'] += $this->theDatas->countTheDetails($infoValue->TunnelID, 'disease', $where)[0]->count;
                    $infoValue->$countName = $theCount;
                    unset($theCount);
                }
            }
            return $info;
    }
    
}
