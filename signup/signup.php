<?php
	header('Content-type: text/javascript');
	
	$data = json_decode(file_get_contents('php://input'), true);
	error_log(json_encode($data));

	$db_user = null;
	$db_pass = null;
	$db_db = null;

	$dbconn = pg_connect("dbname=$db_db user=$db_user password=$db_pass");
	$result = pg_prepare($dbconn, "signup", 'INSERT INTO signups (players, mode) VALUES ($1, $2);');

	$tmp = json_encode($data['players']);
	$tmp[0] = '{';
	$tmp[strlen($tmp) - 1] = '}';
	$result = pg_execute($dbconn, "signup", array($tmp, $data['mode']));
	if(!$result) {
		print json_encode(array('result' => 'success'));
	} else {
		print json_encode(array('result' => 'failed', 'reason' => '...'));
	}
?>