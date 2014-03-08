<!DOCTYPE html>
<head>
	<title>Lab 4</title>
	<meta charset="utf-8">
</head>


<?php
	include("../../secure/database.php");
		$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
			or die('Could not connect:' . pg_last_error());


	// This is the insert city page. Asks the user for information and sanitizes
	// the input. This proceeds to post the information to the same page. 
	if(isset($_GET['action'])){
		if ($_GET['action'] == 'insert'){
			$list = 'SELECT name, country_code FROM lab4.country ORDER BY name;';
			$listresult = pg_prepare($conn, "list", $list);
			$listresult = pg_execute($conn, "list", array());
		
			echo "<form method=\"POST\" action=\"exec.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"insert\">";
			echo "<table border=\"1\">";
			echo	"<tr>";
			echo		"<td>Name</td>";
			echo		"<td><input type=\"text\" name=\"name\"></td>";
			echo	"</tr>";
			echo	"<tr>";
			echo		"<td>Country Code</td>";
			echo		"<td>";
			echo			"<select name=\"country_code\">";
				while($line = pg_fetch_array($listresult, null, PGSQL_ASSOC)){
					echo "<option value=\"".$line['country_code']."\">".$line['name']."</option>";
				}
			echo		"</select>";
			echo		"</td>";
			echo 	"</tr>";
			echo	"<tr>";
			echo		"<td>District</td>";
			echo		"<td><input type=\"text\" name=\"district\"></td>";
			echo	"</tr>";
			echo 	"<tr>";
			echo		"<td>Population</td>";
			echo		"<td><input type=\"text\" name=\"population\"></td>";
			echo	"</tr>";
			echo "</table>";
		echo "<input type=\"submit\" name=\"submit\" value=\"Save\">";
		echo "<input type=\"button\" value=\"Cancel\" onclick=\"top.location.href='lab4.php'\">";
		echo "</form>";
		
			}
	}

	// Only called if a POST request is sent to the script
	else if(isset($_POST['action'])) {

		// Inserts a city and sanitizes the input before placing in the DB.
		if ($_POST['action'] == 'insert') {
			$name = htmlspecialchars($_POST['name']);
			$country_code = htmlspecialchars($_POST['country_code']);
			$district = htmlspecialchars($_POST['district']);
			$population = htmlspecialchars($_POST['population']);

			$query = 'INSERT INTO lab4.city (name, country_code, district, population) VALUES (\''.$name.'\', \''.$country_code.'\', \''.$district.'\', '.$population.');';
			pg_prepare($conn, "insert", $query);

			if (pg_execute($conn, "insert", array())) {
				echo "Insert successful <br/>";
				echo "Return to <a href=\"lab4.php\">search</a>";
			}
			else{
				echo "Insert unsuccessful <br/>";
				echo "Return to <a href=\"lab4.php\">search</a>";
			}


		}
	// This is the script that is called when the edit link is clicked from lab4.php. It allows 
	// the user to edit a small amount of information and then sanitizes the input. 
	else if ($_POST['action'] == 'Edit'){

		if ($_POST['tbl'] == 'language'){
			$table = 'country_language';
		}
		else{
			$table = $_POST['tbl'];
		}
		$key = $_POST['key'];
		$language = null;

		if (isset($_POST['submit'])) {

			if ($table == 'country') {
				$population = htmlspecialchars($_POST['population']);
				$life_expectancy = htmlspecialchars($_POST['life_expectancy']);
				$gnp = htmlspecialchars($_POST['gnp']);
				$head_of_state = htmlspecialchars($_POST['head_of_state']);

				$query = 'UPDATE lab4.'.$table.' SET population='.$population.', life_expectancy='.$life_expectancy.', gnp='.$gnp.', head_of_state=\''.$head_of_state.'\' WHERE (country_code = \''.$key.'\');';
				pg_prepare($conn, "update", $query);
				if (pg_execute($conn, "update", array())) {
					echo "Update successful <br\>";
					echo "Return to <a href=\"lab4.php\">search</a>";
				}
				return;
			}
			else if ($table == 'country_language') {
				$language = $_POST['language'];
				$is_official = htmlspecialchars($_POST['is_official']);
				$percentage = htmlspecialchars($_POST['percentage']);
				$query = 'UPDATE lab4.'.$table.' SET is_official=\''.$is_official.'\' WHERE (country_code = \''.$key.'\');';
				pg_prepare($conn, "update", $query);
				if (pg_execute($conn, "update", array())) {
					echo "Update successful <br/>";
					echo "Return to <a href=\"lab4.php\">search</a>";
				}
				return;
			}
			else {
				$population = htmlspecialchars($_POST['population']);
				$query = 'UPDATE lab4.city SET population = '.$population.' WHERE (id='.$key.');';
				pg_prepare($conn, "update", $query);
				if (pg_execute($conn, "update", array())) {
					echo "Update successful <br />";
					echo "Return to <a href=\"lab4.php\">search</a>";
					return;
				}
				else{
					echo "Update unsuccessful <br\>";
					echo "Return to <a href=\"lab4.php\">search</a>";
					return;
				}

			}
		}


			if($table == 'country'){
				$query = 'SELECT * FROM lab4.'.$table.' WHERE (country_code = \''.$key.'\');';
				$result = pg_prepare($conn, "query", $query);
				$result = pg_execute($conn, "query", array());
			}
			else if($table == 'country_language') {
				$language = $_POST['language'];
				$query = 'SELECT * FROM lab4.'.$table.' WHERE (country_code = \''.$key.'\') AND (language=\''.$language.'\');';
				$result = pg_prepare($conn, "query", $query);
				$result = pg_execute($conn, "query", array());
			}
			else{ // City
				$query = 'SELECT * FROM lab4.city WHERE id = '.$key.';';
				$result = pg_prepare($conn, "query", $query);
				$result = pg_execute($conn, "query", array());
			}
			

			$key = $_POST['key'];
			$tbl = $_POST['tbl'];
			$action = $_POST['action'];
			?>
			
			<form action="exec.php" method="POST">

			<?php
			echo "<table border=\"1\">";
			echo "<input type=\"hidden\" name=\"key\" value=\"$key\">";
			echo "<input type=\"hidden\" name=\"tbl\" value=\"$tbl\">";
			if ($language) {
				echo "<input type=\"hidden\" name=\"language\" value=\"$language\">";
			}
			echo "<input type=\"hidden\" name=\"action\" value=\"Edit\">";

			$i = 0;
			while (($line = pg_fetch_array($result, null, PGSQL_ASSOC)) && ($i < pg_num_fields($result))){
				foreach ($line as $col_value) {
					$field_names = pg_field_name($result, $i);
					if ($field_names == 'population'){
						echo "<tr>";
						echo "<td><strong>$field_names</strong></td>";
						echo "<td><input type=\"text\" value=\"$col_value\" name=\"$field_names\"></td>";
						$i++;
					}
					elseif ($field_names == 'life_expectancy'){
						echo "<tr>";
						echo "<td><strong>$field_names</strong></th>";
						echo "<td><input type=\"text\" value=\"$col_value\" name=\"$field_names\"></td>";
						$i++;
					}
					elseif ($field_names == 'gnp'){
						echo "<tr>";
						echo "<td><strong>$field_names</strong></th>";
						echo "<td><input type=\"text\" value=\"$col_value\" name=\"$field_names\"></td>";
						$i++;
					}
					elseif ($field_names == 'head_of_state'){
						echo "<tr>";
						echo "<td><strong>$field_names</strong></td>";
						echo "<td><input type=\"text\" value=\"$col_value\" name=\"$field_names\"></td>";
						$i++;
					}
					elseif ($field_names == 'percentage') {
						echo "<tr>";
						echo "<td><strong>$field_names</strong></td>";
						echo "<td><input type=\"text\" value=\"$col_value\" name=\"$field_names\"></td>";
						$i++;
					}
					elseif ($field_names == 'is_official') {
						echo "<tr>";
						echo "<td><strong>$field_names</strong></td>";
						echo "<td><input type=\"text\" value=\"$col_value\" name=\"$field_names\"></td>";
						$i++;
					}
					else {
						echo "<tr>";
						echo "<td>$field_names</td>";
						echo "<td>$col_value</td>";
						$i++;
					}
				}
			}
			echo "</table>";
			echo "<input type=\"submit\" value=\"Save\" name=\"submit\">";
			echo "<input type=\"button\" value=\"Cancel\" onclick=\"top.location.href='lab4.php';\">";
			echo "</form>";



		}
		// This is the remove function. The else case is a workaround for city parsing in
		// "[object HTMLInputElement]". 
		else if ($_POST['action'] == 'Remove') {
			$table = $_POST['tbl'];
			$key = $_POST['key'];

			if ($table == 'country') {
				$query = 'DELETE FROM lab4.country WHERE (country_code = \''.$key.'\');';	
			}
			else if($table == 'language'){
				$language = $_POST['language'];
				$query = 'DELETE FROM lab4.country_language WHERE (country_code = \''.$key.'\') AND (language=\''.$language.'\');';
			}
			else {
				$query = 'DELETE FROM lab4.city WHERE (id = '.$key.')';
			}

			
			pg_prepare($conn, "delete", $query);
			
			if(pg_execute($conn, "delete", array())){
				echo "Delete was successful<br/>";
				echo "Return to <a href=\"lab4.php\">search</a>";
			}
			else{
				echo "Delete unsuccessful<br/>";
				echo "Return to <a href=\"lab4.php\">search</a>";
			}


		}


		else{
			print_r($_POST);
			echo "No action found";
		}
	}
?>