CREATE TABLE IF NOT EXISTS `statistics` (
  `id` int(40) NOT NULL auto_increment,
  `type` varchar(15) NOT NULL default 'form',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;