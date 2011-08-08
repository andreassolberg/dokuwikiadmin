<?php

require_once('OpenWiki.class.php');

/**
 *
 */
class OpenWikiDirectory {

	private $wikilist = array();
	
	private $db;
	private $loadedFromDB = false;

	function __construct($db = null) {
		if (!empty($db)) {
			$this->db = $db;
			$this->loadFromDB();
		}
	}

	public function loadFromDB() {
	
		$sql = "SELECT id FROM openwiki ORDER BY name";
		
		$result = mysql_query($sql, $this->db);
		
		if(!$result){
			throw new Exception ("Could not successfully run query ($sql) fromDB:" . mysql_error());
		}
		
		if(mysql_num_rows($result) > 0){		
		
			while ($row = mysql_fetch_assoc($result) ) {
			
				$newwiki = new OpenWiki($row['id'], $this->db);
				$this->wikilist[$row['id']] = $newwiki;
				
				#echo $newwiki->getACLdefinition();
				#echo 'found wiki: ' . $row['id'];
			}
		}
		mysql_free_result($result);
	}
	public function getList() {
		return $this->wikilist;
	}
	public function getListPublic() {
		$list = array();
		foreach ($this->wikilist AS $wiki) {
			if ($wiki->publicACL() > 0) $list[] = $wiki;
		}
		return $list;
	}

	public function getListOwner($owner) {
		$list = array();
		foreach ($this->wikilist AS $wiki) {
			if ($wiki->getOwner() == $owner) $list[] = $wiki;
		}
		return $list;
	}
	
	public static function writeACLdefinition($db, $filename) {
		if (!file_exists($filename)) throw new Exception('ACL file does not exist or is readable.');
		if (!is_writable($filename)) throw new Exception('ACL file is not writable.');
	
		$owd = new OpenWikiDirectory($db);
		$acldef = '# acl.auth.php
# <?php exit()?>
# Access Control Lists
#
# Auto-generated by wikplex 
# Date: ' . date(DATE_RFC822) . '
*               @ALL          0
:*				@ALL		1

sidebar			@ALL		1

';
		foreach ($owd->getList() AS $w) {
			$acldef .= $w->getACLdefinition();
		}
		
		#int file_put_contents ( string $filename , mixed $data [, int $flags [, resource $context ]] )
		file_put_contents($filename, $acldef, LOCK_EX | FILE_TEXT);
		
	}

}

?>