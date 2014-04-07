<!DOCTYPE html>
<head>
	<title>Lab 6</title>
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
			  for($i = 1;$i <= 10; ++$i)
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
				$query = 'SELECT MIN(surface_area), MAX(surface_area), AVG(surface_area) FROM lab6.country;';
				break;
			case 2:
				$query = 'SELECT DISTINCT region, SUM(population) AS total_pop, SUM(surface_area) AS total_area, SUM(gnp) AS total_gnp FROM lab6.country GROUP BY region ORDER BY total_gnp DESC';
				break;
			case 3:
				$query = 'SELECT government_form, COUNT(government_form) AS count, MAX(indep_year) FROM lab6.country WHERE indep_year IS NOT NULL GROUP BY government_form ORDER by count DESC, max DESC;';
				break;
			case 4:
				$query = 'SELECT country.name, count(city.name) FROM lab6.country JOIN lab6.city USING (country_code) GROUP BY (country.name) HAVING count(city.name) > 100 ORDER BY count(city.name)';
				break;
			case 5:
				$query = 'SELECT co.name, country_population, urban_population, CAST(((urban_population/country_population)*100) AS FLOAT) AS urban_pct FROM 
                (SELECT country.name as name, max(country.population) AS country_population, CAST(SUM(city.population) AS FLOAT) AS urban_population
                FROM lab6.country JOIN lab6.city USING (country_code)
                GROUP BY(country.name))AS pop, lab6.country AS co WHERE pop.name = co.name
                ORDER BY urban_pct';
				break;
			case 6:
				$query = 'SELECT lc.name, ci.name AS largest_city, lc.population FROM (SELECT country.name AS name, MAX(city.population) AS population FROM lab6.country JOIN lab6.city USING (country_code) GROUP BY country.name) AS lc, lab6.city AS ci WHERE lc.population = ci.population ORDER BY lc.population DESC';
				break;
			case 7:
				$query = 'SELECT country.name, count(city.name) AS count FROM lab6.country JOIN lab6.city USING (country_code) GROUP BY (country.name) ORDER BY count DESC, country.name;';
				break;
			case 8:
				$query = 'SELECT co.name, capitals.name AS capital, count(language) AS lang_count FROM lab6.country AS co INNER JOIN (SELECT ci.name AS name,ci.country_code AS country_code FROM lab6.city AS ci, lab6.country AS co WHERE ci.id = co.capital)   AS capitals ON (capitals.country_code = co.country_code) INNER JOIN lab6.country_language AS cl ON (co.country_code = cl.country_code) GROUP BY co.name, capitals.name HAVING count(language) > 7 AND count(language) < 13 ORDER BY lang_count DESC';
				break;
			case 9:
				$query = 'SELECT co.name, ci.name,ci.population,  SUM(ci.population) OVER (PARTITION BY ci.country_code ORDER BY ci.population) AS running_total FROM lab6.country AS co, lab6.city AS ci WHERE ci.country_code = co.country_code ORDER BY co.name, running_total';
				break;
			case 10:
				$query = 'SELECT co.name, language, rank() OVER (PARTITION BY co.name ORDER BY percentage DESC) FROM lab6.country AS co, lab6.country_language AS cl WHERE cl.country_code = co.country_code';
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