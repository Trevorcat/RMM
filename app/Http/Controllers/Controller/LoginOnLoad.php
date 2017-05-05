<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginOnLoad extends Controller
{
    //
    public function __construct(){
    	$this->event = new \App\model\loginOnLoad();
    	date_default_timezone_set("Asia/Shanghai");
    }

    public function returnEventInfo(Request $request){
    	$post = $request->json()->all();
        if (!isset($post['Authority']['TunnelID'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There is no \'Authority => TunnelID\''; 
        }else if (!isset($post['UserInfo']['openId'])) {
            $error['error'] = 1;
            return $error['reason'] = 'There is no \'UserInfo => openId\'';
        }else{
            $databases = $post['Authority']['TunnelID'];
            foreach ($databases as $databaseNum => $database) {
                $events = $this->event->getEvents($database);
                $isChecked = $this->event->getIsChecked($database, $post);
                $tunnelName = $this->event->getTunnelName($database);
                foreach ($events as $eventNum => $event) {
                    $event->IsChecked = $isChecked;
                    $event->DiseaseCount['CountofCrack'] = $event->CountofCrack;
                    $event->DiseaseCount['CountofLeak'] = $event->CountofLeak;
                    $event->DiseaseCount['CountofDrop'] = $event->CountofDrop;
                    $event->DiseaseCount['CountofScratch'] = $event->CountofScratch;
                    $event->DiseaseCount['CountofException'] = $event->CountofException;
                    unset($event->CountofCrack);
                    unset($event->CountofLeak);
                    unset($event->CountofDrop);
                    unset($event->CountofScratch);
                    unset($event->CountofException);
                    if ($event->isChecked == 1) {
                        unset($event);
                        continue;
                    }
                    $eventInfo['TunnelPicURL'] = $event->PICsFilePath;
                }
                $eventInfo['TunnelName'] = $tunnelName;
                $isDely = 0;
                foreach ($events as $key => $value) {
                    $nextTime = $key + 1 == count($events) ? strtotime(date("y-m-d")) : strtotime($events[$key + 1]->ExaminationTime);
                    if (ceil(strtotime($value->ExaminationTime) - $nextTime) > 365) {
                        $isDely = 1;
                    }
                    $value->IsDely = $isDely;
                }
                $eventInfo['Events'] = $events;
                $returnEventInfo['EventInfo'][$databaseNum] = $eventInfo;
            }
            return $returnEventInfo;
        }
    }

}
