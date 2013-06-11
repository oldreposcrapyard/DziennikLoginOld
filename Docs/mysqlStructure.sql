-- 
-- Structure for table `grades`
-- 

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `userId` int(11) NOT NULL,
  `subjectId` int(11) NOT NULL,
  `gradeValue` float NOT NULL,
  `gradeWeight` tinyint(1) NOT NULL,
  `gradeGroup` varchar(160) NOT NULL,
  `gradeTitle` varchar(160) NOT NULL,
  `gradeDate` date NOT NULL,
  `gradeAbbrev` varchar(3) NOT NULL,
  `gradeTrimester` tinyint(1) NOT NULL,
  `gradeDownloadDate` datetime NOT NULL,
  `gradeShown` tinyint(1) NOT NULL,
  KEY `userId` (`userId`)
) ENGINE=MRG_MyISAM DEFAULT CHARSET=utf8;

-- 
-- Structure for table `subjects`
-- 

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subjectId` int(11) NOT NULL AUTO_INCREMENT,
  `subjectName` varchar(30) NOT NULL,
  PRIMARY KEY (`subjectId`)
) ENGINE=MRG_MyISAM DEFAULT CHARSET=utf8;

-- 
-- Structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(100) NOT NULL,
  `userPassword` varchar(100) NOT NULL,
  `registerUsername` varchar(100) NOT NULL,
  `registerPassword` varchar(100) NOT NULL,
  UNIQUE KEY `userId` (`userId`)
) ENGINE=MRG_MyISAM DEFAULT CHARSET=utf8;


