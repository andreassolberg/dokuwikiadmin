<?php

/**
 *
 */
class OpenWiki {

	private $identifier;
	private $name;
	private $descr;
	private $owner;
	
	/**
	 * 0 Private
	 * 1 All feide users can read, no anonymous access
	 * 2 Anonymous users can read
	 * 3 Feide users can write, no anonymous access
	 * 4 Feide users can write, anonymous users can read
	 */
	private $access = 0;
	private $customacl = array();
	
	private $loadedFromDB = false;

	function __construct($identifier, $db = null) {
		$this->identifier = $identifier;
		if (!empty($db)) {
			$this->db = $db;
			$this->loadFromDB();
		}
	}
	
	function setInfo($name, $descr, $owner, $access) {
		$this->name = $name;
		$this->descr = $descr;
		$this->owner = $owner;
		$this->access = $access;
	}
	
	public function isLoaded() {
		return $this->loadedFromDB;
	}
	public function loadFromDB() {
	
		$sql ="SELECT * FROM openwiki WHERE id = '" . $this->getIdentifier() . "'";
		
		$result = mysql_query($sql, $this->db);
		
		if(!$result){
			throw new Exception ("Could not successfully run query ($sql) fromDB:" . mysql_error());
		}
		
		if(mysql_num_rows($result) > 0){		
			$row = mysql_fetch_assoc($result);
			
			$this->setInfo($row['name'], $row['descr'], $row['owner'], $row['access']);
			$this->loadACLfromDB();
			$this->loadedFromDB = true;
		}	
		mysql_free_result($result);
	}
	
	private function loadACLfromDB() {
				
		$link = $this->getDBhandle();
		
		$sql ="SELECT * 
			FROM acl
			WHERE wikiid='" . $this->getIdentifier() . "'
			ORDER BY priority";

		$result = mysql_query($sql, $this->db);
		
		if(!$result){
			throw new Exception ("Could not successfully run query ($sql) from DB:" . mysql_error());
		}
		
		if(mysql_num_rows($result) > 0){		
			while($row = mysql_fetch_assoc($result)){
				$this->addACL($row['name'], $row['access']);
			}
		}		
		mysql_free_result($result);
		
	}
	
	public function setDBhandle($db) {
		$this->db = $db;
	}
	
	
	// TODO: addslashes
	public function savetoDB() {
		/*
	id varchar(100) NOT NULL PRIMARY KEY,
	name tinytext,
	descr text,
	owner tinytext,
	access int
		*/
		
		$link = $this->getDBhandle();

		if ($this->isLoaded() ) {
			$sql = "UPDATE openwiki SET 
				name ='" . addslashes($this->getName()) . "', 
				descr ='" . addslashes($this->getDescr()) . "', 
				owner = '" . addslashes($this->getOwner()) . "', 
				access = " . addslashes($this->getAccess()) . " WHERE id = '" . addslashes($this->getIdentifier()) . "'";

			$res = mysql_query($sql, $this->db);
			if(mysql_error()){
				throw new Exception('Invalid query: ' . mysql_error());
			}
			$this->deleteACLinDB();
			
		} else {
		
			$res = mysql_query("INSERT INTO openwiki (id, name, descr, owner, access) values ('" . 
				addslashes($this->getIdentifier()) . "','" . addslashes($this->getName()) . 
				"', '" . addslashes($this->getDescr()) . "', '" . 
				addslashes($this->getOwner()) . "', " . addslashes($this->getAccess()) . ")", $this->db);
			if(mysql_error()){
				throw new Exception('Invalid query: ' . mysql_error());
			}
		}
		
		$this->saveACLtoDB();
	}
	
	// TODO: addslashes
	private function deleteACLinDB() {
		$link = $this->getDBhandle();
		
		$res = mysql_query("DELETE FROM acl WHERE wikiid='" . addslashes($this->getIdentifier()) . "'", $this->db);
		if(mysql_error()){
			throw new Exception('Invalid query: ' . mysql_error());
		}
	}
	
