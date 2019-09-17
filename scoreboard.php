<?php
	require_once('./config.php');

	$db_user = $config["database"]["user"];
	$db_pass = $config["database"]["pass"];
	$db_db = $config["database"]["dbname"];

	date_default_timezone_set($config['timezone']);

	$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");
	if (!$dbconn) {
		die("Could not connect to database");
	}

	// Create tables if they haven't already been created.
	// A wise person once said, let there be a class for reused objects...
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS fortnite_solo (id SERIAL, submitted TIMESTAMP NOT NULL DEFAULT NOW(), player_id VARCHAR(255) NOT NULL, placement INT NOT NULL, kills INT NOT NULL, score INT NOT NULL, screenshot VARCHAR(64) NOT NULL, UNIQUE(player_id, screenshot));");
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS fortnite_duo (id SERIAL, submitted TIMESTAMP NOT NULL DEFAULT NOW(), player_one VARCHAR(255) NOT NULL, player_two VARCHAR(255) NOT NULL, placement INT NOT NULL, kills INT NOT NULL, score INT NOT NULL, screenshot VARCHAR(64) NOT NULL, UNIQUE(player_one, player_two, screenshot));");
	pg_query($dbconn, "CREATE TABLE IF NOT EXISTS bans (player_id VARCHAR(255));");

	$solo_results = pg_query($dbconn, "SELECT player_id, placement, kills, score FROM fortnite_solo WHERE player_id NOT IN (SELECT player_id FROM bans) ORDER BY score DESC;");
	$team_results = pg_query($dbconn, "SELECT player_one, player_two, placement, kills, score FROM fortnite_duo WHERE player_one NOT IN (SELECT player_id FROM bans) AND player_two NOT IN (SELECT player_id FROM bans) ORDER BY score DESC;");

	// The pure scoreboard is at the bottom
	// This big chunk of HTML is used for the "TV-mode"
	if(isset($_GET['mode']) && $_GET['mode'] == 'tv') {
?>
<html>
	<head>
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:site" content="@hexlify" />
		<meta name="twitter:creator" content="@hexlify" />
		<meta name="twitter:creator" content="tamm" />
		<meta property="og:url" content="https://fnite.se/" />
		<meta property="og:title" content="DHS18 Fortnite BYOC" />
		<meta property="og:description" content="Dreamhack Summer 2018 - Fortnite BYOC Score submission" />
		<meta property="og:image" content="https://fnite.se/logo.png" />
		<!-- A joint effort by Tamm and DoXiD to create a quick tournament site during DreamHack Summer 2018.
			Needless to say, we put this site together in less than 6 hours, some features were intended, some wasn't.
			All in the good spirit of hackathon.

			Thread lightly, Ye who enter here.. Here be dragons -->
		<style type="text/css">
			@font-face {
				font-family: LuckyGuy;
				src: url('/LuckiestGuy-Regular.ttf');
			}

			:root {
				--blue: #62CFEE;
				--pink: #F92472;
				--green: #A6E22C;
				--yellow: #E7DB74;
				--orange: #f60;
				--moreorange: #66D9EF;
				--teal: #66D9EF;
				--darkish: #74705D;
				--dark: #2a2a2a;
			}

			body {
				background-image: url('./background.jpg');
				background-size: cover;
				background-position: center center;
				background-attachment: fixed;
				margin: 0px;
				padding: 0px;
				color: #FFFFFF;
				font-family: Helvetica Neue,Helvetica,Arial,sans-serif;
			}

			#content {
				position: absolute;
				left: 0px;
				top: 0px;
				width: 100%;
				height: 100%;
				background-color: #272822;

				column-count: 1;
				column-width: 100%;
				column-gap: 2px;
			}

				#content > div > table {
					text-align: center;
					break-inside: avoid;
					width: 100%;
				}

					.mode {
						font-size: 34px;
						padding: 20px;
					}

					.pname, .pscore {
						font-size: 34px;
						padding: 20px;
						opacity: 0.8;
					}

					.name {
						font-family: Arial, sans-serif;
						font-weight: bold;
					}

					.solo {
						padding-bottom: 0px;
						padding-top: 40px;
						font-family: 'LuckyGuy', cursive;
						color: var(--green);
					}

					.duo {
						padding-bottom: 0px;
						padding-top: 40px;
						font-family: 'LuckyGuy', cursive;
						color: var(--blue);
					}
		</style>
	</head>
