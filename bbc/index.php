<?php

	require_once('../functions.php');

	$rss_tags = array(  
	'title',  
	'link',  
	'guid',    
	'description',  
	'pubDate',    
	);  
	$rss_item_tag = 'item';  
	$rss_url = 'http://news.bbc.co.uk/rss/on_this_day/front_page/rss.xml';

	$rssfeed = rss_to_array($rss_item_tag, $rss_tags, $rss_url);

	echo '<pre>';  
	print_r($rssfeed);

	function rss_to_array($tag, $array, $url) {  
	  $doc = new DOMdocument();  
	  $doc->load($url);  
	  $rss_array = array();  
	  $items = array();  
	  foreach($doc-> getElementsByTagName($tag) AS $node) {  
	    foreach($array AS $key => $value) {  
	      $items[$value] = $node->getElementsByTagName($value)->item(0)->nodeValue;  
	    }  
	    array_push($rss_array, $items);  
	  }  
	  return $rss_array;  
	}

?>