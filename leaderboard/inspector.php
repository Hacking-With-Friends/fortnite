		<?php
		$allowed_IP = ['77.80.235.37', '77.218.254.202'];

		if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_IP)) {
			die('401');
		} ?>

<?php

	$db_user = null;
	$db_pass = null;
	$db_db = null;

	if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
		if (isset($_GET['game'])) {
			$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

			$game_id = pg_escape_string($_GET['game']);
			$mode = pg_escape_string($_GET['mode']);
			if (!isset($_GET['game'])) {
				die('No game selected');
			}
			if (!isset($_GET['mode'])) {
				die('No mode selected');
			}

			$result = pg_query($dbconn, "DELETE FROM fortnite_$mode WHERE id = '$game_id'");
			print json_encode(pg_affected_rows($result));

			if (!$result || pg_affected_rows($result) == false) {
				http_response_code(404);
				die('Not found');
			} else {
				die('DELETED');
			}
		} else {
			http_response_code(404);
			die('Not found');
		}

	 	die('DELETE ACTION');
	 }
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if (isset($_GET['ban'])) {
			$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

			$player_id = pg_escape_string($_GET['ban']);
			if (!isset($_GET['ban'])) {
				die('No user to ban');
			}

			$result = pg_prepare($dbconn, "my_query", 'INSERT INTO bans (player_id) VALUES ($1);');
			$result = pg_execute($dbconn, "my_query", array($player_id));
		} else {
			http_response_code(404);
			die('Not found');
		}

	 	die('BAN ACTION');
	 }
?>

