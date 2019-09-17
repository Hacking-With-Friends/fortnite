<?php
// * IP block on accessing the admin functions.
/*
	$allowed_IP = ['77.80.235.37', '77.218.254.202'];

	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_IP)) {
		die('401');
	}
*/

	header('Content-type: text/javascript');
	require_once('./config.php');

	$db_user = $config["database"]["user"];
	$db_pass = $config["database"]["pass"];
	$db_db = $config["database"]["dbname"];
	$target_dir = "./uploads/";

	$placement_map = array(1 => 80, 2 => 60, 3 => 40, 4 => 20, 5 => 10);
	$kill_multiplier = 5;

	$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

	// Generally these won't be needed, because the landing page (scoreboard) sets em up.
	// But hey, livrem å hängslen!
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS fortnite_solo (id SERIAL, submitted TIMESTAMP NOT NULL DEFAULT NOW(), player_id VARCHAR(255) NOT NULL, placement INT NOT NULL, kills INT NOT NULL, score INT NOT NULL, screenshot VARCHAR(64) NOT NULL, screenshot_hash VARCHAR(64) NOT NULL, UNIQUE(player_id, screenshot));");
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS fortnite_duo (id SERIAL, submitted TIMESTAMP NOT NULL DEFAULT NOW(), player_one VARCHAR(255) NOT NULL, player_two VARCHAR(255) NOT NULL, placement INT NOT NULL, kills INT NOT NULL, score INT NOT NULL,  screenshot VARCHAR(64) NOT NULL, screenshot_hash VARCHAR(64) NOT NULL, UNIQUE(player_one, player_two, screenshot));");
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS bans (player_id VARCHAR(255));");

	// Solo
	if(isset($_GET['player_id']) && !isset($_GET['player_one']) && !isset($_GET['player_two'])) {

	// Duo
	} else {
		$result = pg_query($dbconn, 'SELECT * FROM fortnite_duo;');

		if($result) {
			if(pg_num_rows($result)) {
				?>
				<div class="entry header">
					<div class="row">
						<div class="id">DB ID</div>
						<div class="timestamp">Time</div>
						<div class="players">Players</div>
						<div class="placement">Placement</div>
						<div class="kills">Kills</div>
						<div class="score">Score</div>
						<div class="screenshotBtn">
							Click for Screenshot
						</div>
					</div>
				</div>
				<?php
				while ($row = pg_fetch_assoc($result)) {
					?>
					<div class="entry" onClick="expand(<?= $row["id"]; ?>);">
						<div class="row">
							<div class="id"><?= $row["id"]; ?></div>
							<div class="timestamp"><?= explode('.', $row['submitted'])[0]; ?></div>
							<div class="players"><?= $row['player_one'] . ', ' . $row['player_two'] ?></div>
							<div class="placement"><?= $row['placement'] ?></div>
							<div class="kills"><?= $row['kills'] ?></div>
							<div class="score"><?= $row['score'] ?></div>
							<div class="screenshotBtn">
								Click for Screenshot
							</div>
						</div>
						<div class="row">
							<div class="screenshot" id="screenshot_<?= $row["id"]; ?>" style="background-image: url('./uploads/<?= $row["screenshot"]?>');">
							</div>
						</div>
					</div>
					<?php
				}
			}
		} else {
			die(json_encode(array('result' => 'failed', 'title' => 'Database error', 'message' => pg_result_error($result))));
		}
	}
?>