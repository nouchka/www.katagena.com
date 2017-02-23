<?php
session_start();

if(isset($_GET['t_id']) && $_GET['t_id'] > 0){
	$t_id = $_GET['t_id'];
	require("lib/bdd.inc.php");
	$req = "SELECT * FROM tasks INNER JOIN rights rg ON task_project = right_project INNER JOIN users ON right_user = user_id AND user_login = '".$_SESSION['username']."' WHERE  task_id = ".$t_id.";";
	$res = mysql_query($req);
	if(mysql_num_rows($res) == 0){
		mysql_close($link);
		header("HTTP/1.1 401 Unauthorized");
		exit;
	}
	header('Content-type: application/json');
	$req = "SELECT * FROM tasks WHERE task_id = ".$t_id.";";
	$res = mysql_query($req);
	$line = mysql_fetch_array($res);
	$result = array();
	$result['task'] = array('task_id' => $line["task_id"], 'task_name' => utf8_encode($line["task_name"]), 'task_category' => utf8_encode($line["task_category"]), 'task_time' => $line["task_time"], 'task_deadline' => $line["task_deadline"], 'task_lvl' => $line["task_lvl"], 'task_owner' => $line["task_owner"], 'task_order' => $line["task_order"]);
	echo json_encode($result);
	mysql_close($link);
}else if(isset($_GET['p_id']) && $_GET['p_id'] > 0){
	$p_id = $_GET['p_id'];
	require("lib/bdd.inc.php");
	$req = "SELECT * FROM users INNER JOIN rights rg ON rg.right_user = user_id WHERE right_project = ".$_GET['p_id']." AND user_login = '".$_SESSION['username']."';";
	$res = mysql_query($req);
	if(mysql_num_rows($res) == 0 && $p_id != 1){
		mysql_close($link);
		header("HTTP/1.1 401 Unauthorized");
		exit;
	}
	header('Content-type: application/json');
	$result = array();
	$result['users'] = array();
	$req = "SELECT * FROM `users` INNER JOIN rights ON right_user = user_id AND right_project = ".$p_id." LEFT JOIN projects ON project_id = ".$p_id." AND project_owner = user_id ORDER BY user_login ASC;";
	$res = mysql_query($req);
	while($line=mysql_fetch_array($res)){
		$result['users'][] = array('id'=>$line['user_id'], 'name'=>$line['user_login'], 'owner' => $line['project_owner'], 'project' => $line['right_project']);
	}
	$req = "SELECT * FROM  `tasks` LEFT JOIN `users` ON user_id = task_owner WHERE task_done = 0 AND task_project = ".$p_id." ORDER BY task_order ASC;";
	$res = mysql_query($req);
	while($line=mysql_fetch_array($res)){
		$result['tasks'][] = array('task_id' => $line["task_id"], 'task_name' => utf8_encode($line["task_name"]), 'task_category' => utf8_encode($line["task_category"]), 'task_time' => $line["task_time"], 'task_deadline' => $line["task_deadline"], 'task_lvl' => $line["task_lvl"], 'task_owner' => $line["task_owner"], 'task_order' => $line["task_order"]);
	}
	$result['socket_io'] = $socketIo;
	echo json_encode($result);
	mysql_close($link);
}
?>