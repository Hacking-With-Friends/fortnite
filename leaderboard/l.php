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

			#content {
				column-count: 2;
				column-width: 50%;
				column-gap: 2px;

				height: 100%;
				margin: 0 auto;
				border-left: 1px solid #66D9EF;
				border-right: 1px solid #66D9EF;
			}

			#content > div {
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
		</style>
	</head>
	<body>
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

						//header('Content-type: text/javascript');

						$db_user = null;
						$db_pass = null;
						$db_db = null;

						$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

						// Prepare a query for execution
					//	$result = pg_prepare($dbconn, "solo", 'SELECT player_id, placement, kills FROM fortnite_solo;');
					//	$result = pg_prepare($dbconn, "duo", 'SELECT player_one, player_two, placement, kills FROM fortnite_duo;');

						// Execute the prepared query.  Note that it is not necessary to escape
						// the string "Joe's Widgets" in any way

						$result = pg_query($dbconn, "SELECT player_id, placement, kills, score FROM fortnite_solo ORDER BY score DESC;");
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
								print "<tr><td class='name'>" . $data['player_id'] . "</td><td>" . $data['total_score_top_5'] . "</td></tr>";
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

						$result = pg_query($dbconn, "SELECT player_one, player_two, placement, kills, score FROM fortnite_duo ORDER BY score DESC;");
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
								print "<tr><td class='name'>" . $data['player_id'] . "</td><td>" . $data['total_score_top_5'] . "</td></tr>";
							}
						}
					?>
				</table>
			</div>
		</div>
	</body>
</html>