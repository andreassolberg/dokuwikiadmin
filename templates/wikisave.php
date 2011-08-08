<?php 
	$this->includeAtTemplateBase('includes/header.php'); 


?>

	<h1><?php if (isset($this->data['header'])) { echo $this->data['header']; } else { echo "Some error occured"; } ?></h1>

	<p>Your wiki <strong><?php echo $this->data['identifier']; ?></strong> is now successfully saved.</p>
	
	<p>[ <a href="index.php">List all wikis</a> | <a href="https://ow.feide.no/<?php echo $this->data['identifier']; ?>:start">Visit your <strong><?php echo $this->data['identifier']; ?></strong> wiki</a> ] </p>
	
	<p>Name: <?php echo $this->data['name']; ?></p>
	<p>Description: <?php echo $this->data['descr']; ?></p>
	

			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>