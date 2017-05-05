<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZipArchive;

class UpLoad extends Controller
{
    /**
    *   $up                     upLoad的指针
    *   $path                   当前的绝对路径
    *   $unzipFileStructPath    解压文件夹的所有文件夹名称（遍历）
    */
    private $up;
    private $dataCore;
    public $path;
    public $unzipFileStructPath;

    public function __construct(){
    	$this->up = new \App\model\upLoad();
        $this->dataCore = new \App\model\getTheData();

    	$this->path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

    	$this->up->set('path', $this->path.'/public/uploads');
    	$this->up->set("maxsize", 419430400);
    	$this->up->set("allowtype", array("gif", "png", "jpg","jpeg","zip"));
    	$this->up->set("israndname", false);
    }

    public function upload(Request $request){
    	$zipFile = $request->all();
    	if ($zipFile == '') {
    		$error['reason'] = '上传失败';
    		return $error['code'] = 1;
    	}
    	$success = $this->up->upload($zipFile);
       	if ($success) {
    		$zip = new ZipArchive();
       		if ($zip->open('uploads/'.$this->up->getFileName()[0], ZIPARCHIVE::CREATE)) {
       			$searchPath = $this->path.'/public/unzip/'.explode('.', explode('/', $zip->filename)[count(explode('/', $zip->filename))-1])[0].'/';
                $fileName = explode('.', explode('/', $zip->filename)[count(explode('/', $zip->filename))-1])[0];
    			$zip->extractTo($this->path.'/public/unzip');
    			$zip->close();

                if (file_exists($searchPath)) {
                    $this->unzipFileStructPath[$fileName] = $this->listDir($searchPath, array());

                    foreach ($this->unzipFileStructPath as $TunnelName => $Source) { //遍历文档机构

                        $ExaminationTime = explode('_', $TunnelName)[1];
                        foreach ($Source as $DiseaseFolder => $DiseaseFolderContent) {
                            if (array_search($DiseaseFolder.$ExaminationTime.'.txt', $DiseaseFolderContent) == false) {
                                continue;
                            }
                            $TextName = $DiseaseFolderContent[array_search($DiseaseFolder.$ExaminationTime.'.txt', $DiseaseFolderContent)];
                            $TextPath = 'unzip/'.$TunnelName.'/'.$DiseaseFolder.'/'.$TextName;
                            $TextContent = $this->readTheText($TextPath);                            
                            var_dump($TextContent);
                            foreach ($DiseaseFolderContent as $Mark => $PicsFolder) {
                                if (is_string($Mark)) {
                                    if (isset($insertParameter)) {
                                        unset($insertParameter);
                                    }
                                    foreach ($PicsFolder as $key => $value) {
                                        $insertParameter['HighDefinitionVideoPath'.($key+1)] = 'unzip/'.$TunnelName.'/'.$DiseaseFolder.'/'.$Mark.'/'.$value;
                                    }
                                    
                                    $TextMark = array_search($Mark, $TextContent['DiseaseId']);
                                    foreach ($TextContent as $Title => $Contents) {
                                        $insertParameter[$Title] = $Contents[$TextMark];
                                        var_dump($insertParameter);
                                    }

                                    $insertParameter['PNGFile'] = 'unzip/'.$TunnelName.'/'.$DiseaseFolder.'/'.$Mark.'.png';
                                    // var_dump($insertParameter);
                                    $database = explode('_', $TunnelName)[0];
                                    $requireDatabase = $this->originCreateDatabaseSqlString($database);
                                    var_dump($requireDatabase);
                                    if ($requireDatabase == 0) {
                                        return 0;
                                    }
                                    $requireTable = $this->originCreateTableSqlString($database, explode('_', $TunnelName)[1]);
                                    var_dump($requireTable);
                                    if ($requireTable == 0) {
                                        return 0;
                                    }
                                    $insertSQL = $this->originInsertSqlString($database, $insertParameter, $DiseaseFolder, explode('_', $TunnelName)[1]);
                                    if ($insertSQL == 0) {
                                        return 0;
                                    }
                                }
                            }
                        }
                    }
                }

    		}else{
    			return 0;
    		}
    	}else{
    		return 0;
    	}
    }

