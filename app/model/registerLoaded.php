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
 * RegisterLoaded controller对应的model
 */
class registerLoaded extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    /**
     * @param array post 接受来自controller的过滤后数据
     *
     * @var array tunnelInfo 存放用户权限中的隧道ID
     *            
     * @return data 存放查询得到的数据
     *
     * 请求数据库获取事件被检测的状态
     */
    public function tunnelInfo($post){
        $tunnelInfo = $post['Authority']['TunnelID'];
        
        /**
         * @var int tunnelNum 表示当前遍历的下标
         * @var string tunnelId 表示当前遍历的隧道ID
         * @var array where 存放查询变量
         *            tunnels 存放查找到的隧道
         *            PICsPath 存放隧道图片路径
         *            tunnelDetail 存放隧道详细信息
         *
         * 遍历所有的隧道获取其中每一次事件信息，整合返回数据
         */
        foreach ($tunnelInfo as $tunnelNum => $tunnelId) {  //遍历所有的隧道
            $where['TunnelID'] = $tunnelId;
            $tunnels = $this->theDatas->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '')[0];
            $PICsPath[0] = $tunnels->PICsFilePath;
            $PICsPath[1] = $tunnels->PICsFilePath2;
            unset($tunnels->PICsFilePath);
            unset($tunnels->PICsFilePath2);
            $tunnels->PICsFilePath = $PICsPath;
            $tunnelDetail = $this->theDatas->getDataByTablenameAndDatabasename($tunnelId, 'tunnel_info', '', '');

            /**
             * @var string examinationTime 存放当前遍历的检测时间
             * @var array details 存放当前遍历隧道的信息
             *
             * 获取每条隧道中的事件信息，整理所需数据
             */
            foreach ($tunnelDetail as $examinationTime => $details) {   //遍历当前隧道里的事件记录
                /**
                 * @var int type 病害类型编号
                 *      array whereIn 存放查询条件
                 *            ‘病害类型’+Statics 不同病害的统计信息
                 *
                 * 循环5中不同类型病害的编号，以向数据库中查询相关信息，存入返回变量
                 */
                for ($type=0; $type <= 4 ; $type++) {
                    /*
                        根据不同的病害类型编码进行相关的查询以及整理数据
                     */
                    switch ($type) {
                        case 0://裂缝病害
                            //完成病害等级统计
                            /**
                             * @var int level 病害等级 0-4级
                             * 
                             * 循环查询相关等级的病害
                             */
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $CracksStatics['CountOfLevel'.$level] = !isset($CracksStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'crack_disease', $whereIn, $details->ExaminationTime)[0]->count : $CracksStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'crack_disease', $whereIn, $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            /**
                             * @var int Position 存放病害在相关位置的具体方位
                             *
                             * 循环查询相关位置的病害
                             */
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                /*
                                    根据不同的位置进行查询
                                 */
                                switch ($Position) {
                                    case 0://左边墙
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfSideLeft'] = !isset($CracksStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1://左拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfHanceLeft'] = !isset($CracksStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2://顶部
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfTop'] = !isset($CracksStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3://右拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $CracksStatics['CountOfHanceRight'] = !isset($CracksStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $CracksStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default://右边墙
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
                        
                        case 1://漏水病害
                            //完成病害等级统计
                            /**
                             * @var int level 病害等级 0-4级
                             * 
                             * 循环查询相关等级的病害
                             */
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $LeaksStatics['CountOfLevel'.$level] = !isset($LeaksStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'leak_disease', $whereIn, $details->ExaminationTime)[0]->count : $LeaksStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'leak_disease', $whereIn, $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            /**
                             * @var int Position 存放病害在相关位置的具体方位
                             *
                             * 循环查询相关位置的病害
                             */
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                /*
                                    根据不同的位置进行查询
                                 */
                                switch ($Position) {
                                    case 0://左边墙
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfSideLeft'] = !isset($LeaksStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1://左拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfHanceLeft'] = !isset($LeaksStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2://顶部
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfTop'] = !isset($LeaksStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3://右拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $LeaksStatics['CountOfHanceRight'] = !isset($LeaksStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $LeaksStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default://右边墙
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

                        case 2://掉块病害
                            //完成病害等级统计
                            /**
                             * @var int level 病害等级 0-4级
                             * 
                             * 循环查询相关等级的病害
                             */
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $DropsStatics['CountOfLevel'.$level] = !isset($DropsStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'drop_disease', $whereIn, $details->ExaminationTime)[0]->count : $DropsStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'drop_disease', $whereIn, $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            /**
                             * @var int Position 存放病害在相关位置的具体方位
                             *
                             * 循环查询相关位置的病害
                             */
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                /*
                                    根据不同的位置进行查询
                                 */
                                switch ($Position) {
                                    case 0://左边墙
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfSideLeft'] = !isset($DropsStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1://左拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfHanceLeft'] = !isset($DropsStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2://顶部
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfTop'] = !isset($DropsStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3://右拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $DropsStatics['CountOfHanceRight'] = !isset($DropsStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $DropsStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default://右边墙
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

                        case 3://刮擦病害
                            /**
                             * @var int level 病害等级 0-4级
                             * 
                             * 循环查询相关等级的病害
                             */
                            //完成病害等级统计
                            for ($level=0; $level < 5; $level++) { 
                                $whereIn['SeverityClassfication'] = $level;
                                $ScratchStatics['CountOfLevel'.$level] = !isset($ScratchStatics['CountOfLevel'.$level]) ? $this->theDatas->countTheDetails($tunnelId, 'scratch_disease',$whereIn , $details->ExaminationTime)[0]->count : $ScratchStatics['CountOfLevel'.$level] + $this->theDatas->countTheDetails($tunnelId, 'scratch_disease',$whereIn , $details->ExaminationTime)[0]->count;
                            }
                            unset($whereIn);
                            //完成病害位置统计
                            /**
                             * @var int Position 存放病害在相关位置的具体方位
                             *
                             * 循环查询相关位置的病害
                             */
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                /*
                                    根据不同的位置进行查询
                                 */
                                switch ($Position) {
                                    case 0://左边墙
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfSideLeft'] = !isset($ScratchStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1://左拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfHanceLeft'] = !isset($ScratchStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2://顶部
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfTop'] = !isset($ScratchStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3://右拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ScratchStatics['CountOfHanceRight'] = !isset($ScratchStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ScratchStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default://右边墙
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

                        default://异常病害
                            /**
                             * @var int level 病害等级 0-4级
                             * 
                             * 循环查询相关等级的病害
                             */
                            //完成病害位置统计
                            for ($Position=0; $Position < 5; $Position++) { 
                                $whereIn['Position'] = $Position;
                                /*
                                    根据不同的位置进行查询
                                 */
                                switch ($Position) {
                                    case 0://左边墙
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfSideLeft'] = !isset($ExceptionStatics['CountOfSideLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfSideLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;
                                    
                                    case 1://左拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfHanceLeft'] = !isset($ExceptionStatics['CountOfHanceLeft']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfHanceLeft'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 2://顶部
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfTop'] = !isset($ExceptionStatics['CountOfTop']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfTop'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    case 3://右拱腰
                                        $whereIn['DiseaseType'] = $type;
                                        $whereIn['FoundTime'] = $details->ExaminationTime;
                                        $ExceptionStatics['CountOfHanceRight'] = !isset($ExceptionStatics['CountOfHanceRight']) ? $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count : $ExceptionStatics['CountOfHanceRight'] + $this->theDatas->countTheDetails($tunnelId, 'disease',$whereIn , '')[0]->count;
                                        break;

                                    default://右边墙
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
                
                //释放在循环中的各种病害的临时存放变量
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
        return $tunnelInfo;
    }
    
}
