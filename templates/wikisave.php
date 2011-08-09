<?php 
	$this->includeAtTemplateBase('includes/header.php'); 


?>

	<h1><?php if (isset($this->data['header'])) { echo $this->data['header']; } else { echo "Some error occured"; } ?></h1>

	<p>Your wiki <strong><?php echo htmlspecialchars($this->data['identifier']); ?></strong> is now successfully saved.</p>
	
	<p>[ <a href="index.php">List all wikis</a> | <a href="https://openwiki.uninett.no/<?php echo $this->data['identifier']; ?>:start">Visit your <strong><?php echo htmlspecialchars($this->data['identifier']); ?></strong> wiki</a> ] </p>
	
	<p>Name: <?php echo htmlspecialchars($this->data['name']); ?></p>
	<p>Description: <?php echo htmlspecialchars($this->data['descr']); ?></p>
	

			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>