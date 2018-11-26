<?php
	$allowed_IP = ['77.80.235.37', '77.218.254.202'];

	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_IP)) {
		die('401');
	}

	print '<pre>';	
	$db_user = null;
	$db_pass = null;
	$db_db = null;

	$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

	/*$result = pg_query($dbconn, 'SELECT * FROM fortnite_duo;');

	$screenshots = array();
	if($result) {
		//print json_encode(array('result' => 'success'));
		while ($row = pg_fetch_assoc($result)) {
			$hash = md5($row['screenshot']);
			if(isset($screenshots[$hash])) {
				ob_end_flush();
				print $row['id'] . ' - ' . $row['player_one'] . ', ' . $row['player_two'] . ': ' . $row['score'] . ' ('.$row['kills'].':'.$row['placement'].') vs ' . $screenshots[$hash]['id'] . ' - ' . $screenshots[$hash]['player_one'] . ', ' . $screenshots[$hash]['player_two'] . ': ' . $screenshots[$hash]['score'] . ' ('.$screenshots[$hash]['kills'].':'.$screenshots[$hash]['placement'].')<br>';
				ob_start();
			} else
				$screenshots[$hash] = array('id' => $row['id'], 'player_one' => $row['player_one'], 'player_two' => $row['player_two'], 'score' => $row['score'], 'kills' => $row['kills'], 'placement' => $row['placement']);
			//print json_encode($row);
		}
	} else {
		print json_encode(array('result' => 'failed', 'reason' => '...'));
	}*/

	$result = pg_query($dbconn, 'SELECT * FROM fortnite_solo;');

	$screenshots = array();
	if($result) {
		//print json_encode(array('result' => 'success'));
		while ($row = pg_fetch_assoc($result)) {
			$hash = md5($row['screenshot']);
			if(isset($screenshots[$hash])) {
				ob_end_flush();
				print $row['id'] . ' - ' . $row['player_id'] . ': ' . $row['score'] . ' ('.$row['kills'].':'.$row['placement'].') vs ' . $screenshots[$hash]['id'] . ' - ' . $screenshots[$hash]['player_id'] . ': ' . $screenshots[$hash]['score'] . ' ('.$screenshots[$hash]['kills'].':'.$screenshots[$hash]['placement'].')<br>';
				ob_start();
			} else
				$screenshots[$hash] = array('id' => $row['id'], 'player_id' => $row['player_id'], 'score' => $row['score'], 'kills' => $row['kills'], 'placement' => $row['placement']);
			//print json_encode($row);
		}
	} else {
		print json_encode(array('result' => 'failed', 'reason' => '...'));
	}
?>