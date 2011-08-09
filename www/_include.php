<?php

$SSPCORE = '/var/www/openwiki.uninett.no/simplesamlphp';

$path_extra = $SSPCORE . '/lib';
$path = ini_get('include_path');
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);

include($SSPCORE . '/lib/_autoload.php');


/*
 * Loading OpenWiki libraries
 */
require_once('../lib/OpenWiki.class.php');
require_once('../lib/OpenWikiDictionary.class.php');
require_once('../lib/TimeLimitedToken.class.php');

session_start();


/**
 * Initializating configuration
 */
SimpleSAML_Configuration::init(dirname(dirname(__FILE__)) . '/config', 'dokuwikiadmin');
SimpleSAML_Configuration::init($SSPCORE . '/config');

$config = SimpleSAML_Configuration::getInstance('dokuwikiadmin');

include('../config/groups.php');


/* Load simpleSAMLphp, configuration and metadata */
$as = new SimpleSAML_Auth_Simple('default-sp');
$as->requireAuth(array(
    'idp' => 'https://idp.feide.no',
));
$attributes = $as->getAttributes();

#$username = $attributes['eduPersonPrincipalName'][0];
$username = 'na';
if (isset($attributes['mail'])) {
	$username = $attributes['mail'][0];
}
if (isset($attributes['eduPersonPrincipalName'])) {
	$username = $attributes['eduPersonPrincipalName'][0];
}

$realm = 'na';
if (preg_match('/^(.*)@(.*)$/', $username, $match)) {
	$realm = $match[2];

}
	error_log('username : ' . $username);

$groups = $attributes['groups'];