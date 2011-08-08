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
require_once('../lib/TimeLimitedToken.class.php');

session_start();

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


if (!isset($_SESSION['wikiplex_cachedwiki'])) {
	$_SESSION['wikiplex_cachedwiki'] = array();
}




try {
	
#	echo ('IdP users: ' . $attributes['idp'][0]);
	if ($attributes['idp'][0] == 'https://openidp.feide.no') 
		throw new Exception('Currently OpenWiki administration interface is not available to OpenIdP users.');
	
	/*
	 * What wiki are we talking about?
	 */
	$thiswiki = null;
	if (isset($_REQUEST['edit'])) {
		$_SESSION['edit'] = $_REQUEST['edit'];
		$thiswiki = $_REQUEST['edit'];
	} elseif(isset($_SESSION['edit'])) {
		$thiswiki = $_SESSION['edit'];
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
	
	
	
	
	if (! array_key_exists($thiswiki,$_SESSION['wikiplex_cachedwiki'] ) || 
		isset($_REQUEST['edit']) ) {
	
	// 	echo 'reloading ...' ; exit;
	
		// Create a test wiki
		$ow = new OpenWiki($thiswiki, $link);
		#$ow->setInfo('Test wiki', 'This is a wiki Andreas is testing', 'andreas_solberg_uninett', 3);
	
		if (isset($_REQUEST['createnewsubmit'])) {
			
			if (!preg_match('/^[a-z]+$/', $thiswiki))
				throw new Exception('You tried to create a new wiki, but the wiki ID that you chose contained illegal characters. A wiki ID can only contain lowercase letters [a-z]!');
		
			if (!$ow->isLoaded()) {
				$ow->setOwner($username);
			}
			// Load from db.	
			$ow->loadFromDB();
		}
		
		$_SESSION['wikiplex_cachedwiki'][$thiswiki] =& $ow;
		
		/*
		$ow->setInfo('Test wiki', 'This is a wiki Andreas is testing', 'andreas_solberg_uninett', 3);
		$ow->addACL(array('@org_x_dc_uninett_no', 1));
		$ow->addACL(array('@org_x_dc_uninett_no2', 15));
		$ow->addACL(array('@org_x_dc_uninett_no3', 1));
		$ow->addACL(array('@org_x_dc_uninett_no4', 0));
		*/
		
	} else {
	
		$ow =& $_SESSION['wikiplex_cachedwiki'][$thiswiki];
	
	}
	
	if (!empty($_REQUEST['name'])) {
		
		$ow->setInfo($_REQUEST['name'], $_REQUEST['descr'], $username, $_REQUEST['access']);
	}
	if (isset($_REQUEST['aclswap'])) {
		$ow->swapACL($_REQUEST['aclswap']);
	}
	if (isset($_REQUEST['acldelete'])) {
		$ow->removeACL($_REQUEST['acldelete']);
	}
	if (!empty($_REQUEST['addgroupid'])) {
		$addgroup = str_replace(' ', '', $_REQUEST['addgroupid']);
		$ow->addACL($addgroup, $_REQUEST['addgrouplevel']);
	} elseif(!empty($_REQUEST['addpersonid'])) {
		$addperson = str_replace(' ', '', $_REQUEST['addpersonid']);
		$ow->addACL($addperson, $_REQUEST['addpersonlevel']);
	}
	
	
	if (isset($_REQUEST['save']) ) {
		$ow->setDBhandle($link);
		$ow->savetoDB();
		unset($_SESSION['wikiplex_cachedwiki'][$thiswiki]);
		
		OpenWikiDirectory::writeACLdefinition($link, $config->getValue('aclfile'));
		
		$et = new SimpleSAML_XHTML_Template($config, 'wikisave.php');
		$et->data['header'] = 'Wiki is successfully saved';
		$et->data['identifier'] = $ow->getIdentifier();
		$et->data['name'] = $ow->getName();
		$et->data['descr'] = $ow->getDescr();	
		$et->show();
		exit;
	}
	
	
	$ow->needAdminAccess($username);
	
	
	#echo 'dump: <pre>' . $ow->getACLdefinition() . '</pre>';
	
	
	$duration = $config->getValue('token.duration');
	$tgr = new TimeLimitedToken($config->getValue('secret'), $duration, 1, array($duration, 'read') );
	$newTokenR = $tgr->generate_token();
	
	$tgw = new TimeLimitedToken($config->getValue('secret'), $duration, 1, array($duration, 'write') );
	$newTokenW = $tgw->generate_token();
	
	$urlR = $config->getValue('inviteurl') . '?token=' . $newTokenR . '&perm=read&wiki=' . $thiswiki;
	$urlW = $config->getValue('inviteurl') . '?token=' . $newTokenW . '&perm=write&wiki=' . $thiswiki;
	
	$et = new SimpleSAML_XHTML_Template($config, 'wikiedit.php');
	$et->data['header'] = 'Edit wiki';
	$et->data['tgroups'] = $groups;
	$et->data['taccess'] = $access;
	
	$et->data['identifier'] = $ow->getIdentifier();
	$et->data['name'] = $ow->getName();
	$et->data['descr'] = $ow->getDescr();
	$et->data['acl'] = $ow->getCustomACL();
	$et->data['access'] = $ow->getAccess();
	
	$et->data['tokenr'] = $urlR;
	$et->data['tokenw'] = $urlW;

	
	$et->show();

} catch (Exception $e) {
	
	
	echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Error</title>
</head>
<body><h1>Error</h1><p>' . $e->getMessage() . '</p></body>
</html>';


}


?>