    public function listDir($dir, $parameter){
        $dirStruct = $parameter;
        if(is_dir($dir))
        {
            if ($dh = opendir($dir)) 
            {
                while (($file = readdir($dh)) !== false)
                {
                    if((is_dir($dir."/".$file)) && $file!="." && $file!=".." && $file!=".DS_Store")
                    {
                        $dirStruct[$file] = $this->listDir($dir."/".$file."/",array());
                    }
                    else
                    {
                        if($file!="." && $file!=".."&& $file!=".DS_Store")
                        {
                            array_push($dirStruct, $file);
                        }
                    }
                }
                closedir($dh);
                return $dirStruct;
            }
        }
    }

    /**
     * @param (string) $TextPath 传入text的路径
     * 
     * @return (array) $ProcessedTextContent 返回处理完成的Text信息字典
     * 
     * 接受参数后，读取该路径下的.txt文件
     * 自动将读取后的变量处理成规则的字典变量
     */
    public function readTheText($TextPath){
        $TextHandler = fopen($TextPath, 'r');
        while (!feof($TextHandler)) {
            $TextOriginContent[] = fgets($TextHandler, 4096);
        }
        fclose($TextHandler);
        foreach ($TextOriginContent as $Col => $Content) {
            if ($Col == 0) {
                foreach (explode(' ', $Content) as $key => $value) {
                    $TextMixed[trim($value)] = array();                
                }
            }else{
                $TextContent[$Col] = explode(' ', $Content);
            }
        }
        $ProcessedTextContent = $TextMixed;
        $ColNum = 0;
        foreach ($TextMixed as $TextTile => $array) {
            foreach ($TextContent as $TextCol => $value) {
                if (!is_array($ProcessedTextContent[$TextTile])) {
                    $ProcessedTextContent[$TextTile] = array();
                }
                array_push($ProcessedTextContent[$TextTile], trim($value[$ColNum]));
            }
            $ColNum ++;
        }
        return $ProcessedTextContent;
    }

