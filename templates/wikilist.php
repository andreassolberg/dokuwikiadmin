<?php 
	$this->includeAtTemplateBase('includes/header.php'); 
	

?>



<div id="wikitabs"> 
	
	<ul style=" margin: 0px"> 
        <li><a href="#yourwiki"><span>Your wikis</span></a></li> 
        <li><a href="#otherwiki"><span>Wiki you can access</span></a></li> 
        <li><a href="#publicwiki"><span>Public wikis</span></a></li> 
        <li><a href="#createnewwiki"><span>Create new wiki</span></a></li> 
    </ul>




	<div id="yourwiki">
		
		<h1>Your wikis</h1>

		<p>You are authenticated as <span style="color: #833"><?php echo $this->data['user']; ?></span>, and these are wikis that you have access to administer:</p>



		<?php

			foreach ($this->data['listprivate'] AS $wiki) {

				echo '<div class="wikientry">';

				echo '<h3>' . $wiki->getName() . '</h3>';
				echo '<p>' . $wiki->getDescr() . '</p>';

				echo '<p><img class="linkicon" src="resources/web-link.png" /><a href="http://ow.feide.no/' . $wiki->getIdentifier() . ':start">Go to ' . $wiki->getName() . '</a> ';
				echo '<img class="linkicon" src="resources/settings.png" /><a href="edit.php?edit=' . $wiki->getIdentifier() . '">Administer ' . $wiki->getName() . '</a></p>';


				echo '</div>';
			}


		?>
		
		
		
	</div>

	<div id="otherwiki">
		
		<h1>More wikis that you have write access to</h1>
		
		<?php

			foreach ($this->data['listall'] AS $wiki) {

				#print_r($this->data['groups']);
				
#				if ($wiki->getOwner() === $this->data['user']) continue;

				$acl = $wiki->getCustomACL();
				$access = FALSE;
				foreach($acl AS $a) {
					if ($a[1] == 32) {
						#if ($a[0][0] == '@')
						#	echo 'checking ' . substr($a[0], 1);
						#print_r($a[0]);
						
						
						
						if (in_array(substr($a[0], 1), $this->data['groups'])) {
							$access = TRUE;

						}
						
						if ($a[0] == $this->data['user']) $access = TRUE;
					}
				}
				if (!$access) continue;

				
				echo '<div class="wikientry">';

				echo '<h3>' . $wiki->getName() . '</h3>';
				echo '<p>' . $wiki->getDescr() . '</p>';

				echo '<p><img class="linkicon" src="resources/web-link.png" /><a href="http://ow.feide.no/' . $wiki->getIdentifier() . ':start">Go to ' . $wiki->getName() . '</a></p>';

				// echo '<pre>';
				// print_r($wiki->getCustomACL());
				// echo '</pre>';
				
				echo '</div>';
			}


		?>

		
	</div>

	<div id="publicwiki">
		
		
		
		<h1>Public wikis</h1>

		<p>Public wikis is wikis that are accessible for all authenticated users, or even anonymous users. </p>

		<?php



			foreach ($this->data['listpublic'] AS $wiki) {

				echo '<div class="wikientry">';

				echo '<h3>' . $wiki->getName() . '</h3>';
				echo '<p>' . $wiki->getDescr() . '</p>';

				echo '<p><img class="linkicon" src="resources/web-link.png" /><a href="http://ow.feide.no/' . $wiki->getIdentifier() . ':start">Go to ' . $wiki->getName() . '</a> ';
		#		echo '<img src="resources/settings.png" /><a href="edit.php?edit=' . $wiki->getIdentifier() . '">Administer ' . $wiki->getName() . '</a></p>';

				echo '</div>';
			}

		?>

		
		
	</div>

	<div id="createnewwiki">
		
		<div id="newwiki">

			<img src="resources/dokuwiki.png" style="float: right; border: none" />
			<h1 style="margin-top: 2px" >Create a new wiki</h1>

			<form method="post" action="edit.php">
				<input type="hidden" name="createnew" value="1" />



				<p>The first thing you need to do to create a wiki is to select a wiki identifier, a machine readable name of the wiki. Examples of wiki identifiers: andreas, simplesamlphp, fasintegrasjon, intmask, foo. Please make the wiki identifier descriptive of the wiki content. You can not change the wiki identifier later.</p>


				<p>Important: <i>The wiki identifier must consist of one or more lowercase characters [a-z]. Maximum 15 characters.</i></p>

				<p>Wiki identifier: 
				<input type="text" name="edit" value="" /></p>



				<input type="submit" name="createnewsubmit" value="Create new wiki" />

			</form>
		</div>
		
	</div>





</div>




	
	




	


	



			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>