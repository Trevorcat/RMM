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
        foreach ($post['TunnelInfo']['ExaminationTime'] as $key => $value) {
            $whereCol['FoundTime'] = $value;
            $theDiseases[$key] = $this->theDatas->rangeSearch($database, 'disease', $where, '', $whereCol);
        }
        $resoultNum = 0;
        $resoult = NULL;
        foreach ($theDiseases as $time => $diseases) {
            foreach ($diseases as $key => $value) {
                $resoult[$resoultNum] = $value;
                $resoultNum ++;
            }
        }
        unset($where);
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

            $resoult[$diseasesNum]->DiseaseDetail = $theDetail;
        }
        $theDisease['DiseasesInfo'] = $resoult;
        $theDisease['StartMileage'] = $theDisease['DiseasesInfo'] == NULL ? $post['Mileage'] : $theDisease['DiseasesInfo'][0]->Mileage;
        $theDisease['EndMileage'] = $theDisease['DiseasesInfo'] == NULL ? $post['Mileage'] + 20 : $theDisease['DiseasesInfo'][count($theDisease['DiseasesInfo'])-1]->Mileage;
        return $theDisease;

    }
}
