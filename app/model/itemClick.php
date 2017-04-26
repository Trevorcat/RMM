<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class itemClick extends Model
{
    //
    public function __construct(){
    	$this->theDatas = new \App\model\getTheData();
    }

    public function getDiseaseInfo($database, $start)
    {
        $where['start'] = $start->Mileage;
        $where['range'] = 60;
        $where['col'] = 'Mileage';
    	$data = $this->theDatas->rangeSearch($database, 'disease', $where, '');
    	return $data;
    }

    public function getDiseaseDetail($database, $type, $time, $diseaseId, $start = ''){

        if ($start == '') {
            $where['DiseaseId'] = $diseaseId;
            $detail = $this->theDatas->getDataByTablenameAndDatabasename($database, $type, $where, $time);
        }else{
            $where['start'] = $start->Mileage;
            $where['range'] = $start->Mileage + 60;
            $where['col'] = 'Mileage';
            $detail = $this->theDatas->rangeSearch($database, $type, $where, $time, '');
        }
         
    	return $detail;

    }

    public function getTheStartMileage($database){
        $data = $this->theDatas->theMinOfCol($database, 'disease', 'Mileage', '');
        $where['Mileage'] = $data[0]->min;
        $startDiseaseId = $this->theDatas->getDataByTablenameAndDatabasename($database, 'disease', $where, '');
        return $startDiseaseId[0];
    }
}
