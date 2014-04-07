<?php
	require_once('dbconnect.php');
	// Makes sure user sends login info via HTTPS
	if($_SERVER['SERVER_PORT'] !== 443 &&
	   (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
	  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	  exit;
	}

	session_start();
	
	if(isset($_POST['submit'])){
		$username = htmlspecialchars($_POST['username']);
		mt_rand();
		$salt = sha1(mt_rand());
		$pwd_hash = sha1($salt . htmlspecialchars($_POST['password']));

		$user_info = "INSERT INTO lab8.user_info(username) VALUES ($1) ";
		$authentication = "INSERT INTO lab8.authentication(username, password_hash, salt) VALUES($1, $2, $3)";

		pg_prepare($conn, "user_info", $user_info);
		pg_prepare($conn, "authentication", $authentication);

		pg_execute($conn, "user_info", array($username));
		pg_execute($conn, "authentication", array($username, $pwd_hash, $salt));

		$_SESSION['username'] = $username;
		$ip_address = $_SERVER["REMOTE_ADDR"];
		$action = 'register';

		$query = 'INSERT INTO lab8.log(username, ip_address, action) VALUES($1, $2, $3)';
		pg_prepare($conn, "log", $query);
		pg_execute($conn, "log", array($username, $ip_address, $action));
	}

	if(isset($_SESSION['username'])){
		header('Location: ./home.php');
	}


?>

<link rel="stylesheet" type="text/css" href="css/normalize.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
<script type="text/javascript" src="js/jquery-1.11.0.js"></script>
<style type="text/css">
	div.container {
		clear: both;
		margin: auto;
		position: relative;
		width: 600px;
	}

	label{
		width: 150px;
		float: left;
	}
	h1{
		margin-bottom: 10px;
	}
	div{
		margin: 15px;
	}
</style>
<!-- AJAX call to check if a username is available -->
<script type="text/javascript">
	$(document).ready(function(){
		console.log(form.username.value);
		$('#feedback').load('checker.php').show();
		$('#user').keyup(function(){
			$.post('checker.php', {username: form.username.value},
				function(result){
					$('#feedback').html(result).show();
				});
		});
	});
</script>

<div class="container">
	<h1>Registration Page</h1>
	<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" name="form">
		<div class="username">
			<label for="user">Username</label>
			<input type="text" id="user" placeholder="Username" name="username" required><span id="feedback">  </span>
			
		</div>
		<div class="password">
			<label for="pass">Password</label>
			<input type="password" name="password" id="pass" placeholder="Password" required>
		</div>
		<div class="confirmation">
			<label for="confirmation">Confirm Password</label>
			<input type="password" id="confirmation" placeholder="Confirm Password" required>
		</div>
		<div id="buttons">
			<span><button type="submit" name="submit" style="width:150px;" class="btn btn-success">Create account</button></span>
			<span><button type="button" style="width:150px;" class="btn btn-danger" onclick=" top.location.href='index.php'">Cancel</button></span>
		</div>
	</form>
</div>