<?php

	require('../functions.php');
	
	header("Etag: ". ETag()); // Output the ETag

	$items = onThisDay($_GET['source']); // Grab the items
	

	// -- Output HTML -- //

	makeHeader(); // Generate the header HTML

	html($items); // Generate the HTML for the items

	makeFooter(); // Generate the footer HTML

?>