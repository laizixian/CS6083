CREATE TABLE `Comments` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `noteID` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `comments` varchar(350) DEFAULT NULL,
  PRIMARY KEY (`commentID`),
  KEY `noteID` (`noteID`),
  KEY `username` (`username`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`noteID`) REFERENCES `Note` (`noteID`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`username`) REFERENCES `Users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

CREATE TABLE `Friend` (
  `username` varchar(50) DEFAULT NULL,
  `friend` varchar(50) DEFAULT NULL,
  `flag` tinyint(1) DEFAULT NULL,
  KEY `username` (`username`),
  KEY `friend` (`friend`),
  CONSTRAINT `friend_ibfk_1` FOREIGN KEY (`username`) REFERENCES `Users` (`username`) ON DELETE CASCADE,
  CONSTRAINT `friend_ibfk_2` FOREIGN KEY (`friend`) REFERENCES `Users` (`username`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Has_tag` (
  `noteID` int(11) DEFAULT NULL,
  `tag_name` varchar(50) DEFAULT NULL,
  KEY `noteID` (`noteID`),
  KEY `tag_name` (`tag_name`),
  CONSTRAINT `has_tag_ibfk_1` FOREIGN KEY (`noteID`) REFERENCES `Note` (`noteID`) ON DELETE CASCADE,
  CONSTRAINT `has_tag_ibfk_2` FOREIGN KEY (`tag_name`) REFERENCES `Tag` (`tag_name`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Neighborhood` (
  `NID` int(11) NOT NULL AUTO_INCREMENT,
  `N_name` varchar(50) NOT NULL,
  `area_polygon` polygon DEFAULT NULL,
  PRIMARY KEY (`NID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

CREATE TABLE `Note` (
  `noteID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `scheduleID` int(11) DEFAULT NULL,
  `point_location` point DEFAULT NULL,
  `radius` int(11) DEFAULT NULL,
  `content` varchar(350) DEFAULT NULL,
  `allowC` tinyint(1) DEFAULT NULL,
  `access` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`noteID`),
  KEY `username` (`username`),
  KEY `scheduleID` (`scheduleID`),
  CONSTRAINT `note_ibfk_1` FOREIGN KEY (`username`) REFERENCES `Users` (`username`) ON DELETE CASCADE,
  CONSTRAINT `note_ibfk_2` FOREIGN KEY (`scheduleID`) REFERENCES `Schedules` (`scheduleID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

CREATE TABLE `Rules` (
  `ruleID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `tag_name` varchar(50) DEFAULT NULL,
  `scheduleID` int(11) DEFAULT NULL,
  `NID` int(11) DEFAULT NULL,
  `friend_flag` tinyint(1) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ruleID`),
  KEY `username` (`username`),
  KEY `tag_name` (`tag_name`),
  KEY `scheduleID` (`scheduleID`),
  KEY `NID` (`NID`),
  CONSTRAINT `rules_ibfk_1` FOREIGN KEY (`username`) REFERENCES `Users` (`username`) ON DELETE CASCADE,
  CONSTRAINT `rules_ibfk_2` FOREIGN KEY (`tag_name`) REFERENCES `Tag` (`tag_name`) ON DELETE CASCADE,
  CONSTRAINT `rules_ibfk_3` FOREIGN KEY (`scheduleID`) REFERENCES `Schedules` (`scheduleID`) ON DELETE CASCADE,
  CONSTRAINT `rules_ibfk_4` FOREIGN KEY (`NID`) REFERENCES `Neighborhood` (`NID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

CREATE TABLE `Schedules` (
  `scheduleID` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `repeat_flag` tinyint(4) DEFAULT NULL,
  `weekdays` tinyint(4) DEFAULT NULL,
  `week_flag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`scheduleID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

CREATE TABLE `Tag` (
  `tag_name` varchar(50) NOT NULL,
  PRIMARY KEY (`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Users` (
  `username` varchar(50) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `pass_word` varchar(50) DEFAULT NULL,
  `email` varchar(320) DEFAULT NULL,
  `login_status` tinyint(1) DEFAULT NULL,
  `time_stamp` datetime DEFAULT NULL,
  `point_location` point DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;