ALTER TABLE `address` ADD `delivery_setting_id` INT NOT NULL DEFAULT '1',
ADD `delivery_distance` FLOAT NOT NULL DEFAULT '0';

CREATE TABLE delivery_settings (
  id int(11) NOT NULL AUTO_INCREMENT,
  mode tinyint(1) NOT NULL DEFAULT '1',
  country varchar(255) NOT NULL,
  city varchar(255) NOT NULL,
  adress varchar(255) NOT NULL,
  free_distance float(9,3) NOT NULL DEFAULT '0.000',
  price_per_one float(9,2) NOT NULL DEFAULT '0.00',
  active tinyint(1) NOT NULL DEFAULT '1',
  sort tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;