    /**
     * @param (string) $database 传入需要入库的数据库名
     *
     * @return (string) $return 返回是否能使用该数据库
     * 
     * 接受$database参数后，判断数据库服务器中是否存在此数据库
     * 若不存在则创建数据库并自动创建固定的表，若创建成功，则返回成功；若失败，则返回失败
     * 若存在则返回成功
     */
    public function originCreateDatabaseSqlString($database){
        $where['TunnelId'] = $database;
        $exists = $this->dataCore->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '');
        if (count($exists) == 0) {
            $Sql = 'CREATE DATABASE '. $database . ';'."\n"."
                    CREATE TABLE 0837yingxiuhuoshaopingsuidao03.`disease` (
                    `DiseaseID` varchar(255) NOT NULL,
                    `Position` int(4) unsigned NOT NULL,
                    `Mileage` int(4) unsigned DEFAULT NULL,
                    `DiseaseType` int(4) unsigned DEFAULT NULL,
                    `FoundTime` date DEFAULT NULL,
                    `RepaireTime` date DEFAULT NULL,
                    PRIMARY KEY (`DiseaseID`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=gbk;

                    CREATE TABLE 0837yingxiuhuoshaopingsuidao03.`tunnel_info` (
                    `ExaminationTime` date NOT NULL,
                    `CountofCrack` int(4) unsigned DEFAULT NULL,
                    `CountofLeak` int(4) unsigned DEFAULT NULL,
                    `CountofDrop` int(4) unsigned DEFAULT NULL,
                    `CountofScratch` int(4) unsigned DEFAULT NULL,
                    `CountofException` int(4) unsigned DEFAULT NULL,
                    `Description` longtext,
                    `Severity` int(4) unsigned DEFAULT NULL,
                    PRIMARY KEY (`ExaminationTime`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";
            $success = $this->dataCore->sql('',$Sql) ;
            if ($success == 1) {
                $insertSql = "INSERT INTO `RMM`.`tunnel_info` (`TunnelId`, `TunnelName`, `TunnelDescription`, `Longitude`, `Latitude`, `Mileage`, `PICsFilePath`) VALUES ('$database', '$database', '1.本项目起于约德高速公路召唤师峡谷路段, 2.修建单位：约德尔；监理单位：诺克萨斯；设计单位：符文大陆；竣工时间：2010-2-1', '104.085547', '30.562212', '2321', '0837yingxiuhuoshaopingsuidao02/Tunnel.jpg');";
                $insertSuccess = $this->dataCore->sql('', $insertSql);
                if ($insertSuccess == 0) {
                    return 0;
                }
                $addTheEvnConfig = fopen($this->path.'/.env', 'a+');
                fwrite($addTheEvnConfig, 'DB_DATABASE_'.$database.'='.$database."\n"."\n");
                fclose($addTheEvnConfig);

                $test = fopen($this->path.'/config/database.php', 'r');
                while (!feof($test)) {
                    $show[] = fgets($test, 4096);
                }
                fclose($test);
                $insert = "        'mysql_$database' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => env('DB_DATABASE_$database', 'forge'),
                    'username' => env('DB_USERNAME', 'forge'),
                    'password' => env('DB_PASSWORD', ''),
                    'unix_socket' => env('DB_SOCKET', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                    ],"."\n"."\n";
                array_splice($show, -41, 0, array($insert));
                foreach ($show as $key => $value) {
                    $string = $key == 0 ? $value : $string . $value;
                }
                $testInsert = fopen($this->path.'/config/database.php', 'w+');
                fwrite($testInsert, $string);
                return 1;
            }else{
                return 0;
            }
            
        }else{
            return 1;
        }
    }

    /**
     * @param (string) $table 传入需要入库的表名
     *
     * @return (string) $Sql 返回符合要求的SQL语句
     * 
     * 接受$table参数后，判断数据库中是否存在此表
     * 若不存在则返回创建表的SQL语句
     * 若存在则返回空字符
     */
    public function originCreateTableSqlString($database, $ExaminationTime){
        $Examination = str_split($ExaminationTime, 4);
        $date = str_split($Examination[1], 2);
        $day = $date[1];
        $month = $date[0];
        $year = $Examination[0];

        $SearchFoundTime = $year . '-' . $month . '-' . $day;
        $ProcessExaminationTime = $year . '_' . $month . '_' . $day . '_';

        $where['ExaminationTime'] = $SearchFoundTime;
        $SearchResoult = $this->dataCore->getDataByTablenameAndDatabasename($database, 'tunnel_info', $where, '');
        if (count($SearchResoult) == 1) {
            return 1;
        }else{
            $CreateTableSql[0] = "CREATE TABLE `".$ProcessExaminationTime."crack_disease` (
                `DiseaseID` varchar(255) NOT NULL,
                `Length` float unsigned DEFAULT NULL,
                `Width` float unsigned DEFAULT NULL,
                `SeverityClassfication` int(4) unsigned DEFAULT NULL,
                `Direction` int(4) unsigned DEFAULT NULL,
                `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
                `InfrareVideoPath` varchar(500) DEFAULT NULL,
                `PointCloudCrossSectionPath` varchar(500) DEFAULT NULL,
                `PNGFile` varchar(500) NOT NULL,
                PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `2016_11_08_crack_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            $CreateTableSql[1] = "CREATE TABLE `".$ProcessExaminationTime."leak_disease` (
               `DiseaseID` varchar(255) NOT NULL,
                `Area` float unsigned DEFAULT NULL,
                `SeverityClassfication` int(4) unsigned DEFAULT NULL,
                `IsDry` bit(1) DEFAULT NULL,
                `IceCoverage` bit(1) DEFAULT NULL,
                `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
                `InfraredVideoPath` varchar(500) DEFAULT NULL,
                `PointCloudCrossSectionPath` varchar(500) DEFAULT NULL,
                `PNGFile` varchar(500) NOT NULL,
                PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `2016_11_08_leak_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            $CreateTableSql[2] = "CREATE TABLE `".$ProcessExaminationTime."drop_disease` (
                `DiseaseID` varchar(255) NOT NULL,
                `Area` float DEFAULT NULL,
                `SeverityClassfication` int(4) unsigned DEFAULT NULL,
                `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
                `InfraredVideoPath` varchar(500) DEFAULT NULL,
                `PointCloudCrossSectionPath` varchar(500) DEFAULT NULL,
                `PNGFile` varchar(500) NOT NULL,
                PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `2016_11_08_drop_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            $CreateTableSql[3] = "CREATE TABLE `".$ProcessExaminationTime."scratch_disease` (
                `DiseaseID` varchar(255) NOT NULL,
                `Area` float DEFAULT NULL,
                `SeverityClassfication` int(4) unsigned DEFAULT NULL,
                `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
                `PNGFile` varchar(500) NOT NULL,
                 PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `2016_11_08_scratch_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            $CreateTableSql[4] = "CREATE TABLE `".$ProcessExaminationTime."exception_disease` (
                `DiseaseID` varchar(255) NOT NULL,
                `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
                `Description` longtext,
                `PNGFile` varchar(500) NOT NULL,
                PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `2016_11_08_exception_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            foreach ($CreateTableSql as $key => $value) {
                $success = $this->dataCore->sql($database, $value);
                if ($success == 0) {
                    return 0;
                }
            }

            $insertTunnelInfo = "INSERT INTO `0837yingxiuhuoshaopingsuidao03`.`tunnel_info` (`ExaminationTime`) VALUES ('$SearchFoundTime');";

            $insertSuccess = $this->dataCore->sql($database, $insertTunnelInfo);
            if ($insertSuccess == 0) {
                return 0;
            }
            return 1;
        }
    }

    /**
     * @param (array) $data 传入需要入库的数据
     *
     * @return (string) $Sql 返回符合要求的SQL语句
     * 
     * 接受$data参数后，判断数据库中此表内是否存在此数据
     * 若不存在则返回插入的SQL语句
     * 若存在则返回空字符
     */
    public function originInsertSqlString($database, $data, $type, $ExaminationTime){
        var_dump("asdfasdf");
        $Examination = str_split($ExaminationTime, 4);
        $date = str_split($Examination[1], 2);
        $day = $date[1];
        $month = $date[0];
        $year = $Examination[0];
        $SearchFoundTime = $year . '-' . $month . '-' . $day;
        $ProcessExaminationTime = $year . '_' . $month . '_' . $day . '_';
        switch ($type) {
            case 'Cracks':
                $data['DiseaseType'] = 0;
                $tableName = 'crack';
                break;
            case 'Leaks':
                $data['DiseaseType'] = 1;
                $tableName = 'leak';
                break;
            case 'Drops':
                $data['DiseaseType'] = 2;
                $tableName = 'drop';
                break;
            case 'Scratchs':
                $data['DiseaseType'] = 3;
                $tableName= 'scratch';
                break;
            default:
                $data['DiseaseType'] = 4;
                $tableName = 'exception';
                break;
        }
        $DiseaseTableSQL = "INSERT INTO `".$database."`.`disease` (`DiseaseID`, `Position`, `Mileage`, `DiseaseType`, `FoundTime`) VALUES ('".$data['DiseaseId']."', '".$data['Position']."', '".$data['Mileage']."', '".$data['DiseaseType']."', '".$ExaminationTime."');";
        $where['DiseaseId'] = $data['DiseaseId'];
        var_dump($ProcessExaminationTime.$tableName.'_disease', $where);
        $exists = $this->dataCore->getDataByTablenameAndDatabasename($database, 'disease', $where, '');
        var_dump($exists);
        if (count($exists) == 0) {
            $insertIntoDiseaseTable = $this->dataCore->Sql($database, $DiseaseTableSQL);
            if ($insertIntoDiseaseTable == 0) {
                return 0;
            }
        }

        unset($data['Position']);
        unset($data['Mileage']);
        unset($data['DiseaseType']);
        
        $insertIntoDiseaseDetailKey = '';
        $insertIntoDiseaseDetailValue = '';
        $loopTime = 0;
        foreach ($data as $searchKey => $searchValue) {
            if ($loopTime == 0) {
                $insertIntoDiseaseDetailKey .= "`$searchKey`";
                $insertIntoDiseaseDetailValue .= "'$searchValue'";
            }else{
                $insertIntoDiseaseDetailKey .= ", `$searchKey`";
                $insertIntoDiseaseDetailValue .= ", '$searchValue'";
            }
            $loopTime ++;
        }

        var_dump("INSERT INTO `$database`.`".$ProcessExaminationTime.$tableName."_disease` ($insertIntoDiseaseDetailKey) VALUES ($insertIntoDiseaseDetailValue);");

        $exists = $this->dataCore->getDataByTablenameAndDatabasename($database, $ProcessExaminationTime.$tableName.'_disease', $where, '');
        var_dump(count($exists));
        $insertDetailSuccess = 1;
        if (count($exists) == 0) {
            $insertDetailSuccess = $this->dataCore->sql($database, "INSERT INTO `$database`.`".$ProcessExaminationTime.$tableName."_disease` ($insertIntoDiseaseDetailKey) VALUES ($insertIntoDiseaseDetailValue);");
            var_dump($insertDetailSuccess);
        }

        return $insertDetailSuccess;
    }

    /**
     * @param (array) $originStrings 传入需要入库的原始SQL语句
     *
     * @return (string) $Sql 返回符合要求的SQL语句
     * 
     * 接受$data参数后，判断数据库中此表内是否存在此数据
     * 若不存在则返回插入的SQL语句
     * 若存在则返回空字符
     */
    public function mixTheSqlString($originStrings){

    }

}
