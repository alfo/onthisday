<?php

	require('../functions.php');

	$items = onThisDay('bbc'); // Grab the items from Wikipedia
	

	// -- Output HTML -- //
	
	makeHeader(); // Generate the header HTML

	html($items); // Generate the HTML for the items

	makeFooter(); // Generate the footer HTML

?>