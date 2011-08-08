<?php 
	$this->includeAtTemplateBase('includes/header.php'); 


?>

	<h1><?php if (isset($this->data['header'])) { echo $this->data['header']; } else { echo "Some error occured"; } ?></h1>

	<p>You requested to be granted access to <strong><?php echo $this->data['identifier']; ?></strong>.</p>
	
	<p><?php echo $this->data['body']; ?></p>
	
	
	
	<p>[ <a href="https://ow.feide.no/<?php echo $this->data['identifier']; ?>:start">Visit the <strong><?php echo $this->data['identifier']; ?></strong> wiki</a> ] </p>
	
	<p>Name: <?php echo $this->data['name']; ?></p>
	<p>Description: <?php echo $this->data['descr']; ?></p>
	

			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>