	// TODO: addslashes
	private function saveACLtoDB( ) {
		/*
	id int NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	wikiid varchar(100) NOT NULL,
	name tinytext,
	access int,
	priority int
		*/
		

		
		$link = $this->getDBhandle();
		foreach ($this->customacl AS $priority => $entry) {
		
			$res = mysql_query("INSERT INTO acl (wikiid, name, access, priority) values ('" . 
				addslashes($this->getIdentifier()) . "','" . addslashes($entry[0]) . "', " . 
				addslashes($entry[1]) . ", " . addslashes($priority) . ")", $this->db);
			if(mysql_error()){
				throw new Exception('Invalid query: ' . mysql_error());
			}
		
		}
		

	}
	
	private function getDBhandle() {

		return $this->db;
	}
	
	
	public function getIdentifier() {
		return $this->identifier;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescr() {
		return $this->descr;
	}
	
	public function getOwner() {
		return $this->owner;
	}
	
	public function setOwner($owner) {
		$this->owner = $owner;
	}
	
	public function getAccess() {
		return $this->access;
	}
	
	public function publicACL() {
		$aclmap = array(
			0 => 0,
			1 => 0,
			2 => 1,
			3 => 0,
			4 => 1
		);
		return $aclmap[$this->getAccess()];
	}
	
	public function feideACL() {
		$aclmap = array(
			0 => 0,
			1 => 1,
			2 => 1,
			3 => 32,
			4 => 32
		);
		return $aclmap[$this->getAccess()];
	}
	
	
	public function addACL($groupid, $level) {
		if ($level > 32) throw new Exception('Invalid authentication level');
		$this->customacl[] = array($groupid, $level);
	}
	
	public function removeACL($no) {
		$newacl = array();
		foreach ($this->customacl AS $key => $entry) {
			if ($key != $no) $newacl[] = $entry;
		}
		$this->customacl = $newacl;
	}
	
	public function getCustomACL() {
		return $this->customacl;
	}
	
	public function swapACL($no) {
		$temp = $this->customacl[$no];
		$this->customacl[$no] = $this->customacl[$no+1];
		$this->customacl[$no+1] = $temp;
	}
	
	/**
	 * Does nothing, but throws an exception when user is not the owner 
	 * of this wiki.
	 */
	public function needAdminAccess($username) {
		if ($username != $this->getOwner()) 
			throw new Exception($username . ' is not the owner of this wiki.');
	}

	/**
	 * Encode ASCII special chars
	 *
	 * Some auth backends allow special chars in their user and groupnames
	 * The special chars are encoded with this function. Only ASCII chars
	 * are encoded UTF-8 multibyte are left as is (different from usual
	 * urlencoding!).
	 *
	 * Decoding can be done with rawurldecode
	 *
	 * @author Andreas Gohr <gohr@cosmocode.de>
	 * @see rawurldecode()
	 */
	private function auth_nameencode($name,$skip_group=false){
	  global $cache_authname;
	  $cache =& $cache_authname;
	  $name  = (string) $name;
	
	  if (!isset($cache[$name][$skip_group])) {
		if($skip_group && $name{0} =='@'){
		  $cache[$name][$skip_group] = '@'.preg_replace('/([\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f])/e',
														"'%'.dechex(ord('\\1'))",substr($name,1));
		}else{
		  $cache[$name][$skip_group] = preg_replace('/([\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f])/e',
													"'%'.dechex(ord('\\1'))",$name);
		}
	  }
	
	  return $cache[$name][$skip_group];
	}


	public function getACLdefinition() {
		$def = '# Wiki: ' . $this->getIdentifier() . "\r\n";
		$def  .= $this->getIdentifier() . ':* @ALL ' . $this->publicACL()  . "\r\n";
		$def .= $this->getIdentifier() . ':* @feideusers ' . $this->feideACL() . "\r\n";
		#$def .= $this->getIdentifier() . ':* @feideusers ' . '0' . "\r\n";
		foreach ($this->getCustomACL() AS $aclentry) {
			$def .= $this->getIdentifier() . ':* ' . $this->auth_nameencode($aclentry[0], true) . ' ' . $aclentry[1] . "\r\n";
			#$def .= $this->getIdentifier() . ':* ' . $this->auth_nameencode($aclentry[0], true) . ' ' . '1' . "\r\n";
		}
		#$def .= $this->getIdentifier() . ':* ' . $this->auth_nameencode($this->getOwner()) . ' 1' . "\r\n";
		$def .= $this->getIdentifier() . ':* ' . $this->auth_nameencode($this->getOwner()) . ' 32' . "\r\n";
		$def .= "\r\n\r\n";
		return $def;
	}

}

?>