<?php

	require('../functions.php');
	
	ETag(); // Output the ETag
	charset(); // Output the charset header, as here: http://remote.bergcloud.com/developers/reference/edition#charsets

	$items = onThisDay('wikipedia', ''); // Grab the items
	

	// -- Output HTML -- //

	makeHeader(); // Generate the header HTML

	makeAnnouncement(); // Generate announcemnet, if there is one

	html($items); // Generate the HTML for the items

	makeFooter('wikipedia'); // Generate the footer HTML

?>