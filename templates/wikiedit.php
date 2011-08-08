<?php 
	$this->includeAtTemplateBase('includes/header.php'); 


?>




<form method="post" action="edit.php">

	<h1><?php if (isset($this->data['header'])) { echo $this->data['header']; } else { echo "Some error occured"; } ?></h1>

	<p>Editing your wiki.</p>
	
	<p>Identifier: <strong><?php echo $this->data['identifier']; ?></strong></p>
	
	<p>Name: <input type="text" name="name" value="<?php echo $this->data['name']; ?>" /></p>
	<p>Description: <br /><textarea name="descr" style="width: 300px; height: 100px"><?php echo $this->data['descr']; ?></textarea>
	</p>
	
	<p>Default access control: 
	<select name="access">
	<?php
		foreach ($this->data['taccess'] AS $code => $text) {
			echo '<option ' . ($code == $this->data['access'] ? 'selected="selected" ' : '') . 
				'value="' . $code . '">' . $text . '</option>';
		}
	?></select></p>
	
	
	
	

	<h2 style="margin-top: 2em;">Invitation URLs</h2>
	
	<p>If you give away this URLs to users, they will be asked to authenticate and next their identity is added with the specified permission to this wiki. The invitation URL contain a token that is only valid in 72 hours, but the access granted when the token is used is permanent.</p>
	
	<h3>Read access token</h3>
		<p><input type="text" style="width: 80%" value="<?php echo $this->data['tokenr']; ?>" /></p>

	<h3>Write access token</h3>	
		<p><input type="text" style="width: 80%" value="<?php echo $this->data['tokenw']; ?>" /></p>
	
	
	
	
	<h2 style="margin-top: 2em;">Custom access control</h2>
	
	<table class="list" style="width: 100%">
		<tr class="header">
			<td>Type</td>
			<td>Name</td>
			<td>Access</td>
			<td>Move</td>
			<td>Delete</td>
		</tr>

	<?php
	
	$accessmap = array(
		'0'	=> 'Deny',
		'1' => 'Read',
		'32' => 'Write'
	);
	
	foreach ($this->data['acl'] AS $key => $entry) {

		echo '<tr><td>' . ($entry[0][0] == '@' ? 'Group' : 'Person') . '</td>';
		echo '<td>' . 
			(array_key_exists($entry[0],$this->data['tgroups']) ? 
				$this->data['tgroups'][$entry[0]] : $entry[0]) . '</td>';
		echo '<td>' . $accessmap[$entry[1]] . '</td>';
		echo '<td>' .
			($key != 0 ? '<a href="?aclswap=' . ($key-1) .'">up</a>' : '');
		
		if ($key > 0 && $key + 1 < count($this->data['acl'])) echo ' | ';
		echo ($key + 1 < count($this->data['acl']) ? '<a href="?aclswap=' . ($key ) . '">down</a>' : '') .
			'</td><td><a href="?acldelete=' . $key . '">delete</a></td></tr>';
	
	}
	
	?>
	
	
	</table>
	
	

	
	<h3 style="margin-top: 2em;">Add access to a specific user</h3>
	
	<p>You can grant access to a list of specific users. To add access to a new user, please enter the person's Feide name:</p>
	<table><tr>
	<td><input type="text" name="addpersonid" value="" /></td>
	<td><select name="addpersonlevel">
		<option value="0">Deny access</option>
		<option value="1">Read access</option>
		<option selected="selected" value="32">Write access</option>
	</select></td>
	<td><input type="submit" name="addaccess" value="Add access to person" /></td>
	</tr></table>
	
	
	<h3 style="margin-top: 2em;">Add access to user group</h3>
	<p>You can grant access to group of people. The groups are predefined:</p>
	<table><tr>
	<td><select name="addgroupid">
		<option value="0">Select group...</option>
		<?php
			foreach ($this->data['tgroups'] AS $groupid => $grouptext) {
				echo '<option value="' . $groupid. '">' . $grouptext. '</option>';
			}
		?>
	</select></td>
	<td><select name="addgrouplevel">
		<option value="0">Deny access</option>
		<option value="1">Read access</option>
		<option value="32">Write access</option>
	</select></td>
	<td><input type="submit" name="addaccess" value="Add access to group" /></td>
	</tr></table>
	
	<h3>Complete</h3>
	
	<input type="submit" name="save" value="Save changes to wiki" />

</form>
	

			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>