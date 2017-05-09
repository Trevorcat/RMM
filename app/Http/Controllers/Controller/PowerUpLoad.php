<?php

namespace App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ZipArchive;

class PowerUpLoad extends Controller
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
    public $currentTimer;

    public function __construct(){
    	$this->up = new \App\model\upLoad();
        $this->dataCore = new \App\model\getTheData();

    	$this->path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

    	$this->up->set('path', $this->path.'/public/uploads');
    	$this->up->set("maxsize", 419430400);
    	$this->up->set("allowtype", array("gif", "png", "jpg","jpeg","zip"));
    	$this->up->set("israndname", false);
    }

    /**
     * @param Reequest类 laravel封装类， 用于接受post上传文件信息
     * 
     * @return 0/1 返回值，0：失败， 1：成功
     * 
     * 上传脚本接收到post请求，获取文件指针后存入指定位置 public/uploads
     * 进而自动解压该上传文件到指定位置 public/unzip 
     * 读取改解压完成的文件结构及其内容文件的名称、扩展名， 将文件夹权限设置为775(执行权与读权)
     * 将这些数据整理之后，自动拼接成sql语句，逐条向数据库服务器发送query，完成入库
     * 
     */

    public function upload(Request $request){
    	$zipFile = $request->all();
        //当$zipFile为空时，post发送中出现问题，返回错误反馈
    	if ($zipFile == '') {
    		$error['reason'] = '上传失败';
    		return $error['code'] = 1;
    	}
    	$success = $this->up->upload($zipFile);
        //如果上传成功，也就是ZipFile不为空
       	if ($success) {
    		$zip = new ZipArchive();
            //解包操作，若成功则进入转存操作，若不成功，则返回失败
       		if ($zip->open('uploads/'.$this->up->getFileName()[0], ZIPARCHIVE::CREATE)) {
                //$searchPath 需要遍历的文件夹绝对地址
       			$searchPath = $this->path.'/public/unzip/'.explode('.', explode('/', $zip->filename)[count(explode('/', $zip->filename))-1])[0].'/';
                //$fileName 文件夹的名字
                $fileName = explode('.', explode('/', $zip->filename)[count(explode('/', $zip->filename))-1])[0];
    			$zip->extractTo($this->path.'/public/unzip');
                var_dump($this->path.'/public/unzip/'.$fileName.'/*');
                chmod($this->path.'/public/unzip/'.$fileName, 0775);
    			$zip->close();
                //如果这个文件夹在此绝对路径中
                if (file_exists($searchPath)) {
                    $this->unzipFileStructPath[$fileName] = $this->listDir($searchPath);
                    //遍历文档结构
                    foreach ($this->unzipFileStructPath as $TunnelName => $Source) { 
                        $database = explode('_', $TunnelName)[0];
                        $ExaminationTime = explode('_', $TunnelName)[1];
                        $deleteSuccess = $this->deleteTheTables($database, $ExaminationTime);
                        if ($deleteSuccess == 0) {
                        	return 0;
                        }
                        //进入其中一文件夹
                        foreach ($Source as $DiseaseFolder => $DiseaseFolderContent) {
                            //如果该文件夹中存在.txt文件则开始录入数据，若不存在则跳过这个文件夹
                            if (array_search($DiseaseFolder.$ExaminationTime.'.txt', $DiseaseFolderContent) == false) {
                                continue;
                            }
                            $TextName = $DiseaseFolderContent[array_search($DiseaseFolder.$ExaminationTime.'.txt', $DiseaseFolderContent)];
                            $TextPath = 'unzip/'.$TunnelName.'/'.$DiseaseFolder.'/'.$TextName;
                            $TextContent = $this->readTheText($TextPath);
                            echo "$DiseaseFolder 中共有 $this->currentTimer 条数据等待录入："."<br/>";
                            //进入对应资源包
                            $porcessTime = 1;
                            foreach ($DiseaseFolderContent as $Mark => $PicsFolder) {
                            //如果名称为字符串，则是每个病害对应的资源包，进入对应的资源包
                                if (is_string($Mark)) {
                                    //清除之前可能重复的变量
                                    if (isset($insertParameter)) {
                                        unset($insertParameter);
                                    }
                                    //遍历这个资源包的内容，区分开内部红外图片（.bmp）与高清图片
                                    foreach ($PicsFolder as $key => $value) {
                                        $picType = explode('.', $value)[1];
                                        if ($picType == 'bmp') {
                                            $insertParameter['InfraredVideoPath'] = 'unzip/'.$TunnelName.'/'.$DiseaseFolder.'/'.$Mark.'/'.$value;
                                        }else{
                                            $insertParameter['HighDefinitionVideoPath'.($key+1)] = 'unzip/'.$TunnelName.'/'.$DiseaseFolder.'/'.$Mark.'/'.$value;
                                        }
                                    }
                                    $TextMark = array_search($Mark, $TextContent['DiseaseId']);
                                    //循环读取存放txt文件内容的变量，格式化之后sql需要的字段
                                    foreach ($TextContent as $Title => $Contents) {
                                        $insertParameter[$Title] = $Contents[$TextMark];
                                    }
                                    $insertParameter['PNGFile'] = 'unzip/'.$TunnelName.'/'.$DiseaseFolder.'/'.$Mark.'.png';
                                    echo "第 $porcessTime 条：<br.>";
                                    $requireDatabase = $this->requireOrCreateDatabase($database);
                                    //失败则打印 失败 并继续下一次运行
                                    if ($requireDatabase == 0) {
                                        echo "此处请求或创建 $database 失败，执行下一条记录操作"."<br/>";
                                        continue;
                                    }else{
                                        echo "此处请求或创建 $database 成功"."<br/>";
                                    }
                                    $requireTable = $this->requireOrCreateTable($database, explode('_', $TunnelName)[1]);
                                    //失败则打印 失败 并继续下一次运行
                                    if ($requireTable == 0) {
                                        echo "此处请求或创建 ".explode('_', $TunnelName)[1]." 失败，执行下一条记录操作". "<br/>";
                                        continue;
                                    }else{
                                        echo "此处请求或创建 ".explode('_', $TunnelName)[1]." 成功"."<br/>";
                                    }
                                    $insertSQLSuccess = $this->originInsertSqlString($database, $insertParameter, $DiseaseFolder, explode('_', $TunnelName)[1]);
                                    //失败则打印 失败 并继续下一次运行
                                    if ($insertSQLSuccess == 0) {
                                        echo "此处插入失败，执行下一条记录操作". "<br/>";
                                        continue;
                                    }else{
                                        echo "此处插入成功"."<br/>";
                                    }
                                    $updateSQLSuccess = $this->updateTunnelInfo($database, $ExaminationTime);
                                    //失败则打印 失败 并继续下一次运行
                                    if ($updateSQLSuccess == 0) {
                                        echo "隧道检测信息更新失败". "<br/>";
                                        continue;
                                    }else{
                                        echo "隧道检测信息更新成功". "<br/>";
                                    }
                                    $porcessTime ++;
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

    /**
     * @param $dir 需要被遍历的文件路径
     * 
     * @return $dirStruct 返回该文件路径
     *
     * 以递归的方式，深度优先遍历该文件目录结构
     */
    public function listDir($dir){
        $dirStruct = array();
        if(is_dir($dir)){
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false){
                    if((is_dir($dir."/".$file)) && $file!="." && $file!=".." && $file!=".DS_Store"){
                        $dirStruct[$file] = $this->listDir($dir."/".$file."/",array());
                    }
                    else{
                        if($file!="." && $file!=".."&& $file!=".DS_Store"){
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
     * 自动将读取后的变量处理成规则的数组变量（字典）
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
        var_dump($TextContent);
        foreach ($TextMixed as $TextTile => $array) {
            foreach ($TextContent as $TextCol => $value) {
                if (!is_array($ProcessedTextContent[$TextTile])) {
                    $ProcessedTextContent[$TextTile] = array();
                }
                if (trim($value[$ColNum]) == ' ' && trim($value[$ColNum]) == "\n") {
                    continue;
                }
                array_push($ProcessedTextContent[$TextTile], trim($value[$ColNum]));
            }
            $ColNum ++;
        }
        $this->currentTimer = count($ProcessedTextContent);
        return $ProcessedTextContent;
    }

    /**
     * @param (string) $database 传入需要入库的数据库名
     *
     * @return (string) $return 返回是否成功：0/1
     * 
     * 接受$database参数后，判断数据库服务器中是否存在此数据库
     * 若不存在则创建数据库并自动创建固定的表，若创建成功，则返回成功；若失败，则返回失败
     * 若存在则返回成功
     *
     * 若自动建库，在公共库中，该隧道的描述、经纬度等信息为默认数据，需要工作人员手动修改（1.0）
     */
    public function requireOrCreateDatabase($database){
        $where['TunnelId'] = $database;
        $exists = $this->dataCore->getDataByTablenameAndDatabasename('', 'tunnel_info', $where, '');
        if (count($exists) == 0) {
            $Sql = 'CREATE DATABASE '. $database . ';'."\n"."
                    CREATE TABLE $database.`disease` (
                    `DiseaseID` varchar(255) NOT NULL,
                    `Position` int(4) unsigned NOT NULL,
                    `Mileage` int(4) unsigned DEFAULT NULL,
                    `DiseaseType` int(4) unsigned DEFAULT NULL,
                    `FoundTime` date DEFAULT NULL,
                    `RepaireTime` date DEFAULT NULL,
                    PRIMARY KEY (`DiseaseID`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=gbk;

                    CREATE TABLE $database.`tunnel_info` (
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
                $insertSql = "INSERT INTO `RMM`.`tunnel_info` (`TunnelId`, `TunnelName`, `TunnelDescription`, `Longitude`, `Latitude`, `Mileage`, `PICsFilePath`) VALUES ('$database', '$database', '1.本项目起于约德高速公路召唤师峡谷路段, 2.修建单位： ；监理单位： ；设计单位： ；竣工时间： ', ' ', ' ', ' ', '0837yingxiuhuoshaopingsuidao02/Tunnel.jpg');";
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
     * @param (string) $ExaminationTime 传入需要入库的检测时间
     *
     * @return (string) $insertSuccess 返回是否成功：0/1
     * 
     * 接受$table参数后，判断数据库中是否存在此表
     * 若不存在则创建表创建成功返回1，失败返回0
     * 若存在则返回1
     */
    public function requireOrCreateTable($database, $ExaminationTime){
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
                `InfraredVideoPath` varchar(500) DEFAULT NULL,
                `PointCloudCrossSectionPath` varchar(500) DEFAULT NULL,
                `PNGFile` varchar(500) NOT NULL,
                PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `".$ProcessExaminationTime."crack_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
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
                CONSTRAINT `".$ProcessExaminationTime."leak_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
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
                CONSTRAINT `".$ProcessExaminationTime."drop_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            $CreateTableSql[3] = "CREATE TABLE `".$ProcessExaminationTime."scratch_disease` (
                `DiseaseID` varchar(255) NOT NULL,
                `Area` float DEFAULT NULL,
                `SeverityClassfication` int(4) unsigned DEFAULT NULL,
                `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
                `InfraredVideoPath` varchar(500) DEFAULT NULL,
                `PNGFile` varchar(500) NOT NULL,
                 PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `".$ProcessExaminationTime."scratch_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            $CreateTableSql[4] = "CREATE TABLE `".$ProcessExaminationTime."exception_disease` (
                `DiseaseID` varchar(255) NOT NULL,
                `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
                `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
                `Description` longtext,
                `SeverityClassfication` int(4) unsigned DEFAULT NULL,
                `PNGFile` varchar(500) NOT NULL,
                PRIMARY KEY (`DiseaseID`),
                CONSTRAINT `".$ProcessExaminationTime."exception_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=gbk;";

            foreach ($CreateTableSql as $key => $value) {
                $success = $this->dataCore->sql($database, $value);
                if ($success == 0) {
                    return 0;
                }
            }

            $insertTunnelInfo = "INSERT INTO `$database`.`tunnel_info` (`ExaminationTime`) VALUES ('$SearchFoundTime');";

            $insertSuccess = $this->dataCore->sql($database, $insertTunnelInfo);
            if ($insertSuccess == 0) {
                return 0;
            }
            return 1;
        }
    }

    /**
     * @param (array) $database 传入目标数据库名称
     * @param (array) $data 传入需要入库的数据
     * @param (array) $data 传入需要入库的数据类型（病害类型）
     * @param (array) $ExaminationTime 传入需要入库数据的检测时间
     *
     * @return (string) $insertDetailSuccess 返回是否成功：0/1
     * 
     * 接受$data参数后，判断数据库中此表内是否存在此数据
     * 若不存在则自动建库、自动向配置文件录入添加相关配置
     * 若存在则返回空字符
     */
    public function originInsertSqlString($database, $data, $type, $ExaminationTime){
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
        $exists = $this->dataCore->getDataByTablenameAndDatabasename($database, 'disease', $where, '');
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

        // var_dump("INSERT INTO `$database`.`".$ProcessExaminationTime.$tableName."_disease` ($insertIntoDiseaseDetailKey) VALUES ($insertIntoDiseaseDetailValue);");

        $exists = $this->dataCore->getDataByTablenameAndDatabasename($database, $ProcessExaminationTime.$tableName.'_disease', $where, '');
        $insertDetailSuccess = 1;
        if (count($exists) == 0) {
            $insertDetailSuccess = $this->dataCore->sql($database, "INSERT INTO `$database`.`".$ProcessExaminationTime.$tableName."_disease` ($insertIntoDiseaseDetailKey) VALUES ($insertIntoDiseaseDetailValue);");
        }

        return $insertDetailSuccess;
    }

    /**
     * @param (array) $database 传入目标数据库名
     * @param (array) $ExaminationTime 传入需要入库的检测时间
     *
     * @return (string) $updateSuccess 返回是否更新成功
     * 
     * 接受$data参数后，判断数据库中此表内是否存在此数据
     * 若不存在则返回插入的SQL语句
     * 若存在则返回空字符
     */
    public function updateTunnelInfo($database, $ExaminationTime){
        $Examination = str_split($ExaminationTime, 4);
        $date = str_split($Examination[1], 2);
        $day = $date[1];
        $month = $date[0];
        $year = $Examination[0];
        $SearchFoundTime = $year . '-' . $month . '-' . $day;
        $ProcessExaminationTime = $year . '_' . $month . '_' . $day . '_';

        $tables = array("crack", "leak", "drop", "scratch", "exception");
        foreach ($tables as $key => $value) {
            $countSql = "select count(*) as count from `".$database."`.`".$ProcessExaminationTime.$value."_disease`;";
            $count[$value] = $this->dataCore->countSql($database, $countSql)[0]['count'];
        }
        $updateSql = "UPDATE `$database`.`tunnel_info` SET `CountofCrack`='".$count['crack']."', `CountofLeak`='".$count['leak']."', `CountofDrop`='".$count['drop']."', `CountofScratch`='".$count['scratch']."', `CountofException`='".$count['exception']."' WHERE `ExaminationTime`='".$SearchFoundTime."';";

        $updateSuccess = $this->dataCore->Sql($database, $updateSql);

        if ($updateSuccess != 0) {
            return 1;
        }else{
            return 0;
        }
    }

    public function deleteTheTables($database, $ExaminationTime){
    	$Examination = str_split($ExaminationTime, 4);
        $date = str_split($Examination[1], 2);
        $day = $date[1];
        $month = $date[0];
        $year = $Examination[0];
        $SearchFoundTime = $year . '-' . $month . '-' . $day;
        $ProcessExaminationTime = $year . '_' . $month . '_' . $day . '_';

        $existsDatabase = $this->requireOrCreateDatabase($database);

        if ($existsDatabase == 0) {
        	echo "选择数据库失败";
        	return 0;
        }

    	$deleteTheExaminationSql = "DELETE FROM ".$database.".tunnel_info where ExaminationTime = ".$SearchFoundTime;
    	$success = $this->dataCore->sql($deleteTheExaminationSql);
    	if ($success == 0) {
    		return 0;
    	}

    	$DROPTableSql[0] = "DROP TABLE IF EXISTS `".$ProcessExaminationTime."crack_disease` ";

        $DROPTableSql[1] = "DROP TABLE IF EXISTS `".$ProcessExaminationTime."leak_disease`";

        $DROPTableSql[2] = "DROP TABLE IF EXISTS `".$ProcessExaminationTime."drop_disease`";

        $DROPTableSql[3] = "DROP TABLE IF EXISTS `".$ProcessExaminationTime."scratch_disease`";

        $DROPTableSql[4] = "DROP TABLE IF EXISTS `".$ProcessExaminationTime."exception_disease`";

        foreach ($DROPTableSql as $key => $value) {
            $success = $this->dataCore->sql($database, $value);
            if ($success == 0) {
                return 0;
            }
        }

        return 1;

    }

}
