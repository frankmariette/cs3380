<?php
	require_once('dbconnect.php');
	$username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : "";
	if($username == null){
		echo "\tPlease enter a username.";
	}
	else{
		$query = "SELECT username FROM lab8.user_info WHERE username ILIKE $1";

		pg_prepare($conn, "checker", $query);
		$result = pg_execute($conn, "checker", array($username));
		$result = pg_fetch_array($result, null, PGSQL_ASSOC);
		if ($result['username'] == $username) {
			echo "<span style='color: red; font-weight: bold;'>\tUsername is already taken.</span>";
		} else{
			echo "<span style='color: green; font-weight: bold;'>\t$username</span> is available!";
		}
	}
?>