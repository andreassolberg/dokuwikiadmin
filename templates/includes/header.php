<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head xml:lang="en">

	<meta charset="utf-8" />

	<!-- Foodle: CSS -->	
	<link rel="stylesheet" media="screen" type="text/css" href="/res/css/foodle.css" /> 
	<link rel="stylesheet" media="screen" type="text/css" href="/res/css/foodle-layout.css" /> 
	<link rel="stylesheet" media="screen" type="text/css" href="/res/css/openwiki.css" /> 

	<!-- JQuery -->

	<script type="text/javascript" src="/res/js/jquery.js"></script>
	<script type="text/javascript" src="/res/js/jquery-ui.js"></script>
	<link rel="stylesheet" media="screen" type="text/css" href="/res/js/uitheme/jquery-ui-themeroller.css" />
	


	<!-- Foodle: JS -->	
	<!-- <script type="text/javascript" src="/res/js/foodle.js"></script>	 -->

	<script type="text/javascript">
		$(document).ready(function() {
			$("#wikitabs").tabs();
		}) 
	</script>


	<title>OpenWiki</title> 

	
</head>
<body>

<!-- Red logo header -->
<div id="header">	
	<div id="logo">OpenWiki <span id="version">Version 1.0</span> 
	</div><!-- end #logo -->
	<a href="http://rnd.feide.no"><img id="ulogo" alt="notes" src="/res/uninettlogo.gif" /></a>
</div><!-- end #header -->

























<!-- Grey header bar below -->
<div id="headerbar" style="clear: both">
<?php 

echo '<p id="breadcrumb">';
if (isset($this->data['bread'])) {
	$first = TRUE;
	foreach ($this->data['bread'] AS $item) {
		if (!$first) echo ' » ';		
		if (isset($item['href'])) {
			
			if (strstr($item['title'],'bc_') == $item['title'] ) {
				echo '<a href="' . $item['href'] . '">' . $this->t($item['title']) . '</a>';
			} else {
				echo '<a href="' . $item['href'] . '">' . $item['title'] . '</a>';
			}
		} else {
			if (strstr($item['title'],'bc_') == $item['title'] ) {
				echo $this->t($item['title']);
			} else {
				echo $item['title'];
			}
			
		}
		$first = FALSE;
	}
}
echo '</p>';




	if (isset($this->data['headbar'])) {
		echo $this->data['headbar'];
	}
?>

<br style="height: 0px; clear: both" />
</div><!-- /#headerbar -->

  




<?php
$languages = $this->getLanguageList();
$langnames = array(
	'no' => 'Bokmål',
	'nn' => 'Nynorsk',
	'se' => 'Sami',
	'da' => 'Dansk',
	'fi' => 'Suomeksi',
	'en' => 'English',
	'de' => 'Deutsch',
	'sv' => 'Svenska',
	'es' => 'Español',
	'fr' => 'Français',
	'nl' => 'Nederlands',
	'lb' => 'Luxembourgish', 
	'sl' => 'Slovenščina', // Slovensk
	'hr' => 'Hrvatski', // Croatian
	'hu' => 'Magyar', // Hungarian
);



echo '<div id="langbar" style="clar: both">';
if (empty($_POST) ) {
	$textarray = array();

/*
	foreach ($languages AS $lang => $current) {

		if ($current) {
			$textarray[] = '<form class="button" method="get" action="' . htmlspecialchars(SimpleSAML_Utilities::addURLparameter(SimpleSAML_Utilities::selfURL(), 'language=' . $lang)) . '"><div class="no"><input type="submit" value="[' . 
				$langnames[$lang] . ']" class="button" /></div></form>';
		} else {
			$textarray[] = '<form class="button" method="get" action="' . htmlspecialchars(SimpleSAML_Utilities::addURLparameter(SimpleSAML_Utilities::selfURL(), 'language=' . $lang)) . '"><div class="no"><input type="submit" value="' . 
				$langnames[$lang] . '" class="button" /></div></form>';
		}
	}
	*/

	foreach ($languages AS $lang => $current) {
		if ($current) {
			$textarray[] = $langnames[$lang];
		} else {
			$textarray[] = '<a href="' . htmlspecialchars(
				SimpleSAML_Utilities::addURLparameter(
						SimpleSAML_Utilities::selfURL(), array(
							'language' => $lang,
						))) . '">' . 
				$langnames[$lang] . '</a>';
		}
	}
	echo '' .  join(' | ', $textarray) . '';

	

}
echo '</div><!-- end #langbar -->';
?>










<div id="content">
