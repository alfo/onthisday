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

	// This function will generate an ETag, which will be unique for each day
	function ETag() {
		// Get today's date
		$date = date('d-m-Y');
		// md5 it
		header("ETag: ".md5($date));
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
	function onThisDay($source) {

		if ($source == 'wikipedia') {

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

			// -- This section could also be trimmed down -- //

			// Remove the last three objects, which are always not actual information
			unset($output[count($output)-1]);
			unset($output[count($output)-1]);
			unset($output[count($output)-1]);

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
				$add['info'] = $info; // Add the info
				$return[] = $add; // Add the $add to the $return array
			}

		} else { // Using the BBC as source instead

			$doc = phpQuery::newDocumentHTML(grab('http://news.bbc.co.uk/onthisday/default.stm'));

			$items = $doc->find('div.bodytext')->text();

			// Trim the whitespace around the items
			$items = trim(removeEmptyLines($items));

			// Split $items into an $output array based on new lines
			foreach(preg_split("/(\r?\n)/", $items) as $line){
				$output[] = substr($line, 1);
			}

			// Cycle through the array of raw data and extract only the articles
			$i = 0;

			while ($i <= 8) {
				$articles[] = $output[$i];
				$i++;
			}

			// This isn't nice, but it works
			$extracted[0]['title'] = $articles[0];
			$extracted[0]['description'] = $articles[1];
			$extracted[1]['title'] = $articles[2];
			$extracted[1]['description'] = $articles[3];
			$extracted[2]['title'] = $articles[4];
			$extracted[2]['description'] = $articles[5];
			$extracted[2]['title'] = $articles[6];
			$extracted[2]['description'] = $articles[7];

			// Initialize the $return array
			$return = array();

			// Now to format the info

			foreach ($extracted as $item) {
				$year_bits = explode(': ', $item['title']);
				$year = $year_bits[0];
				// A quick way to fix a missing 1 on the front of years.
				// As the BBC do not report on non-currnet affairs, it is safe to say that this will not cause any mishaps
				if ($year < 1000) $year = "1".$year;
				$info = $item['description'];
				$add = array();
				$add['year'] = $year;
				$add['info'] = $info;
				$return[] = $add;
			}

		}

		usort($return, "sortByYear");

		$return = array_slice($return, -3, 3);

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
