CREATE TABLE `ukm_kontakter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_owa` varchar(255) DEFAULT NULL,
  `country` int(4) DEFAULT NULL,
  `first_name` varchar(150) DEFAULT NULL,
  `last_name` varchar(150) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `email1` varchar(255) DEFAULT NULL,
  `email2` varchar(255) DEFAULT NULL,
  `email3` varchar(255) DEFAULT NULL,
  `phone_mobile` int(8) DEFAULT NULL,
  `phone_home` int(8) DEFAULT NULL,
  `phone_work` int(8) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone_company` int(8) DEFAULT NULL,
  `owner` enum('system','torstein','ingerlise','jardar','anne','marius') DEFAULT 'system',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
