CREATE TABLE comments (
  cid bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
  assoc_post int(10) unsigned DEFAULT '0' NOT NULL,
  username varchar(20) DEFAULT '' NOT NULL,
  email varchar(50) DEFAULT '' NOT NULL,
  host varchar(50) DEFAULT '' NOT NULL,
  comment blob NOT NULL,
  date varchar(50) DEFAULT '' NOT NULL,
  timestamp timestamp(14),
  PRIMARY KEY (cid)
);

CREATE TABLE news (
  pid bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
  title varchar(70) DEFAULT '' NOT NULL,
  article blob NOT NULL,
  more_article blob,
  author varchar(50) DEFAULT '' NOT NULL,
  email varchar(50) DEFAULT '' NOT NULL,
  date varchar(50) DEFAULT '' NOT NULL,
  timestamp timestamp(14),
  PRIMARY KEY (pid)
);

CREATE TABLE score (
  username varchar(20) DEFAULT '' NOT NULL,
  plus bigint(20) unsigned DEFAULT '1' NOT NULL,
  minus bigint(20) unsigned DEFAULT '1' NOT NULL,
  mp int(10) unsigned DEFAULT '0' NOT NULL,
  last timestamp(14)
);

CREATE TABLE user (
  user_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment,
  username varchar(20) DEFAULT '' NOT NULL,
  password varchar(50) DEFAULT '' NOT NULL,
  email varchar(50) DEFAULT '' NOT NULL,
  access int(1) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (user_id,username)
);

