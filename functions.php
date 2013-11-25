<?php

	// Some installations of PHP will complain if this isn't done:
	date_default_timezone_set('UTC');
	
	// Include phpQuery, the engine we use to extract the content from Wikipedia
	include 'phpQuery.php';

	// Make the header by including it 
	function makeHeader() {
		include '../header.php'; // relative URL from /edition and /sample
	}

	// Make the footer by including it
	function makeFooter($source) {
		if ($source == 'bbc') $footerSource = 'The BBC';
		elseif ($source == 'wikipedia') $footerSource = 'Wikipedia';
		include '../footer.php';
	}

	function makeAnnouncement() {
		$announcements = json_decode(file_get_contents('../announcements.json'), true);
		$announcement = '';
		foreach ($announcements as $a) {
			if (isToday($a['date'])) {
				$announcement = $a;
			}
		}

		if ($announcement)
			include '../announcement.php';
	}

	function isToday($timestamp) {

		$today = date('Ymd');
		$announcementDay = date('Ymd', strtotime($timestamp));

		if ($today == $announcementDay)
			return true;
		else
			return false;
	}

	// This function will generate an ETag, which will be unique for each day
	function ETag() {
		// Get today's date
		$date = date('d-m-Y');
		// md5 it
		header("ETag: " . md5($date));
	}

	function charset() {
		header("Content-Type: text/html; charset=utf-8");
	}

	// A simple function to remove the empty lines from the content returned by Wikipedia
	function removeEmptyLines($string) { return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string); }

	function xmlToArray($input) {
		// -- This bit could do with some cleaning up, but it works for now -- //
		$xml = simplexml_load_string($input); // Load the XML returned into an object
		$json = json_encode($xml); // Convert the object to JSON
		$array = json_decode($json, true); // Convert the JSON into an array
		return $array;
	}

	// The main function. This obtains the information on this day
	function onThisDay($newOrOld, $number) {

		$contents = trim(grab('http://en.wikipedia.org/w/api.php?action=featuredfeed&feed=onthisday&feedformat=rss'));
		
		$array = xmlToArray($contents);
		
		// Count the number of days
		$noOfDays = count($array['channel']['item']);
		// Get the Key of today
		$today = $noOfDays - 1;
		// Get the description of today's entry
		$input = $array['channel']['item'][$today]['description'];
		
		// The contents of 'description' now contains a HTML unordered list

		// Initialize phpQuery to deal with the parsing
		$doc = phpQuery::newDocumentHTML($input);

		// Produce an array of the contents of the list items
		$items = $doc->find('li')->text();

		// Trim the whitespace around the items
		$items = trim(removeEmptyLines($items));
		
		// Now $items is a string with each item on a new line
		
		// Split $items into an $output array based on new lines
		foreach(preg_split("/(\r?\n)/", $items) as $line){
			$output[] = trim($line);
		}

		// Now we have a nice array with maybe three or four items in it that look like this:
		// "1843 – Something awesome happened"

		// We're now going to separate it into a multidimensional array that looks like this:
		/*

		Item 1
			- Year
			- Info
		Item 2
			- Year
			- Info

		*/

		$return = array(); // Prepare an array for outputting

		foreach ($output as $item) {
			$bits = explode(" – ", $item); // Separate the string into year and info
			$year = trim($bits[0]); // Set $year as the first part 
			unset($bits[0]); // Stop the year turning up twice
			$info = implode(" – ", $bits); // Set $info as the rest of the parts
			$add = array(); // Prepare an array for adding
			$add['year'] = $year; // Add the year
			
			// Remove (pictured) from the text, as no pictures are printed
			$pictured = " (pictured)";
			$info = str_replace($pictured, "", $info);
			
			$add['info'] = $info; // Add the info
			$return[] = $add; // Add the $add to the $return array
		}

		if ($newOrOld == 'old') {
			$start = 0;
		} else {
			$start = -$number;
		}

		// Sort all the stories by year
		usort($return, "sortByYear");

		$return = array_slice($return, $start, $number, true);

		// Return!
		return $return;

	}

	function sortByYear($a, $b) {
		return $a['year'] - $b['year'];
	}

	// This function takes in an $items array, and outputs some HTML
	function html($items) {

		// Start with a simple loop of each item
		foreach ($items as $item) {
			echo '<div class="item">';
			echo '	<h4>' , $item['year'] , '</h4>';
			echo '	<p>' , $item['info'] , '</p>';
			echo '</div>';
		}
	}

	function grab($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_USERAGENT, 'On This Day for Little Printer by Alex Forey');
	    $data = curl_exec($ch);
	    curl_close($ch);
	    return $data;
	}
	
?>
