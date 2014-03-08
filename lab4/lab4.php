<!DOCTYPE html>
<html>
<head>
	<title>Lab 4</title>
	<meta charset='UTF-8'>
	<style>
		table{
			padding: 0px;
		}
	</style>
	<script>
		function clickAction(form, pk, tbl, action)
		{
		  document.forms[form].elements['key'].value = pk;
		  document.forms[form].elements['action'].value = action;
		  document.forms[form].elements['tbl'].value = tbl;
		  document.getElementById(form).submit();
		}
		function clickLanguageAction(form, pk, tbl, action, language){
			document.forms[form].elements['key'].value = pk
			document.forms[form].elements['action'].value = action;
		  	document.forms[form].elements['tbl'].value = tbl;
		  	document.forms[form].elements['language'].value = language;
		  	document.getElementById(form).submit();
		}
</script>
</head>
<body>

	<!-- This generates a form. The form submits to itself to query the database and generate a table.
		 It also generates the initial input and maintains radio button selection between requests --> 
	<form action="<?= $_SERVER['PHP_SELF']?>" method="POST">
		<label>Search for a : </label>
		<?php
			if(isset($_POST['option']))
				$option = $_POST['option'];
			else
				$option = NULL;
			if($option == 'country'){
				echo "<input type=\"radio\" id=\"country\" name=\"option\" value=\"country\" checked>";
			}
			else{
				echo "<input type=\"radio\" id=\"country\" name=\"option\" value=\"country\">";
			}
			echo "<label for=\"country\">Country</label>";
			
			if ($option == 'city') {
				echo "<input type=\"radio\" id=\"city\" name=\"option\" value=\"city\" checked>";
			}
			else{
				echo "<input type=\"radio\" id=\"city\" name=\"option\" value=\"city\">";
			}
			echo "<label for=\"city\">City</label>";


			if ($option == 'language') {
				echo "<input type=\"radio\" id=\"language\" name=\"option\" value=\"language\" checked>";
			}
			else{
				echo "<input type=\"radio\" id=\"language\" name=\"option\" value=\"language\"> ";
			}
			echo "<label for=\"language\">Language</label>";
		?>
		<p>That begins with:
			<input type="text" id="search" name="search">
		</p>
		<input type="submit" name="submit" value="Submit">
	</form>
	<hr>
	<p>Or insert a new city with this <a href="exec.php?action=insert">link</a></p>
	<?php

		// Database connection
		include("../../secure/database.php");
			$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
				or die('Could not connect:' . pg_last_error());

		if(isset($_POST['submit'])){

			
			$search = $_POST['search'];
			$search = htmlspecialchars($search.'%');
			$where = 'name';
			if (!isset($_POST['option'])) {
				$_POST['option'] = null;
			}


			// Determines query execution based off of options selected and text inputted from the user. 
			if ($_POST['option'] == 'language') {
				$result = pg_prepare($conn, "query", "SELECT * FROM lab4.country_language WHERE language ILIKE $1 ORDER BY language");
				$result = pg_execute($conn, "query", array($search ) );
			}
			elseif ($_POST['option'] == 'city') {
				$result = pg_prepare($conn,  "query", "SELECT * FROM lab4.city WHERE name ILIKE $1 ORDER BY name");
				$result = pg_execute($conn, "query", array($search ) );
			}
			elseif ($_POST['option'] == 'country') {
				$result = pg_prepare($conn,  "query", "SELECT * FROM lab4.country WHERE name ILIKE $1 ORDER BY name");
				$result = pg_execute($conn, "query", array($search ) );
			}
			else{
				echo "<hr>";
				die();
			}


			echo "<hr>";
			echo "<p>There were <em>" . pg_num_rows($result) . "</em> rows returned.</p>";
			
			echo "<table border=\"1\">\n";
			echo "<tr>";
			echo "<th>Actions</th>";
			$i = 0;
			while( $i < pg_num_fields($result)){
				$field_names = pg_field_name($result, $i);
				echo "<th>$field_names</th>";
				$i++;
			}
			echo "</tr>";

			// Generates and fills out table from array returned from $result
			while ($line = pg_fetch_array($result, null, PGSQL_NUM)){
				echo "<form id=\"action_form\" method=\"POST\" action=\"exec.php\">";
				echo "\t<tr>\n";
				if ($option == 'language') {
					echo "<input type=\"hidden\" name=\"language\" value=\"$line[1]\" />";
					echo "<td><input type=\"submit\" name=\"action\" value=\"Edit\" onclick=\"clickLanguageAction('action_form', $line[0], $option, 'edit', $line[1]);\">";
					echo "<input type=\"submit\" name=\"action\" value=\"Remove\" onclick=\"clickLanguageAction('action_form', $line[0], $option, 'remove', $line[1]);\">";
				}
				else{
					echo "<td><input type=\"submit\" name=\"action\" value=\"Edit\" onclick=\"clickAction('action_form', $line[0], $option, 'edit');\">";
					echo "<input type=\"submit\" name=\"action\" value=\"Remove\" onclick=\"clickAction('action_form', $line[0], $option, 'remove');\">";
				}
				echo "<input type=\"hidden\" name=\"key\" value=\"$line[0]\" />";
				echo "<input type=\"hidden\" name=\"tbl\" value=\"$option\" />";
				echo "</form>";
				foreach ($line as $col_value) {
					echo "\t\t<td>$col_value</td>\n";
				}
				echo "\t</tr>\n";
			}
			echo "</table>\n";

			//Free result set
			pg_free_result($result);


			// Closes connection
			pg_close($conn);

		}

	?>

</body>
</html>