<html>
	<head>
		<style>
			@font-face {
				font-family: LuckGuy;
				src: url(/LuckiestGuy-Regular.ttf);
			}
			body {
				font-family: 'LuckGuy', cursive;
				background-color: #66D9EF;
				color: #F8F8F0;
				margin: 0px;
			}
			#popup {
				background-color: #49483E !important;
				border: 1px solid #66D9EF;
				text-align-last: center;
				color: #F8F8F0;
				position: absolute;
				top: 50%;
				margin-top: -5rem;
				left: 50%;
				margin-left: -20%;
				width: 40%;
			}

			#popup > h3 {
				font-family: 'LuckGuy', cursive;
			}

			#content {
				width: 100%;
				height: 100%;
				margin: 0 auto;

				display: flex;
				flex-direction: row;

			}
			#content > div {
				flex: 1 1 auto;
				height: 100%;
				width: 100%;
				background-color: #272822;
				overflow-y: auto;
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
					color: #66D9EF;
				}

				.duo {
					color: #FD971F;
				}

			a {
				color: white;
			}

			a:visited {
				color: orange;
			}
		</style>
		<script type="text/javascript">
			var timers = {};
			function clearTimer(name) {
				if(timers[name] !== undefined) {
					window.clearInterval(timers[name]);
					return true;
				}
				return false;
			}

			function setTimer(name, func, time=10) {
				timers[name] = setInterval(func, time);
			}

			function deleteResult(game_id, mode) {
				var xhr = new XMLHttpRequest();
				xhr.open("DELETE", '/inspector.php?game=' + game_id + '&mode=' + mode, true);

				//Send the proper header information along with the request
				xhr.setRequestHeader("Content-type", "application/json");

				xhr.onreadystatechange = function() {//Call a function when the state changes.
					if(xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
						showPopup("Success", "Result deleted.");

					} else if (xhr.readyState == XMLHttpRequest.DONE) {
						showPopup("Failure", "Failed to delete result: " + xhr.status, true);
					}
				}
				xhr.send();
			}

			function banUser(player_id) {
				var xhr = new XMLHttpRequest();
				xhr.open("POST", '/inspector.php?ban=' + player_id, true);

				//Send the proper header information along with the request
				xhr.setRequestHeader("Content-type", "application/json");

				xhr.onreadystatechange = function() {//Call a function when the state changes.
					if(xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
						showPopup("Success", "User banned.");

					} else if (xhr.readyState == XMLHttpRequest.DONE) {
						showPopup("Failure", "Failed to ban user: " + xhr.status, true);
					}
				}
				xhr.send();
			}

			function showPopup(title, content, error=false) {
				let popup = document.getElementById('popup');
				if(popup)
					popup.remove();

				popup = document.createElement('div');
				popup.id = 'popup';

				popup.innerHTML = '<h3 id="popup_title">'+title+'</h3>';
				popup.innerHTML += '<p>'+content+'</p>';


				document.body.appendChild(popup);
				if (error) {
					document.getElementById('popup_title').style.color = '#FD971F';
				}

				setTimer('clear_popup', function() {
					let popup = document.getElementById('popup');
					if(popup)
						popup.remove();
					clearTimer('clear_popup');
				}, 5000);
			}

			window.onload = function() {
			}
		</script>
	</head>
	<body>

		<?php if (isset($_GET['player_id'])) : ?>
		<div id="content" class="single">
			<div id="moo">
				<table>
					<tr>
						<th class="mode solo" colspan="4">
							solo
						</th>
					</tr>
					<tr>
						<th>
							Game ID
						</th>
						<th >
							Player Name
						</th>
						<th >
							placement
						</th>
						<th >
							kills
						</th>
						<th >
							score
						</th>
					</tr>
					<?php

						$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");
						if (isset($_GET['player_id'])) {
							$player_id = pg_escape_string($_GET['player_id']);
						}
						print '<tr><td colspan="4"><button onclick="banUser(\''.$player_id.'\')">BAN USER</button></td></tr>';
						if (isset($_GET['game'])) {
							$game_id = pg_escape_string($_GET['game']);
						}
						$game_id_query = $game_id ? "AND id = '$game_id'" : "";
						$game_id_query_extrafields = $game_id ? ", screenshot" : "";

						$result = pg_query($dbconn, "SELECT id, player_id, placement, kills, score".$game_id_query_extrafields." FROM fortnite_solo WHERE player_id = '$player_id' ".$game_id_query."ORDER BY score DESC;");

						if (!$result) {
							print '<tr><td>' . json_encode(array('result' => 'There are no scores yet.')) . '</td></tr>';
							exit;
						}

						while ($row = pg_fetch_assoc($result)) {
							print "<tr><td>".$row['id']."</td><td class='name'><a href='?player_id=".$row['player_id']."&game=".$row['id']."'>" . $row['player_id'] . "</a></td><td>" . $row['placement'] ."</td><td>". $row['kills'] ."</td><td>". $row['score'] . "</td></tr>";
							if ($game_id) {
								print '<tr><td colspan="4"><button onclick="deleteResult('.$game_id.', \'solo\')">DELETE RESULT</button></td></tr>';
								print '<tr><td colspan="4"><img width="100%" src="' . $row['screenshot'] . '"></td></tr>';
							}
						}
					?>
				</table>
			</div>
		</div>
		<?php elseif (isset($_GET['team'])) : ?>
		<div id="content" class="single">
			<div id="moo">
				<table>
					<tr>
						<th class="mode duo" colspan="4">
							duo
						</th>
					</tr>
					<tr>
						<th>
							Game ID
						</th>
						<th >
							Team Name
						</th>
						<th >
							placement
						</th>
						<th >
							kills
						</th>
						<th >
							score
						</th>
					</tr>
					<?php
						$team = false;
						$player_one = false;
						$player_two = false;
						$game_id = false;
						$game_id_query = false;
						$game_id_query_extrafields = false;

						$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");
						if (isset($_GET['team'])) {
							$team = explode(', ', pg_escape_string(urldecode($_GET['team'])));
							$player_one = $team[0];
							$player_two = $team[1];
						}

						print '<tr><td colspan="4"><button onclick="banUser(\''.$player_one.'\')">BAN '.$player_one.'</button><button onclick="banUser(\''.$player_two.'\')">BAN '.$player_two.'</button></td></tr>';

						if (isset($_GET['game'])) {
							$game_id = pg_escape_string($_GET['game']);
							$game_id_query = $game_id ? "AND id = '$game_id'" : "";
							$game_id_query_extrafields = $game_id ? ", screenshot" : "";
						}

						$result = pg_query($dbconn, "SELECT id, player_one, player_two, placement, kills, score".$game_id_query_extrafields." FROM fortnite_duo WHERE player_one = '$player_one' AND player_two = '$player_two' ".$game_id_query."ORDER BY id DESC;");

						if (!$result) {
							print '<tr><td>' . json_encode(array('result' => 'There are no scores yet.')) . '</td></tr>';
							exit;
						}


						while ($row = pg_fetch_assoc($result)) {
							$team = $row['player_one'].', '.$row['player_two'];
							print "<tr><td>".$row['id']."</td><td class='name'><a href='?team=".urlencode($team)."&game=".$row['id']."'>" . $team . "</a></td><td>" . $row['placement'] ."</td><td>". $row['kills'] ."</td><td>". $row['score'] . "</td></tr>";
							if ($game_id) {
								print '<tr><td colspan="4"><button onclick="deleteResult('.$game_id.', \'duo\')">DELETE RESULT</button></td></tr>';
								print '<tr><td colspan="4"><img width="100%" src="' . $row['screenshot'] . '"></td></tr>';
							}
						}
					?>
				</table>
			</div>
		</div>
		<?php else: ?>
		<div id="content">
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

						$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

						$result = pg_query($dbconn, "SELECT player_id, placement, kills, score FROM fortnite_solo WHERE player_id NOT IN (SELECT player_id FROM bans) ORDER BY score DESC;");
						if (!$result) {
							print '<tr><td>' . json_encode(array('result' => 'There are no scores yet.')) . '</td></tr>';
							exit;
						}

						$players = array();
						while ($row = pg_fetch_assoc($result)) {
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

						if(!sizeof($players)) {
							print '<tr><td>' . json_encode(array('result' => 'There are no scores yet.')) . '</td></tr>';
						} else {
							foreach($players as $player_id => $data) {
								print "<tr><td class='name'><a href='?player_id=".$data['player_id']."'>" . $data['player_id'] . "</a></td><td>" . $data['total_score_top_5'] . "</td></tr>";
							}
						}
					?>
				</table>
			</div>
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
							Player score
						</th>
					</tr>
					<?php

						$result = pg_query($dbconn, "SELECT player_one, player_two, placement, kills, score FROM fortnite_duo WHERE player_one NOT IN (SELECT player_id FROM bans) AND player_two NOT IN (SELECT player_id FROM bans) ORDER BY score DESC;");
						if (!$result) {
							print '<tr><td>' . json_encode(array('result' => 'There are no scores yet.')) . '</td></tr>';
							exit;
						}

						$teams = array();
						while ($row = pg_fetch_assoc($result)) {
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


						if(!sizeof($teams)) {
							print '<tr><td>' . json_encode(array('result' => 'There are no scores yet.')) . '</td></tr>';
						} else {
							foreach($teams as $player_id => $data) {
								print "<tr><td class='name'><a href='?team=".urlencode($data['player_id'])."'>" . $data['player_id'] . "</a></td><td>" . $data['total_score_top_5'] . "</td></tr>";
							}
						}
					?>
				</table>
			</div>
		</div>
		<?php endif; ?>
	</body>
</html>