<?php
	require_once('dbconnect.php');
	session_start();


	// Displays info for when user registered. 
	$registered = "SELECT log_date, ip_address FROM lab8.log WHERE username = $1 AND action = 'register'";
	pg_prepare($conn,"registered", $registered );
	$registered = pg_execute($conn, "registered", array($_SESSION['username']));
	$registered = pg_fetch_array($registered, null, PGSQL_ASSOC);

	// Displays descriptiong for current logged in user
	$description = "SELECT description FROM lab8.user_info WHERE username = $1";
	pg_prepare($conn, "description", $description);
	$description = pg_execute($conn, "description", array($_SESSION['username']));
	$description = pg_fetch_array($description, null, PGSQL_ASSOC);

	// Handles log data for logins
	$logins = "SELECT * FROM lab8.log WHERE username = $1";
	pg_prepare($conn, "logins", $logins);
	$logins = pg_execute($conn, "logins", array($_SESSION['username']));
	$numFields = pg_num_fields($logins);
	$numRows = pg_num_rows($logins);
	$logins = pg_fetch_array($logins, null, PGSQL_ASSOC);

?>
<link rel="stylesheet" type="text/css" href="css/normalize.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<style type="text/css">
	body{
		text-align: center;
	}
</style>

<h1>Welcome <?=$_SESSION['username']?></h1>

<p>You registered on <strong><?=$registered['log_date']?></strong> with this IP address <strong><?=$registered['ip_address']?></strong></p>
<p>Description: <?=$description['description']?></p>

<div class="container">
	<table name="logins" class="table">
	<tr><th>IP Address</th><th>Log Date</th><th>Action</th></tr>
		<?php
			// Generates the table for the login info.
			for($i = 0; $i < $numRows; $i++){
				echo "<tr>";
				echo "<td>".$logins['ip_address']."</td>";
				echo "<td>".$logins['log_date']."</td>";
				echo "<td>".$logins['action']."</td>";
				echo "</tr>";
			}
		?>
	</table>
</div>

<button type="button" onclick="top.location.href='update.php'" class="btn btn-primary">Update</button>
<button type="button" onclick="location.href='logout.php'" class="btn btn-danger">Logout</button>






