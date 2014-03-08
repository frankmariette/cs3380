<!DOCTYPE html>
<head>
	<title>Lab 2</title>
	<meta charset='UTF-8'>
	<style>
		/* corrects margin for text */
		p{
			margin-top:0px;
			margin-bottom:20px;
		}
	</style>
</head>
<body>
	<!-- Utilizes a combination of HTML and PHP to generate a query button that remains the same as the initial selection. Defaults to 1 if nothing selected -->
	<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
		<select name="value">
			<?php
			  for($i = 1;$i <= 12; ++$i)
			  {
			    if( $_POST['value'] == $i )
			      echo "<option value= \"$i\" selected>Query $i</option>";
			    else
			      echo "<option value= \"$i\">Query $i</option>";
			  }
			?>
		</select>
	<input type="submit" name="submit" value="Execute">
	</form>
	<br>
	<hr>
	<br>
	<?php
		include("../../secure/database.php");
		$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
			or die('Could not connect:' . pg_last_error());


		// Performs queries based on what user has selected. 
		if (isset($_POST['submit'])){
			switch ($_POST['value']){
			case 1:
				$query = 'SELECT name, region, continent, government_form 
		   				  FROM lab2.country 
		   				  WHERE surface_area >2000000 
		   				  ORDER BY gnp';
				break;
			case 2:
				$query = 'SELECT name, language, percentage 
						  FROM lab2.country AS co INNER JOIN lab2.country_language AS cl 
						  ON(co.country_code = cl.country_code) 
						  WHERE (percentage > 50.0) AND (is_official = false) 
						  ORDER BY percentage DESC';
				break;
			case 3:
				$query = 'SELECT DISTINCT language 
						  FROM lab2.country_language 
						  WHERE (percentage < 10.0) AND (is_official = true) 
						  ORDER BY language';
				break;
			case 4:
				$query = 'SELECT ci.name AS cityname, district, co.name AS countryname 
						  FROM lab2.city AS ci INNER JOIN lab2.country AS co 
						  ON(co.country_code = ci.country_code) 
						  ORDER BY co.name, ci.population DESC, ci.name';
				break;
			case 5:
				$query = 'SELECT co.name, ci.name AS capital , language, round((percentage/100 * co.population)::NUMERIC, 0) AS speakers 
						  FROM lab2.city AS ci, lab2.country AS co, lab2.country_language AS cl 
						  WHERE (is_official = true) AND (co.capital = ci.id) AND (co.country_code = cl.country_code) 
						  ORDER BY co.name, speakers DESC;';
				break;
			case 6:
				$query = 'SELECT name, district, population 
						  FROM lab2.city 
						  WHERE population > 3500000 
						  ORDER BY name';
				break;
			case 7:
				$query = 'SELECT ci.name AS cityName, district, co.name AS countryName 
					      FROM lab2.city AS ci, lab2.country AS co 
					      WHERE ci.name::text LIKE \'S%s\' AND ci.country_code = co.country_code';
				break;
			case 8:
				$query = 'SELECT DISTINCT name 
						  FROM lab2.country AS co, lab2.country_language AS cl 
						  WHERE (population > 10000000) AND is_official = false AND cl.country_code = co.country_code AND percentage > 20.0 
						  ORDER BY name';
				break;
			case 9:
				$query = 'SELECT name, indep_year, region, life_expectancy, gnp 
						  AS GNP, government_form 
						  FROM lab2.country 
						  ORDER BY indep_year 
						  LIMIT 5 OFFSET 2';
				break;
			case 10:
				$query = 'SELECT name, continent, region, indep_year, government_form, life_expectancy 
						  FROM lab2.country 
						  WHERE (continent != \'Africa\') 
						  ORDER BY life_expectancy 
						  LIMIT 20';
				break;
			case 11:
				$query = 'SELECT name, region, government_form, gnp, gnp_old, (gnp - gnp_old) AS Difference 
						  FROM lab2.country 
						  WHERE (gnp_old > gnp) 
						  ORDER BY (gnp_old-gnp)DESC';
				break;
			case 12:
				$query = 'SELECT name, round((gnp/population) * 1000000) AS per_capita_gnp, life_expectancy, government_form
						  FROM lab2.country 
						  WHERE population > 0
						  ORDER BY per_capita_gnp DESC';
				break;
			default:
				break;
			}


		// Generates table headers as well as returning the number of rows returned.  
		$result = pg_query($query) or die('Query failed:' . pg_last_error());
		echo "<p>There were <em>" . pg_num_rows($result) . "</em> rows returned.</p>";
		echo "<table border=\"1\">\n";
		echo "<tr>";
		$i = 0;
		while( $i < pg_num_fields($result)){
			$field_names = pg_field_name($result, $i);
			echo "<th>$field_names</th>";
			$i++;
		}
		echo "</tr>";

		// Generates and fills out table from array returned from $result
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
			echo "\t<tr>\n";
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
	else{
		// Provides a promt for user if nothing has been selected previously
		echo "<strong>Select a query to execute</strong>";
	}
	?>	

</body>
