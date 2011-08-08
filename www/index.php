<?php

$path_extra = '/var/simplesamlphp-openwiki_new/lib';
$path = ini_get('include_path');
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);


include('/var/simplesamlphp-openwiki_new/lib/_autoload.php');
/*
 * Loading OpenWiki libraries*
 */
require_once('../lib/OpenWiki.class.php');
require_once('../lib/OpenWikiDictionary.class.php');

/**
 * Initializating configuration
 */
SimpleSAML_Configuration::init(dirname(dirname(__FILE__)) . '/config', 'simplemultiwiki');
SimpleSAML_Configuration::init('/var/simplesamlphp-openwiki_new/config');

$config = SimpleSAML_Configuration::getInstance('simplemultiwiki');




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
$groups = $attributes['groups'];






$link = mysql_connect(
	$config->getValue('db.host', 'localhost'), 
	$config->getValue('db.user'),
	$config->getValue('db.pass'));
if(!$link){
	throw new Exception('Could not connect to database: '.mysql_error());
}
mysql_select_db($config->getValue('db.name','feideopenwiki'));


$owd = new OpenWikiDirectory($link);


$list = $owd->getListPublic();
$listprivate = $owd->getListOwner($username);
$listall = $owd->getList();

$template = 'wikilist.php';

if ($_GET['template'] === '2') {
	$template = 'wikilistbeta.php';
}

$et = new SimpleSAML_XHTML_Template($config, $template);
$et->data['header'] = 'List of wikis';
$et->data['user'] = $username;
$et->data['groups'] = $groups;
$et->data['listpublic'] = $list;
$et->data['listall'] = $listall;
$et->data['listprivate'] = $listprivate;


$et->show();


?>