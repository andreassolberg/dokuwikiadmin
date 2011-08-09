<?php 
	$this->includeAtTemplateBase('includes/header.php'); 


?>

	<h1><?php if (isset($this->data['header'])) { echo $this->data['header']; } else { echo "Some error occured"; } ?></h1>

	<p>You requested to be granted access to <strong><?php echo htmlspecialchars($this->data['identifier']); ?></strong>.</p>
	
	<p><?php echo $this->data['body']; ?></p>
	
	
	
	<p>[ <a href="https://openwiki.uninett.no/<?php echo htmlspecialchars($this->data['identifier']); ?>:start">Visit the <strong><?php echo htmlspecialchars($this->data['identifier']); ?></strong> wiki</a> ] </p>
	
	<p>Name: <?php echo htmlspecialchars($this->data['name']); ?></p>
	<p>Description: <?php echo htmlspecialchars($this->data['descr']); ?></p>
	

			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>