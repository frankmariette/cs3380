<html>
<head/>
<body>
<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
  <table border="1">
     <tr><td>Number of Rows:</td><td><input type="text" name="rows" /></td></tr>
     <tr><td>Number of Columns:</td><td><select name="columns">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="4">4</option>
    <option value="8">8</option>
    <option value="16">16</option>

  </select>
</td></tr>
   <tr><td>Operation:</td><td><input type="radio" name="operation" value="multiplication" checked="yes">Multiplication</input><br/>
  <input type="radio" name="operation" value="addition">Addition</input>
  </td></tr>
  </tr><td colspan="2" align="center"><input type="submit" name="submit" value="Generate" /></td></tr>
</table>
</form>



<?php
  if (isset($_POST['submit'])){
    if (is_numeric($_POST['rows']) && $_POST['rows'] > 0){ 
        echo "\n\nThe " . $_POST['rows'] . " x " . $_POST['columns'] . " " . $_POST['operation'] . " table:\n\n\n";
        echo "<br>";
        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<th>0</th>";
        for ($i = 1; $i <= $_POST['columns']; ++$i){
           echo "<th>$i</th>";
        }
        echo "</tr>";
        for ($j = 1; $j <= $_POST['rows']; ++$j){
          echo "<tr>";
          echo "<th>$j</th>";
          for ($i = 1; $i <= $_POST['columns']; ++$i){
            if ($_POST['operation']=="multiplication"){
              echo "<td align=\"center\">" . ($j * $i) . "</td>";
            }
            else if($_POST['operation']=="addition") {
              echo "<td align=\"center\">" . ($j + $i) . "</td>";
            }
           } 	   
           echo "</tr>";
        }
      }
    else {
      echo "Invalid rows and/or columns parameters";
    }
  }
?>
</body>
</html>
