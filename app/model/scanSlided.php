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
            $theDiseases = $this->theDatas->rangeSearch($database, 'disease', $where, '', $whereCol);
        }
        $resoultNum = 0;
        $resoult = NULL;
        foreach ($theDiseases as $time => $diseases) {
           $resoult[$resoultNum] = $diseases;
        }
    	
    	$theDisease['DiseasesInfo'] = $resoult;
    	$theDisease['StartMileage'] = $theDisease['DiseasesInfo'][0]->DiseaseID;
    	$theDisease['EndMileage'] = $theDisease['DiseasesInfo'][count($theDisease['DiseasesInfo'])-1]->DiseaseID;
    	return $theDisease;
    }
}
