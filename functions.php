<?php

	// Include phpQuery, the engine we use to extract the content from Wikipedia
	include 'phpQuery.php';

	// Make the header by including it 
	function makeHeader() {
		include '../header.php'; // relative URL from /edition and /sample
	}

	// Make the footer by including it
	function makeFooter() {
		include '../footer.php';
	}

	// This function will generate an ETag, which will be unique for each day
	function ETag() {
		// Get today's date
		$date = date('d-m-Y');
		// md5 it
		return md5($date);
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

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://en.wikipedia.org/w/api.php?action=featuredfeed&feed=onthisday&feedformat=rss');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, '3');
			curl_setopt($ch, CURLOPT_USERAGENT, 'On This Day for Little Printer by Alex Forey');
			$contents = trim(curl_exec($ch)); // Get the contents from Wikipedia
			curl_close($ch);

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
				$output[] = substr($line, 1);
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

			// Which bits of info do we need?
			$rss_tags = array('title', 'link', 'guid', 'description', 'pubDate');

			// Initialize a DOM Document for Parsing
			$doc = new DOMdocument();

			// Load the contents of the feed
			$contents = grab('http://news.bbc.co.uk/rss/on_this_day/front_page/rss.xml');

			// Load the contents into the object
			$doc->loadXML($contents);
	
			// Initialize some arrays
			$rss_array = array();
			$items = array();
	
			// Cycle through each item, extract the info and add it to a big array
			foreach($doc-> getElementsByTagName('item') as $node) {
				foreach($rss_tags as $key => $value) {
					$items[$value] = $node->getElementsByTagName($value)->item(0)->nodeValue;
				}
				array_push($rss_array, $items);
			}

			// Initialize the $return array
			$return = array();

			// Now to format the info

			foreach ($rss_array as $item) {
				$year_bits = explode(': ', $item['title']);
				$year = $year_bits[0];
				$info = $item['description'];
				$add = array();
				$add['year'] = $year;
				$add['info'] = $info;
				$return[] = $add;
			}

		}

		// Return!
		return $return;

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
	    $data = curl_exec($ch);
	    curl_close($ch);
	    return $data;
	}
?>