<?php
	//Makes sure user sends login info via HTTPS
	if($_SERVER['SERVER_PORT'] !== 443 &&
	   (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
	  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	  exit;
	}
	
	session_start();
	require_once('dbconnect.php');


	if (isset($_POST['submit'])) {
		$username = htmlspecialchars($_POST['username']);
		$query = 'SELECT * FROM lab8.authentication WHERE username = $1';
		pg_prepare($conn, "validate", $query);
		$result = pg_execute($conn, "validate", array($username));
		$result = pg_fetch_array($result, null, PGSQL_ASSOC);

		$pwd_hash = sha1($result['salt'] . htmlspecialchars($_POST['password']));


		if ($pwd_hash == $result['password_hash']){
			$_SESSION['username'] = $username;
			$ip_address = $_SERVER["REMOTE_ADDR"];
			$action = 'login';

			$query = 'INSERT INTO lab8.log(username, ip_address, action) VALUES($1, $2, $3)';
			pg_prepare($conn, "log", $query);
			pg_execute($conn, "log", array($username, $ip_address, $action));
		}
		else{
			echo "<span style='color: red;'>Bad username/password combination</span>";
		}

	}
	if(isset($_SESSION['username'])){

		header('Location: ./home.php');
	}

?>
<link rel="stylesheet" type="text/css" href="css/normalize.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<style type="text/css">
	body{
		text-align: center;
	}
	input{
		margin: 10px;
	}
	label {
		margin-left: 10px;
	}
	button a {
		color: white;
	}
	button a:hover {
		color: white;
		text-decoration: none;
	}
</style>


<div class="container">
	<h1>Database Lab 8</h1>
	<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<label for="user">User</label>
		<input type="text" name="username" placeholder="Username">
		<label for="pass">Password</label>
		<input type="password" name="password" placeholder="Password">
		<div class="container">
			<button type="submit" name="submit" class="btn btn-primary">Login</button>
			<button type="button" class="btn btn-success"><a href="./registration.php">Register now!</a></button>
		</div>
		
	</form>
</div>