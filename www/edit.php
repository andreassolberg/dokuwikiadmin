<?php


include('_include.php');





try {
	
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
			
			$okrealms = $config->getValue('okrealms');
			

			
			if (!in_array($realm, $okrealms)) {
				throw new Exception('Users from realm [' . $realm . '] are not authorized to create new wikis. As of summer 2011, creating new wikis is restricted to employees from UNINETT AS. We decided to restrict access until we have decided on a strategy on what set of collaboration tools UNINETT will offer... Contact helpdesk@uninett.no for questions or comments on this.');
			}
			
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