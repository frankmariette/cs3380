<?php
	session_start();
	require_once('dbconnect.php');

	if (!isset($_SESSION['username'])) {
		header('Location: ./index.php');
	}

	if (isset($_POST['submit'])) {
		$username = $_SESSION['username'];
		$description = $_POST['description'];

		$query = 'UPDATE lab8.user_info SET description = $1 WHERE username = $2';
		pg_prepare($conn, "description", $query);
		pg_execute($conn, "description", array($description, $username));
		header('Location: ./home.php');

	}


?>

<link rel="stylesheet" type="text/css" href="css/normalize.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap.css">


<div class="container">
	<form action="<?=$_SERVER['PHP_SELF'] ?>" method="post">
		<h3>Add description</h3>
		<textarea name="description" rows="4" cols="47"></textarea>
		<br>
		<div id="buttons">
			<span><button type="submit" name="submit" style="width:150px;" class="btn btn-success">Update description</button></span>
			<span><button type="button" style="width:150px;" class="btn btn-danger" onclick=" top.location.href='index.php'">Cancel</button></span>
		</div>
	</form>	
</div>
	