<body>
	<div id="content">
<?php
	}
	//** This is where the TV-Mode ends.
?>
<?php
if(1==2) {
	?>
	<div id="moo">

		<table>
			<tr>
				<th class="mode solo" colspan="2">
					solo
				</th>
			</tr>
			<tr>
				<th class="pname">
					Player Name
				</th>
				<th class="pscore">
					Player score
				</th>
			</tr>
			<?php

				if (time() - strtotime($config["start_dates"]["solo"]) < 0) {
					print '<tr><td colspan="2" style="color: var(--pink);">The solo-tournament has not begun yet.</td></tr>';
				} else if (!$solo_results || pg_num_rows($solo_results) <= 0) {
					print '<tr><td colspan="2">There are no scores here yet.</td></tr>';
				} else {
					$players = array();
					while ($row = pg_fetch_assoc($solo_results)) {
						if(!isset($players[$row['player_id']]))
							$players[$row['player_id']] = array('player_id' => $row['player_id'], 'games' => array(), 'top_5' => array(), 'total_score_top_5' => 0);

						$game_struct = array('placement' => $row['placement'], 'kills' => $row['kills'], 'score' => $row['score']);
						if(sizeof($players[$row['player_id']]['top_5']) < 5) {
							$players[$row['player_id']]['top_5'][] = $game_struct;
							$players[$row['player_id']]['total_score_top_5'] += $game_struct['score'];
						}
					}

					usort($players, function($a, $b){
						return $a['total_score_top_5'] == $b['total_score_top_5'] ? 0 : $a['total_score_top_5'] < $b['total_score_top_5'] ? 1 : -1;
					});

					foreach($players as $player_id => $data) {
						print "<tr><td class='name'>" . $data['player_id'] . "</td><td>" . $data['total_score_top_5'] . "</td></tr>";
					}
				}

			?>
		</table>
	</div>
<?php
}
?>
<div id="moo2">
	<table>
		<tr>
			<th class="mode duo" colspan="2">
				duo
			</th>
		</tr>
		<tr>
			<th class="pname">
				Player Names
			</th>
			<th class="pscore">
				Team score
			</th>
		</tr>
		<?php

			if (time() - strtotime($config["start_dates"]["duo"]) < 0) {
				print '<tr><td colspan="2" style="color: var(--pink);">The duo-tournament has not begun yet.</td></tr>';
			} else if (!$team_results || pg_num_rows($team_results) <= 0) {
				print '<tr><td colspan="2">There are no scores here yet.</td></tr>';
			} else {
				$teams = array();
				while ($row = pg_fetch_assoc($team_results)) {
					if ($row['player_one'].$row['player_two'] == '') {
						continue;
					}
					$team = $row['player_one'].', '.$row['player_two'];

					if(!isset($teams[$team]))
						$teams[$team] = array('player_id' => $team, 'games' => array(), 'top_5' => array(), 'total_score_top_5' => 0);

					$game_struct = array('placement' => $row['placement'], 'kills' => $row['kills'], 'score' => $row['score']);
					$teams[$team]['games'][] = $game_struct;
					if(sizeof($teams[$team]['top_5']) < 5) {
						$teams[$team]['top_5'][] = $game_struct;
						$teams[$team]['total_score_top_5'] += $game_struct['score'];
					}
				}

				usort($teams, function($a, $b){
					return $a['total_score_top_5'] == $b['total_score_top_5'] ? 0 : $a['total_score_top_5'] < $b['total_score_top_5'] ? 1 : -1;
				});

				foreach($teams as $player_id => $data) {
					print "<tr><td class='name'>" . $data['player_id'] . "</td><td>" . $data['total_score_top_5'] . "</td></tr>";
				}
			}

		?>
	</table>
</div>
