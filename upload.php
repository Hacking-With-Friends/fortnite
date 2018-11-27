<?php
	//$allowed_IP = ['77.80.235.37', '77.218.254.202'];

	//if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_IP)) {
	//	die('401');
	//}

	header('Content-type: text/javascript');

	$__LOCKED = array('solo' => false, 'duo' => false);
	$db_user = "esport";
	$db_pass = null;
	$db_db = "esport";
	$target_dir = "./uploads/";

	$placement_map = array(1 => 80, 2 => 60, 3 => 40, 4 => 20, 5 => 10);
	$kill_multiplier = 5;

	$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

	// Generally these won't be needed, because the landing page (scoreboard) sets em up.
	// But hey, livrem å hängslen!
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS fortnite_solo (id SERIAL, player_id VARCHAR(255) NOT NULL, placement INT NOT NULL, kills INT NOT NULL, score INT NOT NULL, screenshot VARCHAR(64) NOT NULL, UNIQUE(player_id, screenshot));");
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS fortnite_duo (id SERIAL, player_one VARCHAR(255) NOT NULL, player_two VARCHAR(255) NOT NULL, placement INT NOT NULL, kills INT NOT NULL, score INT NOT NULL, screenshot VARCHAR(64) NOT NULL, UNIQUE(player_one, player_two, screenshot));");
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS bans (player_id VARCHAR(255));");

	function gen_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xfff ) | 0x4000,
			mt_rand( 0, 0x3ff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}

	// Make sure the upload directory exists.
	// If this fails, well a clever admin surely gotta check the error log and fix it.. right?
	if (!file_exists($target_dir)) {
		mkdir($target_dir, 0777, true);
	}

	if(isset($_FILES['screenshot']) && filesize($_FILES["screenshot"]["tmp_name"]) > 0) {
		$hash = hash_file("sha256", $_FILES["screenshot"]["tmp_name"]);
		$file_id = gen_uuid();
		$extension = pathinfo($_FILES["screenshot"]["name"], PATHINFO_EXTENSION);
		$final_destination = $target_dir . $file_id .'.'. $extension;
	} else {
		die(json_encode(array('result' => 'failed', 'reason' => 'No screenshot was attached.')));
	}

	while (file_exists($final_destination)) {
		$file_id = gen_uuid();
		$final_destination = $target_dir . $file_id .'.'. $extension;
	}

	$placement = pg_escape_string($_POST['placement']);
	$kills = pg_escape_string($_POST['kills']);

	$score = 0;
	if(isset($placement_map[$placement]))
		$score += $placement_map[$placement];
	$score += $kill_multiplier * $kills;

	// Solo
	if(isset($_POST['player_id']) && !isset($_POST['player_one']) && !isset($_POST['player_two'])) {
		if($__LOCKED['solo']) {
			die(json_encode(array('result' => 'failed', 'reason' => 'Solo results are locked in! No more scores are allowed.')));
		}
		
		$result = pg_prepare($dbconn, "find_screenshot", 'SELECT id FROM fortnite_solo WHERE screenshot=$1;');
		$result = pg_execute($dbconn, "find_screenshot", array($hash));
	
		if($result) {
			if(pg_num_rows($result)) {
				while ($row = pg_fetch_assoc($result)) {
					die(json_encode(array('result' => 'failed', 'title' => 'Registration failed', 'message' => 'Screenshot already uploaded once in solo game.')));
				}
			}
		} else {
			die(json_encode(array('result' => 'failed', 'title' => 'Database error', 'message' => pg_last_error($dbconn))));
		}

		$result = pg_prepare($dbconn, "update_solo", 'INSERT INTO fortnite_solo (player_id, placement, kills, score, screenshot) VALUES ($1, $2, $3, $4, $5);');
		$result = pg_execute($dbconn, "update_solo", array($_POST['player_id'], $placement, $kills, $score, $hash));
	
		error_log("Player $player_id got $kills kills at placement $placement.");

	// Duo
	} else {
		if($__LOCKED['duo']) {
			die(json_encode(array('result' => 'failed', 'reason' => 'Duo results are locked in! No more scores are allowed.')));
		}
		
		$result = pg_prepare($dbconn, "find_screenshot", 'SELECT id FROM fortnite_duo WHERE screenshot=$1;');
		$result = pg_execute($dbconn, "find_screenshot", array($hash));

		if($result) {
			if(pg_num_rows($result)) {
				while ($row = pg_fetch_assoc($result)) {
					die(json_encode(array('result' => 'failed', 'reason' => 'Screenshot already uploaded once in duo.')));
				}
			}
		} else {
			die(json_encode(array('result' => 'failed', 'title' => 'Database error', 'message' => pg_result_error($result))));
		}

		$players = array($_POST['player_one'], $_POST['player_two']);
		sort($players);

		$result = pg_prepare($dbconn, "update_duo", 'INSERT INTO fortnite_duo (player_one, player_two, placement, kills, score, screenshot) VALUES ($1, $2, $3, $4, $5, $6);');
		$result = pg_execute($dbconn, "update_duo", array($players[0], $players[1], $placement, $kills, $score, $hash));

		error_log("Playes " .$players[0]. " & " .$players[1]. " got $kills kills at placement $placement.");
	}

	print json_encode(array('result' => 'success', "title" => "Registration successful", "message" => "Your score has been counted"));
?>