DROP PROCEDURE IF EXISTS AUTO_INPUT;

CREATE PROCEDURE AUTO_INPUT(
	IN DATABASE_NAME 		VARCHAR(255),
	IN TABLENAME			VARCHAR(255),
	IN EXAMINATION_TIME		VARCHAR(255),
	IN DISEASE_TYPE			VARCHAR(255),

	OUT SUCCESS				INT
)
BEGIN
	DECLARE TABLE_NAME VARCHAR(255);
	IF NOT EXISTS (SELECT * FROM `RMM.tunnel_info` WHERE NAME = DATABASE_NAME)
	BEGIN
		CREATE DATABASE DATABASE_NAME
	END

	USE DATABASE_NAME;

	

	IF NOT EXISTS (SELECT * FROM CONCAT(DATABASE_NAME, '.tunnel_info') WHERE ExaminationTime = EXAMINATION_TIME)
	BEGIN
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
	END