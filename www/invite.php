<?php

$path_extra = '/var/simplesamlphp-openwiki_new/lib';
$path = ini_get('include_path');
$path = $path_extra . PATH_SEPARATOR . $path;
ini_set('include_path', $path);


include('/var/simplesamlphp-openwiki_new/lib/_autoload.php');



/*
 * Loading OpenWiki libraries
 */
require_once('../lib/OpenWiki.class.php');
require_once('../lib/OpenWikiDictionary.class.php');
require_once('../lib/TimeLimitedToken.class.php');

/**
 * Initializating configuration
 */
SimpleSAML_Configuration::init(dirname(dirname(__FILE__)) . '/config', 'wikiplex');
SimpleSAML_Configuration::init('/var/simplesamlphp-openwiki_new/config');

$config = SimpleSAML_Configuration::getInstance('wikiplex');


include('../config/groups.php');




/* Load simpleSAMLphp, configuration and metadata */
$as = new SimpleSAML_Auth_Simple('default-sp');
$as->requireAuth(array(
    'idp' => 'https://idp.feide.no',
));
$attributes = $as->getAttributes();



$username = 'na';
if (isset($attributes['mail'])) {
	$username = $attributes['mail'][0];
}
if (isset($attributes['eduPersonPrincipalName'])) {
	$username = $attributes['eduPersonPrincipalName'][0];
}





try {
	
	/*
	 * What wiki are we talking about?
	 */
	$thiswiki = null;
	if (isset($_REQUEST['wiki'])) {
		$_SESSION['wiki'] = $_REQUEST['wiki'];
		$thiswiki = $_REQUEST['wiki'];
	}
	if (empty($thiswiki)) throw new Exception('No wiki selected');
	
	
	
	$link = mysql_connect(
		$config->getValue('db.host', 'localhost'), 
		$config->getValue('db.user'),
		$config->getValue('db.pass'));
	if(!$link){
		throw new Exception('Could not connect to database: '.mysql_error());
	}
	mysql_select_db($config->getValue('db.name','feideopenwiki'));
	
	
	
	
	$ow = new OpenWiki($thiswiki, $link);
	

// 	echo 'token [' . $_GET['token'] . ']'; exit;

	if (empty($_GET['token'])) throw new Exception('You did not provide a token parameter, which is required.');
	if (empty($_GET['perm'])) throw new Exception('You did not provide a perm parameter, which is required.');
	if (!in_array($_GET['perm'], array('read', 'write'))) throw new Exception('Illegal perm value. Must be [read] or [write].');

	
	$token = $_GET['token'];
	$permission = $_GET['perm'];
	
	
	$duration = $config->getValue('token.duration');
	
	$tg = new TimeLimitedToken($config->getValue('secret'), $duration, 1, array($duration, $permission) );
	
	
	$newToken = $tg->generate_token();
	
//   	echo 'New token: ' . $newToken; exit;
	
// 		echo $username; 
		
	$cacl = $ow->getCustomACL();

// 	echo '<pre>';
// 	print_r($cacl);
// 	exit;
	
	if (!$tg->validate_token($token)) {
		
			$et = new SimpleSAML_XHTML_Template($config, 'inviteok.php');
			$et->data['header'] = 'Invalid token';
			$et->data['identifier'] = $ow->getIdentifier();
			$et->data['body'] = 'Your time limited token was not valid. That is most likely because it is timed out. Probably someone gave you this URL to have access to a wiki, contact them to get a new URL with a fresh token.';
			$et->data['name'] = $ow->getName();
			$et->data['descr'] = $ow->getDescr();	
			$et->show();
			exit;
		
		
	}
	



	
	$permcode = 0;
	if ($permission == 'read') {
		$permcode = 1;
	} elseif ($permission == 'write') {
		$permcode = 32;
	} else {
		throw new Exception('Illegal perm value. Must be [read] or [write]. Was: [' . $permission . ']');
	}

	
	foreach($cacl AS $al) {
		#echo 'comparing [' . $username . ' = ' . $al[0] . '] and [' . $permcode . ' = ' . $al[1] . '] ' . "\n\n";
		if ($al[0] == $username && $al[1] == $permcode) {
			$et = new SimpleSAML_XHTML_Template($config, 'inviteok.php');
			$et->data['header'] = 'You have access already.';
			$et->data['identifier'] = $ow->getIdentifier();
			
			$et->data['body'] = 'No changes neccessary in the access list. Please visit the wiki and login...';

			$et->data['name'] = $ow->getName();
			$et->data['descr'] = $ow->getDescr();	
			$et->show();
			exit;
		}
	}
	

	$ow->addACL($username, $permcode);
	
	$ow->setDBhandle($link);
	$ow->savetoDB();

	
	OpenWikiDirectory::writeACLdefinition($link, $config->getValue('aclfile'));
	
	$et = new SimpleSAML_XHTML_Template($config, 'inviteok.php');
	$et->data['header'] = 'You are now granted access';
	$et->data['identifier'] = $ow->getIdentifier();

	$et->data['body'] = 'Access successfully granted.';

	$et->data['name'] = $ow->getName();
	$et->data['descr'] = $ow->getDescr();	
	$et->show();
	exit;
// 	
// 	echo 'Check equals: ' . $check . ' :'. $thiswiki . '|' . $permission . '|' . $config->getValue('secret');
// 
// 	print_r($ow);
// 	
// 	exit;
// 	
	
	

} catch (Exception $e) {
	
	
	SimpleSAML_Utilities::fatalError((isset($session) ? $session->getTrackID() : null), null, $e);

}


?>