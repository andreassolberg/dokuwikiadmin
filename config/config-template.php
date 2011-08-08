<?php
/* 
 * The configuration of wikiplex
 * 
 * 
 */

$config = array (

	'inviteurl' => 'https://openwiki.feide.no/invite.php',
	'basedir' => '/www/openwiki.feide.no/',
	'simplesamlphpdir' 		=> NULL,
	'templatedir'           => 'templates/',
	
	'aclfile'	=> '/www/ow.feide.no/conf/acl.auth.php',
	
	
	
	/*
	 * Languages available and what language is default
	 */
	'language.available'	=> array('en', 'no'),
	'language.default'		=> 'en',
	
	'secret' => 'xxxx',
	'token.duration' => 259200, // In seconds
	
	// 
	
	'db.host'	=> 'sql.example.org',
	'db.user'	=> 'user',
	'db.pass'	=> 'pass',
	'db.name'	=> 'feideopenwiki'

);