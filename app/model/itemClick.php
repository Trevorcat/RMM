<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class itemClick extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getDiseaseInfo($database)
    {
    	$data = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', '', '');
        
    	return $data;
    }

    public function getDiseaseDetail($database, $type, $time, $diseaseId){
    	$where['DiseaseId'] = $diseaseId;
    	$detail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $time);
    	return $detail;

    }

    public function getTheStartMileage($database){
        $data = $this->theDatas->theMinOfCol($database, 'disease', 'Mileage', '');
        $where['Mileage'] = $data[0]->min;
        $startDiseaseId = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', $where, '');
        return $startDiseaseId[0]->DiseaseID;
    }
}
