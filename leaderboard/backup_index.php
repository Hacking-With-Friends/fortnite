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
							print json_encode(array('result' => 'Couldnt fetch leaderboard.'));
							exit;
						}

						//$placement_map = array(1 => 80, 2 => 60, 3 => 40, 4 => 20, 5 => 10);
						//$placement_cap = 5;
						$players = array();
						while ($row = pg_fetch_assoc($result)) {
							if(!isset($players[$row['player_id']]))
								$players[$row['player_id']] = array('games' => array(), 'top_5' => array(), 'total_score_top_5' => 0);

							$game_struct = array('placement' => $row['placement'], 'kills' => $row['kills'], 'score' => $row['score']);
							$players[$row['player_id']]['games'][] = $game_struct;
							if(sizeof($players[$row['player_id']]['top_5']) < 5) {
								$players[$row['player_id']]['top_5'][] = $game_struct;
								$players[$row['player_id']]['total_score_top_5'] += $game_struct['score'];
							}
						}

						$leaderboard = array();
						foreach($players as $player_id => $player_info) {
							$score = $player_info['total_score_top_5'];
							$index = -1;
							$leaderboard_index = 0;
							foreach($leaderboard as $player => $highscore) {
								if ($score > $highscore) {
									$index = $leaderboard_index;
									break;
								}
								$leaderboard_index++;
							}
							error_log(sizeof($leaderboard));
							if($index == -1 || sizeof($leaderboard) <= 0)
								$leaderboard[$player_id] = $score;
							elseif ($index)
								array_splice($leaderboard, $index, $score);
						}

						if(!sizeof($players)) {
							print json_encode(array('result' => 'Couldnt fetch leaderboard.'));
						} else {
							//print json_encode($leaderboard, JSON_PRETTY_PRINT);
							foreach($leaderboard as $player_id => $score) {
								print "<tr><td>$player_id</td><td>$score</td></tr>";
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

						//header('Content-type: text/javascript');

						$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");

						// Prepare a query for execution
					//	$result = pg_prepare($dbconn, "solo", 'SELECT player_id, placement, kills FROM fortnite_solo;');
					//	$result = pg_prepare($dbconn, "duo", 'SELECT player_one, player_two, placement, kills FROM fortnite_duo;');

						// Execute the prepared query.  Note that it is not necessary to escape
						// the string "Joe's Widgets" in any way

						$result = pg_query($dbconn, "SELECT player_one, player_two, placement, kills, score FROM fortnite_duo ORDER BY score DESC;");
						if (!$result) {
							print json_encode(array('result' => 'There are no scores yet.'));
							exit;
						}


						$teams = array();
						//$placement_map = array(1 => 80, 2 => 60, 3 => 40, 4 => 20, 5 => 10);
						//$placement_cap = 5;
						while ($row = pg_fetch_assoc($result)) {
							$team = $row['player_one'].', '.$row['player_two'];

							if(!isset($teams[$team]))
								$teams[$team] = array('games' => array(), 'top_5' => array(), 'total_score_top_5' => 0);

							$game_struct = array('placement' => $row['placement'], 'kills' => $row['kills'], 'score' => $row['score']);
							$teams[$team]['games'][] = $game_struct;
							if(sizeof($teams[$team]['top_5']) < 5) {
								$teams[$team]['top_5'][] = $game_struct;
								$teams[$team]['total_score_top_5'] += $game_struct['score'];
							}
						}

						$leaderboard = array();
						foreach($teams as $team_id => $team_info) {
							$score = $team_info['total_score_top_5'];
							$index = -1;
							$leaderboard_index = 0;
							foreach($leaderboard as $team => $highscore) {
								if ($score > $highscore) {
									$index = $leaderboard_index;
									break;
								}
								$leaderboard_index++;
							}
							if($index == -1 || sizeof($leaderboard) <= 0){
								$leaderboard[$team_id] = $score;
							}
							elseif ($index >= 0) {
								$carry = array_splice($leaderboard, $index, $score);
								$leaderboard += $carry;
							}
						}

						if(!sizeof($teams)) {
							print json_encode(array('result' => 'There are no scores yet.'));
						} else {
							//print json_encode($leaderboard, JSON_PRETTY_PRINT);
							foreach($leaderboard as $team_id => $score) {
								print "<tr><td>$team_id</td><td>$score</td></tr>";
							}
						}
					?>
				</table>
			</div>
		</div>
	</body>
</html>