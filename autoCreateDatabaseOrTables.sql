DELIMITER //

-- ROP PROCEDURE IF EXISTS AUTO_CREATE_DATABASE_OR_TABLES;

CREATE PROCEDURE AUTO_CREATE_DATABASE_OR_TABLES(
	IN DATABASE_NAME 		VARCHAR(255),
	IN EXAMINATION_TIME		VARCHAR(255),

	OUT SUCCESS				INT
)
BEGIN
	DECLARE TABLE_NAME VARCHAR(255) DEFAULT null;
    
	IF NOT EXISTS (SELECT * FROM `RMM.tunnel_info` WHERE NAME = DATABASE_NAME) THEN
	BEGIN
		CREATE DATABASE DATABASE_NAME;

		CREATE TABLE `disease` (
		  	`DiseaseID` varchar(255) NOT NULL,
		  	`Position` int(4) unsigned NOT NULL,
		  	`Mileage` int(4) unsigned DEFAULT NULL,
		  	`DiseaseType` int(4) unsigned DEFAULT NULL,
		  	`FoundTime` date DEFAULT NULL,
		  	`RepaireTime` date DEFAULT NULL,
		  	PRIMARY KEY (`DiseaseID`)
		) ENGINE=InnoDB DEFAULT CHARSET=gbk;

		CREATE TABLE `tunnel_info` (
		  	`ExaminationTime` date NOT NULL,
		  	`CountofCrack` int(4) unsigned DEFAULT NULL,
		  	`CountofLeak` int(4) unsigned DEFAULT NULL,
		  	`CountofDrop` int(4) unsigned DEFAULT NULL,
		  	`CountofScratch` int(4) unsigned DEFAULT NULL,
		  	`CountofException` int(4) unsigned DEFAULT NULL,
		  	`Description` longtext,
		  	`Severity` int(4) unsigned DEFAULT NULL,
		  	PRIMARY KEY (`ExaminationTime`)
		) ENGINE=InnoDB DEFAULT CHARSET=gbk;

		END;
	END IF;

	USE DATABASE_NAME;

    SET TABLE_NAME = CONCAT(@DATABASE_NAME, '.tunnel_info');
	IF NOT EXISTS (SELECT * FROM TABLE_NAME WHERE ExaminationTime = EXAMINATION_TIME)
	THEN BEGIN
		DECLARE TABLE_NAME VARCHAR(255);
		SET TABLE_NAME = CONCAT(EXAMINATION_TIME, '_crack_disease');
		CREATE TABLE TABLE_NAME(
			`DiseaseID` varchar(255) NOT NULL,
  			`HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
  			`HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
  			`HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
  			`Description` longtext,
  			`PNGFile` varchar(500) NOT NULL,
  			PRIMARY KEY (`DiseaseID`),
  			CONSTRAINT `2016_11_08_exception_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
		) ENGINE=InnoDB DEFAULT CHARSET=gbk;

		SET TABLE_NAME = CONCAT(EXAMINATION_TIME, '_leak_disease');
		CREATE TABLE TABLE_NAME(
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
		) ENGINE=InnoDB DEFAULT CHARSET=gbk;

		SET TABLE_NAME = CONCAT(EXAMINATION_TIME, '_drop_disease');
		CREATE TABLE TABLE_NAME(
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
		) ENGINE=InnoDB DEFAULT CHARSET=gbk;

		SET TABLE_NAME = CONCAT(EXAMINATION_TIME, '_scratch_disease');
		CREATE TABLE TABLE_NAME(
			`DiseaseID` varchar(255) NOT NULL,
			`Area` float DEFAULT NULL,
			`SeverityClassfication` int(4) unsigned DEFAULT NULL,
			`HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
			`HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
			`HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
			`PNGFile` varchar(500) NOT NULL,
			PRIMARY KEY (`DiseaseID`),
			CONSTRAINT `2016_11_08_scratch_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
		) ENGINE=InnoDB DEFAULT CHARSET=gbk;

		SET TABLE_NAME = CONCAT(EXAMINATION_TIME, '_exception_disease');
		CREATE TABLE TABLE_NAME(
			`DiseaseID` varchar(255) NOT NULL,
		    `HighDefinitionVideoPath1` varchar(500) DEFAULT NULL,
		    `HighDefinitionVideoPath2` varchar(500) DEFAULT NULL,
		    `HighDefinitionVideoPath3` varchar(500) DEFAULT NULL,
		    `Description` longtext,
		    `PNGFile` varchar(500) NOT NULL,
		    PRIMARY KEY (`DiseaseID`),
		    CONSTRAINT `2016_11_08_exception_disease_ibfk_1` FOREIGN KEY (`DiseaseID`) REFERENCES `disease` (`DiseaseID`)
		) ENGINE=InnoDB DEFAULT CHARSET=gbk;

		END;
	END IF;

	IF @@ERROR = 0
		THEN	SET SUCCESS = 1;
	ELSE
		SET SUCCESS = 0;
	END IF;

END
//
DELIMITER ; 