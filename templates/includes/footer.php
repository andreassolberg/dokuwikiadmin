
</div>    <!-- /#content -->

<div id="footer">
	<?php
	
	if ($this->data['user']) {
	
		if (isset($this->data['user']->userid)) {
			echo $this->t('authtext', 
				array(
					'%DISPLAYNAME%' => $this->data['user']->name, 
					'%USERID%' => $this->data['user']->userid
				) 
			); 
		}

		
	} else {
		echo($this->t('is_anonymous'));
	}




	
	?>

	<!-- <br /><?php echo $this->t('visit'); ?> <a href="http://rnd.feide.no">rnd.feide.no</a> -->
</div><!-- /#footer -->



</body>
</html>
