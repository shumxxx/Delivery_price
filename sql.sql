CREATE TABLE delivery_settings (
  id int(11) NOT NULL AUTO_INCREMENT,
  mode tinyint(1) NOT NULL DEFAULT '1',
  country varchar(255) NOT NULL,
  city varchar(255) NOT NULL,
  adress varchar(255) NOT NULL,
  free_distance float(9,3) NOT NULL DEFAULT '0.000',
  price_per_one float(9,2) NOT NULL DEFAULT '0.00',
  active tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;