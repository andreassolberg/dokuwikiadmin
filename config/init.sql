CREATE TABLE openwiki (
	id varchar(100) NOT NULL PRIMARY KEY,
	name tinytext,
	descr text,
	owner tinytext,
	access int
);

CREATE TABLE acl (
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	wikiid varchar(100) NOT NULL,
	name tinytext,
	access int,
	priority int
);