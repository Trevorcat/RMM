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
        

        foreach ($tunnelInfo as $tunnelNum => $tunnelId) {  //遍历所有的隧道

            $where['TunnelID'] = $tunnelId;
            $tunnels = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0];
            $tunnelDetail = $this->theDatas->getDataByTablenameAndDatabasename($tunnelId, 'tunnel_info', '', '');
            foreach ($tunnelDetail as $examinationTime => $details) {   //遍历当前隧道里的查询记录

                for ($type=0; $type <= 4 ; $type++) { 
                    switch ($type) {
                        case 0:
                            //完成病害等级统计
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $CracksStatics['CountOfLevel'.$level] = !isset($CracksStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'crack_disease', $whereIn, $details->ExaminationTime)[0]->count : $CracksStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'crack_disease', $whereIn, $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                switch ($Position) {
                                    case 0:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfSideLeft'] = !isset($CracksStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfHanceLeft'] = !isset($CracksStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfTop'] = !isset($CracksStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfHanceRight'] = !isset($CracksStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfSideRight'] = !isset($CracksStatics['CountOfSideRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfSideRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                }
                            }
                            unset($whereIn['Position']);
                            $CracksStatics['CountOfAll'] = $this->theDatas->countTheDetails($tunnelId, 'disease', $whereIn, '')[0]->count ;
                            unset($whereIn);
                            $tunnels->CracksStatic[$examinationTime] = $CracksStatics;
                            break;
                        
                        case 1:
                            //完成病害等级统计
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $LeaksStatics['CountOfLevel'.$level] = !isset($LeaksStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'leak_disease', $whereIn, $details->ExaminationTime)[0]->count : $LeaksStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'leak_disease', $whereIn, $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                switch ($Position) {
                                    case 0:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfSideLeft'] = !isset($LeaksStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfHanceLeft'] = !isset($LeaksStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfTop'] = !isset($LeaksStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfHanceRight'] = !isset($LeaksStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfSideRight'] = !isset($LeaksStatics['CountOfSideRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfSideRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                }
                            }
                            unset($whereIn['Position']);
                            $LeaksStatics['CountOfAll'] = $this->theDatas->countTheDetails($tunnelId, 'disease', $whereIn, '')[0]->count ;
                            unset($whereIn);
                            $tunnels->LeaksStatic[$examinationTime] = $LeaksStatics;
                            break;

                        case 2:
                            //完成病害等级统计
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $DropsStatics['CountOfLevel'.$level] = !isset($DropsStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'drop_disease', $whereIn, $details->ExaminationTime)[0]->count : $DropsStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'drop_disease', $whereIn, $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                switch ($Position) {
                                    case 0:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfSideLeft'] = !isset($DropsStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfHanceLeft'] = !isset($DropsStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfTop'] = !isset($DropsStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfHanceRight'] = !isset($DropsStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfSideRight'] = !isset($DropsStatics['CountOfSideRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfSideRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                }
                            }
                            unset($whereIn['Position']);
                            $DropsStatics['CountOfAll'] = $this->theDatas->countTheDetails($tunnelId, 'disease', $whereIn, '')[0]->count ;
                            unset($whereIn);
                            $tunnels->DropsStatic[$examinationTime] = $DropsStatics;
                            break;

                        case 3:
                            //完成病害等级统计
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $ScratchStatics['CountOfLevel'.$level] = !isset($ScratchStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'scratch_disease',$whereIn , $details->ExaminationTime)[0]->count : $ScratchStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'scratch_disease',$whereIn , $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                switch ($Position) {
                                    case 0:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfSideLeft'] = !isset($ScratchStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfHanceLeft'] = !isset($ScratchStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfTop'] = !isset($ScratchStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfHanceRight'] = !isset($ScratchStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfSideRight'] = !isset($ScratchStatics['CountOfSideRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfSideRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                }
                            }
                            unset($whereIn['Position']);
                            $ScratchStatics['CountOfAll'] = $this->theDatas->countTheDetails($tunnelId, 'disease', $whereIn, '')[0]->count ;
                            unset($whereIn);
                            $tunnels->ScratchStatic[$examinationTime] = $ScratchStatics;
                            break;

                        default:
                            //完成病害位置统计
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                switch ($Position) {
                                    case 0:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfSideLeft'] = !isset($ExceptionStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfHanceLeft'] = !isset($ExceptionStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfTop'] = !isset($ExceptionStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfHanceRight'] = !isset($ExceptionStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default:
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfSideRight'] = !isset($ExceptionStatics['CountOfSideRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfSideRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                }
                            }
                            unset($whereIn['Position']);
                            $ExceptionStatics['CountOfAll'] = $this->theDatas->countTheDetails($tunnelId, 'disease', $whereIn, '')[0]->count ;
                            unset($whereIn);

                            $tunnels->ExceptionStatic[$examinationTime] = $ExceptionStatics;
                            break;
                    }
                }
                
                unset($CracksStatics);
                unset($LeaksStatics);
                unset($DropsStatics);
                unset($ScratchStatics);
                unset($ExceptionStatics);

                $tunnels->ExaminationTime[$examinationTime] = $details->ExaminationTime;
                $tunnels->severity[$examinationTime] = $details->Severity;
            }
            unset($tunnelInfo[$tunnelNum]);
            $tunnelInfo[$tunnelNum] = $tunnels;
        } 
        // var_dump($tunnelInfo);
        return $tunnelInfo;
    }
    
}
