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
  UNIQUE KEY `uniqueFingerprint` (`userId`,`subjectId`,`gradeValue`,`gradeWeight`,`gradeTitle`,`gradeGroup`,`gradeAbbrev`,`gradeTrimester`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Structure for table `subjects`
-- 

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subjectId` int(11) NOT NULL AUTO_INCREMENT,
  `subjectName` varchar(30) NOT NULL,
  PRIMARY KEY (`subjectId`),
  UNIQUE KEY `subjectName` (`subjectName`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
