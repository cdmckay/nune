#
# Table structure for table 'news'
#

CREATE TABLE news (
  artnr int(10) unsigned DEFAULT '0' NOT NULL auto_increment,
  title varchar(70) DEFAULT '' NOT NULL,
  previewtext blob NOT NULL,
  htmltext blob NOT NULL,
  author varchar(50) DEFAULT '' NOT NULL,
  email varchar(50) DEFAULT '' NOT NULL,
  date varchar(50) DEFAULT '' NOT NULL,
  PRIMARY KEY (artnr)
);

#
# Table structure for table 'user'
#

CREATE TABLE user (
  username varchar(20) DEFAULT '' NOT NULL,
  password varchar(50) DEFAULT '' NOT NULL,
  email varchar(50) DEFAULT '' NOT NULL,
  access int(1) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (username)
); 

#
# This is the confirmation post
#

INSERT INTO news VALUES (
	1,
	'Welcome to NUNE!',
	'Welp, welcome to NUNE, yet another news script for your enjoyment.  To login, go to the news/admin directory and login as username nune, password nune (CASE SENSITIVE) and then delete that user and make a new one with Access Level 3.  Then make as many as you damn well please :)  Please report any bugs you find to <A HREF=\"mailto:mercutio2k@hotmail.com\">mercutio2k@hotmail.com</A>.  Any changes you make please mail me just so I can see what people have done with my script.  NUNE was created from MyNews, another news script.  Although MyNews was used as a base, very little remains from that script.  Enjoy!',
	'',
	'Mercutio',
	'mercutio2k@hotmail.com',
	'[00:00 AM] January 0, 0000'
);

#
# This is the first user
#

INSERT INTO user VALUES (
	'nune',
	'lI1omYv.pyEhQ',
	'nune@nune.com',
	'3'
);
