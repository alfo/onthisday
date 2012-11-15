<?php

	require('../functions.php');
	
	ETag(); // Output the ETag

	$items = onThisDay('wikipedia'); // Grab the items
	

	// -- Output HTML -- //

	makeHeader(); // Generate the header HTML

	html($items); // Generate the HTML for the items

	makeFooter('wikipedia'); // Generate the footer HTML

?>