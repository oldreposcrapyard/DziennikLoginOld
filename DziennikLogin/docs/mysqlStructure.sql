-- 
-- Structure for table `emails`
-- 

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `userId` int(11) DEFAULT NULL,
  `userEmail` varchar(254) NOT NULL,
  UNIQUE KEY `userEmail` (`userEmail`,`userId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Structure for table `registerPasswords`
-- 

DROP TABLE IF EXISTS `registerPasswords`;
CREATE TABLE IF NOT EXISTS `registerPasswords` (
  `userId` int(11) NOT NULL,
  `registerUsername` varchar(160) NOT NULL,
  `registerPassword` blob NOT NULL,
  UNIQUE KEY `user_id` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Structure for table `reportjobs`
-- 

DROP TABLE IF EXISTS `reportjobs`;
CREATE TABLE IF NOT EXISTS `reportjobs` (
  `jobId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `reportType` varchar(20) CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  `reportEmail` varchar(160) NOT NULL,
  `reportTo` varchar(20) NOT NULL,
  UNIQUE KEY `jobId` (`jobId`)
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
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- 
-- Structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name',
  `user_password_hash` char(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email',
  `user_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s activation status',
  `user_activation_hash` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s email verification hash string',
  `user_password_reset_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password reset code',
  `user_password_reset_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the password reset request',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';