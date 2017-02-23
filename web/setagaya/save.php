<?php
require ("lib/bdd.inc.php");
require (__DIR__ . '/library/ElephantIO/Client.php');
use ElephantIO\Client as ElephantIOClient;
session_start ();
// change the following paths if necessary
$config = dirname ( __FILE__ ) . '/library/config.php';
require_once ("library/Hybrid/Auth.php");
$lvl = array ();
$lvl ['ui'] = 3;
$lvl ['nui'] = 2;
$lvl ['uni'] = 1;
$lvl ['nuni'] = 0;
if (isset ( $_SESSION ['username'] )) {
	// Changing/Creating project name
	if (isset ( $_POST ['action'] ) && $_POST ['action'] == "project-name") {
		// id specified
		if (isset ( $_POST ['inputId'] ) && $_POST ['inputId'] > 0) {
			// Check rights
			$req = "SELECT * FROM projects pj INNER JOIN users us ON us.user_id = pj.project_owner WHERE pj.project_id = '" . mysqli_escape_string ( $_POST ['inputId'] ) . "' AND user_login = '" . $_SESSION ['username'] . "';";
			$res = $mysqli->query ( $req );
			if (mysqli_num_rows ( $res ) == 1) {
				// Update project
				$req = "UPDATE projects SET project_name = '" . mysqli_escape_string ( $_POST ['inputName'] ) . "' WHERE project_id = '" . mysqli_escape_string ( $_POST ['inputId'] ) . "';";
				$res = $mysqli->query ( $req );
				$mysqli->close ( );
				header ( 'Location:./form.php' );
			} else {
				// No rights to modifiy name
				// TODO message
				$mysqli->close ( );
				header ( "HTTP/1.1 401 Unauthorized" );
				exit ();
			}
		} else {
			// Check users
			$req = "SELECT user_id FROM users WHERE user_login = '" . $_SESSION ['username'] . "';";
			$res = $mysqli->query ( $req );
			if (mysqli_num_rows ( $res ) > 0) {
				$line = mysqli_fetch_array ( $res, MYSQLI_ASSOC );
				$id = $line ['user_id'];
			} else {
				$req = "INSERT INTO users (`user_id` , `user_login`) VALUES (NULL, '" . mysqli_escape_string ( $_SESSION ['username'] ) . "');";
				$res = $mysqli->query ( $req );
				$id = mysqli_insert_id ();
			}
			$req = "INSERT INTO projects (`project_id`, `project_name`, `project_owner`) VALUES (NULL, '" . mysqli_escape_string ( $_POST ['inputName'] ) . "', '" . $id . "');";
			$res = $mysqli->query ( $req );
			$idProject = mysqli_insert_id ();
			$req = "INSERT INTO rights (`right_id` ,`right_user` ,`right_project` ,`right_modify` ,`right_act` ,`right_manuser` ,`right_mantask`) VALUES (NULL ,  '" . $id . "', '" . $idProject . "', '1', '1', '1', '1');";
			$res = $mysqli->query ( $req );
			$mysqli->close ( );
			header ( 'Location:./form.php' );
		}
	} else if ((isset ( $_POST ['p_id'] ) && $_POST ['p_id'] > 0) || (isset ( $_GET ['p_id'] ) && $_GET ['p_id'] != "")) {
		if (isset ( $_POST ['p_id'] ) && $_POST ['p_id'] > 0) {
			$p_id = mysqli_escape_string ( $_POST ['p_id'] );
		} else {
			$p_id = mysqli_escape_string ( $_GET ['p_id'] );
		}
		if (isset ( $_POST ['t_id'] ) && $_POST ['t_id'] > 0) {
			$t_id = mysqli_escape_string ( $_POST ['t_id'] );
		} else if (isset ( $_GET ['t_id'] ) && $_GET ['t_id'] != "") {
			$t_id = mysqli_escape_string ( str_replace ( "li-", "", $_GET ['t_id'] ) );
		}
		if (isset ( $t_id )) {
			$sql = " INNER JOIN tasks ON task_id = " . $t_id . " AND task_project = right_project";
		} else {
			$sql = "";
		}
		$req = "SELECT * FROM users INNER JOIN rights rg ON rg.right_user = user_id" . $sql . " WHERE right_project = " . $p_id . " AND user_login = '" . $_SESSION ['username'] . "';";
		$res = $mysqli->query ( $req );
		
		// Check t_id part off project
		
		if (mysqli_num_rows ( $res ) == 1) {
			if (isset ( $_POST ['action'] ) && $_POST ['action'] == "task-name") {
				// id specified
				if (isset ( $_POST ['t_id'] ) && $_POST ['t_id'] > 0) {
					// Update task
					$req = "UPDATE tasks SET task_name = '" . mysqli_escape_string ( $_POST ['inputTask'] ) . "',  task_category = '" . mysqli_escape_string ( $_POST ['inputCategory'] ) . "', task_time = '" . mysqli_escape_string ( $_POST ['inputHours'] ) . "' WHERE task_id = '" . mysqli_escape_string ( $_POST ['t_id'] ) . "';";
					$res = $mysqli->query ( $req );
					$mysqli->close ( );
					if ($socketIo) {
						try {
							$elephant = new ElephantIOClient ( 'http://katagena.com/', 'socket.io', 1, false, true, true );
							$elephant->init ();
							$elephant->send ( ElephantIOClient::TYPE_EVENT, null, null, json_encode ( array (
									'name' => 'client_data',
									'args' => array (
											'pId' => $p_id,
											'tId' => mysqli_escape_string ( $_POST ['t_id'] ) 
									) 
							) ) );
							$elephant->close ();
						} Catch ( Exception $e ) {
							var_dump ( $e );
						}
					}
					header ( 'Location:./form.php?p_id=' . $p_id );
				} else {
					$req = "INSERT INTO `tasks` (`task_id` ,`task_name` ,`task_category` ,`task_time` ,`task_deadline` ,`task_lvl` ,`task_project` ,`task_owner` ,`task_order`) VALUES (NULL , '" . mysqli_escape_string ( $_POST ['inputTask'] ) . "', '" . mysqli_escape_string ( $_POST ['inputCategory'] ) . "', '" . mysqli_escape_string ( $_POST ['inputHours'] ) . "', NULL , '0', '" . $p_id . "', NULL, '');";
					$res = $mysqli->query ( $req );
					$id = mysqli_insert_id ();
					$mysqli->close ( );
					if ($socketIo) {
						try {
							$elephant = new ElephantIOClient ( 'http://katagena.com/', 'socket.io', 1, false, true, true );
							$elephant->init ();
							$elephant->send ( ElephantIOClient::TYPE_EVENT, null, null, json_encode ( array (
									'name' => 'client_data',
									'args' => array (
											'pId' => $p_id,
											'tId' => $id 
									) 
							) ) );
							$elephant->close ();
						} Catch ( Exception $e ) {
							var_dump ( $e );
						}
					}
					header ( 'Location:./form.php?p_id=' . $p_id );
				}
			} else if (isset ( $_GET ['action'] ) && $_GET ['action'] == "task-order" && isset ( $_GET ['t_id'] ) && $_GET ['t_id'] != "") {
				$id = mysqli_escape_string ( str_replace ( "li-", "", $_GET ['t_id'] ) );
				// Update task
				$req = "UPDATE tasks SET task_order = '" . mysqli_escape_string ( $_GET ['order'] ) . "' WHERE task_id = '" . mysqli_escape_string ( $id ) . "';";
				$res = $mysqli->query ( $req );
				$mysqli->close ( );
				if ($socketIo) {
					try {
						$elephant = new ElephantIOClient ( 'http://katagena.com/', 'socket.io', 1, false, true, true );
						$elephant->init ();
						$elephant->send ( ElephantIOClient::TYPE_EVENT, null, null, json_encode ( array (
								'name' => 'client_data',
								'args' => array (
										'pId' => $p_id,
										'tId' => mysqli_escape_string ( $id ) 
								) 
						) ) );
						$elephant->close ();
					} Catch ( Exception $e ) {
						var_dump ( $e );
					}
				}
				// TODO return si OK
				$result = array ();
				$result ['task'] = array (
						'task_id' => $id,
						'task_order' => mysqli_escape_string ( $_GET ['order'] ) 
				);
				header ( 'Content-type: application/json' );
				echo json_encode ( $result );
			} else if (isset ( $_GET ['action'] ) && $_GET ['action'] == "task-done" && isset ( $_GET ['t_id'] ) && $_GET ['t_id'] != "") {
				$id = mysqli_escape_string ( str_replace ( "li-", "", $_GET ['t_id'] ) );
				// Update task
				$req = "UPDATE tasks SET task_done = '1' WHERE task_id = '" . mysqli_escape_string ( $id ) . "';";
				$res = $mysqli->query ( $req );
				$mysqli->close ( );
				if ($socketIo) {
					try {
						$elephant = new ElephantIOClient ( 'http://katagena.com/', 'socket.io', 1, false, true, true );
						$elephant->init ();
						$elephant->send ( ElephantIOClient::TYPE_EVENT, null, null, json_encode ( array (
								'name' => 'client_data',
								'args' => array (
										'pId' => $p_id,
										'tId' => mysqli_escape_string ( $id ) 
								) 
						) ) );
						$elephant->close ();
					} Catch ( Exception $e ) {
						var_dump ( $e );
					}
				}
				// TODO return si OK
				$result = array ();
				$result ['task'] = array (
						'task_id' => $id,
						'task_done' => '1' 
				);
				header ( 'Content-type: application/json' );
				echo json_encode ( $result );
			} else if (isset ( $_GET ['action'] ) && $_GET ['action'] == "planning-task" && isset ( $_GET ['t_id'] ) && $_GET ['t_id'] != "" && isset ( $_GET ['from'] ) && $_GET ['from'] != "" && isset ( $_GET ['to'] ) && $_GET ['to'] != "") {
				$to = $_GET ['to'];
				$id = mysqli_escape_string ( str_replace ( "li-", "", $_GET ['t_id'] ) );
				if (substr ( $_GET ['from'], 0, 6 ) == "today-") {
					$req = "UPDATE tasks SET task_owner = NULL WHERE task_id = " . $id . ";";
					$res = $mysqli->query ( $req );
				}
				if (isset ( $lvl [$to] )) {
					$req = "UPDATE tasks SET task_lvl = " . $lvl [$to] . " WHERE task_id = " . $id . ";";
					$res = $mysqli->query ( $req );
				} else if (substr ( $_GET ['to'], 0, 6 ) == "today-") {
					$req = "UPDATE tasks SET task_owner = " . substr ( $_GET ['to'], 6 ) . " WHERE task_id = " . $id . ";";
					$res = $mysqli->query ( $req );
				}
				if ($socketIo) {
					try {
						$elephant = new ElephantIOClient ( 'http://katagena.com/', 'socket.io', 1, false, true, true );
						$elephant->init ();
						$elephant->send ( ElephantIOClient::TYPE_EVENT, null, null, json_encode ( array (
								'name' => 'client_data',
								'args' => array (
										'pId' => $p_id,
										'tId' => mysqli_escape_string ( $id ) 
								) 
						) ) );
						$elephant->close ();
					} Catch ( Exception $e ) {
						var_dump ( $e );
					}
				}
				$mysqli->close ( );
				echo "task updated";
			} else if (isset ( $_GET ['action'] ) && $_GET ['action'] == "remove-user" && isset ( $_GET ['u_id'] ) && $_GET ['u_id'] != "") {
				$req = "DELETE FROM rights WHERE right_project = " . $p_id . " AND right_user = " . mysqli_escape_string ( $_GET ['u_id'] ) . ";";
				$res = $mysqli->query ( $req );
				$req = "UPDATE tasks SET task_owner = NULL WHERE task_project = " . $p_id . " AND task_owner = " . mysqli_escape_string ( $_GET ['u_id'] ) . ";";
				$res = $mysqli->query ( $req );
				$mysqli->close ( );
				header ( 'Location:./form.php?p_id=' . $p_id );
			} else if (isset ( $_GET ['action'] ) && $_GET ['action'] == "add-user" && isset ( $_POST ['u_name'] ) && $_POST ['u_name'] != "") {
				$req = "SELECT user_id FROM users WHERE user_login = '" . mysqli_escape_string ( $_POST ['u_name'] ) . "';";
				$res = $mysqli->query ( $req );
				if (mysqli_num_rows ( $res ) > 0) {
					$line = mysqli_fetch_array ( $res, MYSQLI_ASSOC );
					$id = $line ['user_id'];
				} else {
					$req = "INSERT INTO users (`user_id` , `user_login`) VALUES (NULL, '" . mysqli_escape_string ( $_POST ['u_name'] ) . "');";
					$res = $mysqli->query ( $req );
					$id = mysqli_insert_id ();
				}
				$req = "INSERT INTO rights (`right_id` ,`right_user` ,`right_project` ,`right_modify` ,`right_act` ,`right_manuser` ,`right_mantask`) VALUES (NULL ,  '" . $id . "', '" . $p_id . "', '1', '1', '1', '1');";
				$res = $mysqli->query ( $req );
				$mysqli->close ( );
				header ( 'Location:./form.php?p_id=' . $p_id );
			} else {
				echo "No correct parameters";
			}
		} else {
			// No rights to modifiy name
			// TODO message
			$mysqli->close ();
			header ( "HTTP/1.1 401 Unauthorized" );
			exit ();
		}
	}
}
?>