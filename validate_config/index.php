<?php

	require_once('../functions.php');

	if (isset($_POST['config'])) {

		$post = json_decode($_POST['config'], true);

		if (isset($post['time_preference'])) {

			if ($post['time_preference'] == 'new' || $post['time_preference'] == 'old') {
				yay();
			} else {
				fail(2, $post['time_preference']);
			} 

		} else fail(1, 0);

	} else header('HTTP/1.0 400 Bad Request', true, 400);

	function yay() {
		$return = array('valid' => 'true');
		$die = json_encode($return);
		die($die);
	}

	function fail($type, $post) {
		if ($type == 1)
			$return = array('valid' => 'false', 'errors' => array('No "time_preference" value passed.'));
		elseif ($type == 2)
			$return = array('valid' => 'false', 'errors' => array('"time_preference" value invalid. Supplied was '.$post.', should be new or old.'));
		header('HTTP/1.0 400 Bad Request', true, 400);
		$die = json_encode($return);
		die($die);
	}

?>