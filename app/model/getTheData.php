<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;

class getTheData extends Model
{
    //
    public function __construct(){
    }

    public function sql($database, $sql){

        $pdo = new PDO('mysql:host=123.206.226.28;dbname='.$database.';port=3306','root','');
        $pdo->exec('set names utf8');
        $success = $pdo->query($sql);
        return $success == NULL ? 0 : 1;
    }

    public function countSql($database, $sql){

        $pdo = new PDO('mysql:host=123.206.226.28;dbname='.$database.';port=3306','root','');
        $pdo->exec('set names utf8');

        $success = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $success == NULL ? 0 : $success;
    }

    public function countTheDetails($database, $table, $wheres = '', $time = ''){
        $databaseName = $this->databaseName($database);
        $tableName = $time == '' ? $table : $this->tableName($time, $table);
        if (is_string($wheres)) {
            $count = $wheres == '' ? 0 :DB::connection($databaseName)->select('select count(*) as count from ' . $tableName . ' where ' . $wheres);
        }else{
            $count = $wheres == '' ? 0 :DB::connection($databaseName)->select('select count(*) as count from ' . $tableName . ' where ' . $this->where($wheres));
        }
        return $count;
    }

    public function theMinOfCol($database, $table, $col = '', $time = ''){
        $databaseName = $this->databaseName($database);
        $tableName = $time == '' ? $table : $this->tableName($time, $table);

        $min = $col == '' ? 0 :DB::connection($databaseName)->select('select min(' . $col . ') as min from ' . $tableName );
        return $min;
    }

    public function theMaxOfCol($database, $table, $col = '', $time = ''){
        $databaseName = $this->databaseName($database);
        $tableName = $time == '' ? $table : $this->tableName($time, $table);

        $max = $col == '' ? 0 :DB::connection($databaseName)->select('select max(' . $col . ') as max from ' . $tableName );
        return $max;
    }

    public function getDataByTablenameAndDatabasename($database, $table, $wheres = '', $time = ''){
        $databaseName = $this->databaseName($database);

    	$tableName = $time == '' ? $table : $this->tableName($time, $table);

        if (is_string($wheres)) {
             $datas = $wheres == '' ? DB::connection($databaseName)->select('select * from ' . $tableName) : DB::connection($databaseName)->select('select * from ' . $tableName . ' where ' . $wheres);
         }else{
            $datas = $wheres == '' ? DB::connection($databaseName)->select('select * from ' . $tableName) : DB::connection($databaseName)->select('select * from ' . $tableName . ' where ' . $this->where($wheres));
         }
    	return $datas;
    }

    public function updateTheData($database, $table, $wheres = '', $time = ''){
        $databaseName = $this->databaseName($database);
        DB::connection($databaseName)->update('update ' . $table . ' set IsChecked = 1 where OpenId = ? and TunnelId = ? ', [$wheres['OpenId'], $wheres['TunnelID']]);
    }

    public function databaseName($tunnel_num){
        if ($tunnel_num == '') {
            $databaseName = 'mysql';
        }else{
            $databaseName = 'mysql_' . $tunnel_num;
        }
    	return $databaseName;
    }

    public function tableName($time, $classify){
    	$tableName = '';
    	$transTime = explode('-', $time);
    	foreach ($transTime as $key => $value) {
    		$tableName .= $value . '_' ;
    	}
    	$tableName .= $classify;
    	return $tableName;
    }

    public function where($parameter){
    	$where = '';
    	foreach ($parameter as $key => $value) {
    		if ($where === '') {
    			$where = $key .  ' = ' . '\'' . $value . '\'';
    			continue;
    		} 
    		$mix = $key . ' = ' . '\'' . $value . '\'';
    		$where .= 'and ' . $mix;
    	}
    	return $where;
    }

    public function countLevelWhere($where){
        $SQLString = '';
        foreach ($where['DiseaseID'] as $key => $value) {
            if ($SQLString == '') {
                $SQLString .= '(DiseaseID = '. '\'' . $value->DiseaseID . '\'';
            }
            else{
                $SQLString .= ' or DiseaseID = ' . '\'' . $value->DiseaseID . '\'';
            }
        }
        return $SQLString .') and SeverityClassfication = ' . '\'' . $where['SeverityClassfication'] . '\'';
    }

    public function rangeSearch($database, $table, $where, $time, $whereCol = ''){
        $databaseName = $this->databaseName($database);
        $tableName = $time == '' ? $table : $this->tableName($time, $table);
        $rangeCover = $where['start'] + $where['range'];

        $wheres = $whereCol == '' ? $where['col'] . ' >= ' . $where['start'] . ' and ' . $where['col'] . ' <= ' . $rangeCover . ' order by Mileage asc'  : $where['col'] . ' >= ' . $where['start'] . ' and ' . $where['col'] . ' <= ' . $rangeCover . ' and ' . $this->where($whereCol) . ' order by Mileage asc';

        $range = $wheres == '' ? 0 :DB::connection($databaseName)->select('select * from ' . $tableName . ' where ' . $wheres);
        return $range;
    }

    public function rangeSearchForOkClick($database, $table, $where = '', $time, $whereCol = ''){
        $databaseName = $this->databaseName($database);
        $tableName = $time == '' ? $table : $this->tableName($time, $table);
        $setTheRange = '';

        if ($where != '') {
            foreach ($where as $key => $value) {
                if ($key == 'CrackMinLength' || $key == 'CrackMinWidth' || $key == 'LeakMinArea' || $key == 'DropMinArea' || $key == 'CratchMinArea') {
                    if ($key == 'CrackMinLength') {
                        $setTheRange = $setTheRange == '' ? $setTheRange . ' and Length >= ' . $value : 'Length >= ' . $value;
                    }else if ($key == 'CrackMinWidth') {
                        $setTheRange = $setTheRange == '' ? $setTheRange . ' and Width >= ' . $value : 'Width >= ' . $value;
                    }else {
                        $setTheRange = $setTheRange == '' ? $setTheRange . ' and Area >= ' . $value : 'Area >= ' . $value;
                    }
                }else{
                    if ($key == 'CrackMaxLength') {
                        $setTheRange = $setTheRange == '' ? $setTheRange . ' and Length <= ' . $value : 'Length <= ' . $value;
                    }else if ($key == 'CrackMaxWidth') {
                        $setTheRange = $setTheRange == '' ? $setTheRange . ' and Width <= ' . $value : 'Width <= ' . $value;
                    }else {
                        $setTheRange = $setTheRange == '' ? $setTheRange . ' and Area <= ' . $value : 'Area <= ' . $value;
                    }
                }
            }
        }
        $wheres = $whereCol == '' ? $setTheRange : $setTheRange . ' and ' . $this->where($whereCol) ;
        $range = $wheres == '' ? DB::connection($databaseName)->select('select * from ' . $tableName) : DB::connection($databaseName)->select('select * from ' . $tableName . ' where ' . $wheres);
        return $range;
    }

}
