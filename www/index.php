<?php

include('_include.php');




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

if (isset($_GET['template']) && $_GET['template'] === '2') {
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