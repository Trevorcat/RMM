<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class scanSlided extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function searchTheDisease($post){
        $database = $post['TunnelInfo']['TunnelId'];
        $where['col'] = 'Mileage';
        $where['start'] = $post['Mileage'];
        $where['range'] = 20;
        $diseases = array();

        $maxMileage = $this->theDatas->theMaxOfCol($database, 'disease', 'Mileage', '')[0]->max;
        if ($post['Mileage'] < 0) {
            $theDisease['DiseasesInfo'] = NULL;    
        }elseif ($post['Mileage'] >= $maxMileage) {
            $theDisease['DiseasesInfo'] = NULL;
        }else{
            foreach ($post['TunnelInfo']['ExaminationTime'] as $key => $ExaminationTime) {
                $whereCol['FoundTime'] = $ExaminationTime;
//
                $rangeWhere = array();
                foreach ($post['Filter'] as $type => $choose) {
                    if ($choose['Select'] == 1) {
                        switch ($type) {
                            case 'Crack':
                                $typeNum = 0;
                                break;
                            case 'Leak':
                                $typeNum = 1;
                                break;
                            case 'Drop':
                                $typeNum = 2;
                                break;
                            case 'Scratch':
                                $typeNum = 3;
                                break;
                            default:
                                $typeNum = 4;
                                break;
                        }
                        array_push($rangeWhere, strtolower($typeNum));
                    }       
                }
                $theDiseases[$key] = $this->theDatas->rangeSearch($database, 'disease', $where, '', $whereCol);
            }
//
            $resoultNum = 0;
            $resoult = NULL;
            foreach ($theDiseases as $time => $diseases) {
                foreach ($diseases as $key => $value) {
                    $resoult[$resoultNum] = $value;
                    $resoultNum ++;
                }
            }
            unset($where);
            if ($resoult != NULL) {
                foreach ($resoult as $diseasesNum => $diseaseSearch) {
                    $where['DiseaseID'] = $diseaseSearch->DiseaseID;
                    $Examination = $diseaseSearch->FoundTime;
                    $Diseasetype = $diseaseSearch->DiseaseType;
                    switch ($Diseasetype) {
                        case '0':                   //裂缝cracks
                        $type = 'crack_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                        case '1':                   //漏洞leaks
                        $type = 'leak_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                        case '2':                   //掉块drop
                        $type = 'drop_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;  
                        case '3':                   //划痕scratch
                        $type = 'scratch_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                        default:                    //异常exception
                        $type = 'exception_disease';
                        $theDetail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $Examination)[0];
                        unset($theDetail->DiseaseID);
                        break;
                    }   
                    $resoult[$diseasesNum]->PNGURL = $theDetail->PNGFile;
                    $theDetail->DiseasePosition['Mileage'] = $resoult[$diseasesNum]->Mileage;
                    $theDetail->DiseasePosition['Position'] = $resoult[$diseasesNum]->Position; 
                    $resoult[$diseasesNum]->Info = $theDetail;
                    unset($theDetail->PNGFile);
                    unset($resoult[$diseasesNum]->Position);
                    unset($resoult[$diseasesNum]->Mileage);
                }
            }
            $sureSearch = array();
            if ($resoult != NULL) {
                foreach ($rangeWhere as $key => $value) {
                    foreach ($resoult as $diseasesNum => $diseaseSearchd) {
                        if ($diseaseSearchd->DiseaseType == $value) {                            array_push($sureSearch, $diseaseSearchd);
                        }else{
                            continue;
                        }
                    }
                }
                
            }
            $NULL[0] = NULL;
            $theDisease['DiseasesInfo'] = $sureSearch == NULL ? $NULL : $sureSearch;
            $theDisease['StartMileage'] = $post['Mileage'];
            $theDisease['EndMileage'] = $post['Mileage'] + 20;
        }
        return $theDisease;
    }

}
