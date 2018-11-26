<?php
	//$allowed_IP = ['77.80.235.37', '77.218.254.202'];

	//if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_IP)) {
	//	die('401');
	//}

	header('Content-type: text/javascript');
	
	$data = json_decode(file_get_contents('php://input'), true);
	$db_user = null;
	$db_pass = null;
	$db_db = null;
	$placement_map = array(1 => 80, 2 => 60, 3 => 40, 4 => 20, 5 => 10);
	$kill_multiplier = 5;

	$player_id = pg_escape_string($data['player_id']);
	$placement = pg_escape_string($data['placement']);
	$kills = pg_escape_string($data['kills']);
	$screenshot = pg_escape_string($data['screenshot']);
	$score = 0;
	if(isset($placement_map[$placement]))
		$score += $placement_map[$placement];
	$score += $kill_multiplier * $kills;

	$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

	$tmp = explode(',', $player_id);
	$players = array();
	foreach($tmp as $player) {
		$players[] = trim($player);
	}

	if(sizeof($players) == 1) {
		print json_encode(array('result' => 'failed', 'reason' => 'Solo results are locked in! No more scores are allowed.'));
		die();
		// Solo
		$result = pg_query($dbconn, "SELECT id FROM fortnite_solo WHERE screenshot='".$screenshot."';");
		if($result) {
			while ($row = pg_fetch_assoc($result)) {
				print json_encode(array('result' => 'failed', 'reason' => 'Screenshot already uploaded once in solo.'));
				die();
			}
		}

		$result = pg_prepare($dbconn, "my_query", 'INSERT INTO fortnite_solo (player_id, placement, kills, score, screenshot) VALUES ($1, $2, $3, $4, $5);');
		$result = pg_execute($dbconn, "my_query", array($players[0], $placement, $kills, $score, $screenshot));
	} else {
		// Duo
		print json_encode(array('result' => 'failed', 'reason' => 'Duo results are locked in! No more scores are allowed.'));
		die();
		$result = pg_query($dbconn, "SELECT id FROM fortnite_duo WHERE screenshot='".$screenshot."';");
		if($result) {
			while ($row = pg_fetch_assoc($result)) {
				print json_encode(array('result' => 'failed', 'reason' => 'Screenshot already uploaded once in duo.'));
				die();
			}
		}
		sort($players);
		$result = pg_prepare($dbconn, "my_query", 'INSERT INTO fortnite_duo (player_one, player_two, placement, kills, score, screenshot) VALUES ($1, $2, $3, $4, $5, $6);');
		$result = pg_execute($dbconn, "my_query", array($players[0], $players[1], $placement, $kills, $score, $screenshot));
	}

	error_log("Player $player_id got $kills kills at placement $placement.");
	print json_encode(array('result' => 'success'));
?>