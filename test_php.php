<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
table {
  width:100%;
  border-collapse:collapse;
}
table, td, th {
  border: 2px solid black;
  padding: 5px;
}
th {
  text-align: left;
}
</style>
</head>
<body>

<?php
$servername="localhost";
$username="jonastost";
$password="rachelrand";
$database="ferment_data";
$sql = mysqli_connect($servername, $username, $password, $database);

if (!$sql) {
  die('Connection error: ' . mysqli_error($sql));
}
function new_brewdata_table($brew_name_time, $mode, $sql) {
  if ($mode == "fridge" or $mode == "time") {
    $table = "CREATE TABLE " . $brew_name_time."_data (time VARCHAR(255), timestamp VARCHAR(255), current_temp INT, average_temp INT, row INT)";
  } else {
    $table = "CREATE TABLE ".$brew_name_time."_data (time VARCHAR(255), timestamp VARCHAR(255), row INT)";
  }
  if (!$sql->query($table)) {
    echo mysqli_error($sql);
    return false;
  }
  echo mysqli_error($sql);
  return true;
}
function quoted($vari) {
  $vari = "'".$vari;
  $vari .= "'";
  return $vari;
}
function new_brewinfo_table($brew_name, $brew_type, $done, $rating, $bottled, $grains, $hops, $hops_times, $yeast, $modes, $sql) {
  $brew_time = 0;
  while (!empty($sql->query("SELECT name FROM ".$brew_name."_".$brew_time))) {
    $brew_time++;
  }
  try {
    $sql->query("CREATE TABLE ".$brew_name."_".$brew_time." (name VARCHAR(255), type VARCHAR(255), done BOOLEAN, rating TINYINT(10), bottled BOOLEAN, grains VARCHAR(255), hops VARCHAR(255), hops_times VARCHAR(255), yeast VARCHAR(255))");
  } catch (Exception $e) {
    echo "There was an error in creating the tables";
  }
  echo strval($brew_time);
  $brew_name_time = $brew_name."_".strval($brew_time);
  $success = new_brewdata_table($brew_name_time, $modes, $sql);
  $brew_name_time_q = quoted($brew_name_time);
  $yeast_q = quoted($yeast);
  $brew_type_q = quoted($brew_type);
  $modes_q = quoted($modes);
  $strin1 = $strin2 = $strin3 = "";
  foreach ($grains as $type) {
    $strin1 .= $type . "<br>";
  }
  foreach ($hops as $type) {
    $strin2 .= $type . "<br>";
  }
  foreach ($hops_times as $type) {
    $strin3 .= $type . "<br>";
  }
  $strin1 = quoted($strin1);
  $strin2 = quoted($strin2);
  $strin3 = quoted($strin3);

  $table = "INSERT INTO ".$brew_name_time." (name, type, done, rating, bottled, grains, hops, hops_times, yeast) VALUES (".$brew_name_time_q.", ".$brew_type_q.", false, 1, false, ".$strin1.", ".$strin2.", ".$strin3.", ".$yeast_q.")";
  if (!$sql->query($table) or !$success) {
   echo mysqli_error($sql);
   return false;
  }


  //Add new brew to the records table.
  $table = "INSERT INTO All_Records (brew_name_time, done, mode) VALUES (".$brew_name_time_q.", false, ".$modes_q.")";
  if (!$sql->query($table)) {
    echo mysqli_error($sql);
    return false;
  }
  return true;
}
function newbrew($brew_name, $brew_type, $grains, $hops, $hops_times, $yeast, $mode, $sql) {
  try {
    $success = new_brewinfo_table($brew_name, $brew_type, false, 1, false, $grains, $hops, $hops_times, $yeast, $mode, $sql);
    if (!$success) {
      echo "There was a problem creating the tables, but no exceptions were thrown. <br>";
    } else {
      echo "The tables were created. Update the tables below to see changes";
      return true;
    }
  } catch (Exception $e) {
    echo "An error has occurred: ";
    echo $e->getMessage();
  }
  return false;
}
function openTable() {
  echo "<table>";
  echo "<tr>";
  echo "<th>Name</th>";
  echo "<th>Type</th>";
  echo "<th>Grains</th>";
  echo "<th>Hops</th>";
  echo "<th>Times</th>";
  echo "<th>Yeast</th>";
  echo "<th>Start Time</th>";
  echo "<th>Time Brewing</th>";
  echo "</tr>";
  return;
}
function closeTable() {
  echo "</table>";
  return;
}
function findBrew($brew_name_time, $sql) {
  $find = "SELECT name, type, yeast FROM ".$brew_name_time;
  $find2 = "SELECT grains, hops, hops_times FROM ".$brew_name_time;
  if ($result=$sql->query($find) && $result2=$sql->query($find2)) {
    $result = $sql->query($find);
    $name = $yeast = $type = "";
    while ($row = mysqli_fetch_row($result)) {
      $name .= $row[0];
      $type .= $row[1];
      $yeast .= $row[2];
    }
    if ($name == "") {
      echo "yes";
    }
    $grain_str = $hops_str = $times_str = "";
    while ($row2=mysqli_fetch_row($result2)) {
      $grain_str .= $row2[0]."<br>";
      $hops_str .= $row2[1]."<br>";
      $times_str .= $row2[2]."<br>";
    }
    echo "<tr>";
    echo "<td>".$name."</td>";
    echo "<td>".$type."</td>";
    echo "<td>".$grain_str."</td>";
    echo "<td>".$hops_str."</td>";
    echo "<td>".$times_str."</td>";
    echo "<td>".$yeast."</td>";
    echo "<td>"."</td>";
    echo "<td>"."</td>";
    echo "</tr>";
  } else {
    echo "This brew does not exist. Check the name and time.";
  }
  return;
}
function currentbrews($sql) {
  $find="SELECT brew_name_time FROM All_Records WHERE done=false";
  if ($result = $sql->query($find)) {
    echo mysqli_error($sql);
    opentable();
    while($row=mysqli_fetch_row($result)) {
      echo mysqli_error($sql);
      findbrew($row[0], $sql);
    }
    closetable();
  } else {
    echo "There is nothing currently in progress.";
  }
  return;
}
function erase_brewdata($brew_name_time, $sql) {
  $drop = "DROP TABLE ".$brew_name_time;
  $drop2 = "DROP TABLE ".$brew_name_time."_data";
  if(!$sql->query($drop) && !$sql->query($drop2)) {
    return false;
  }
  return true;
}
function pastbrews($sql) {
  $find = "SELECT brew_name_time FROM All_Records WHERE done=true";
  if ($result=$sql->query($find)) {
    opentable();
    while ($row=mysqli_fetch_row($result)) {
      findbrew($row[0], $sql);
    }
    closetable();
  } else {
    echo "There have been no past brews.";
  }
}
function parse_recipe_str($recipe_str) {
  try {
    $ar = explode(",", $recipe_str, 19);
    for ($i=0; $i < sizeof($ar); $i++) {
      $ar[$i] = str_replace(" ", "_", $ar[$i]);
    }
    return $ar;
  } catch (Exception $e) {
    echo "An Exception was thrown during parsing";
  }
  return array("n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n", "n");
}
$str = $_GET['str'];
$recipe_str = $_GET['recipestr'];

if($str == "get_current") {
  currentbrews($sql);
} else if ($str == "get_past") {
  pastbrews($sql);
} else if ($str == "create_new") {
  echo $recipe_str;
  $ar = parse_recipe_str($recipe_str);
  if($ar[2] != "n") {
    try {
      echo $ar[17];
      $grain = array($ar[2], $ar[3], $ar[4], $ar[5], $ar[6]);
      $hops = array($ar[7], $ar[8], $ar[9], $ar[10], $ar[11]);
      $times = array($ar[12], $ar[13], $ar[14], $ar[15], $ar[16]);
      $worked = newbrew($ar[0], $ar[1], $grain, $hops, $times, $ar[17], $ar[18], $sql);
      if (!$worked) {
        throw new Exception("An error occurred during the creation of tables");
      }
    } catch (Exception $e) {
      echo "Something went wrong";
      echo $e->getMessage();
    }
  }
} else {
  echo "The strings did not match any commands on record.<br>";
}

mysqli_close($sql);
?>

</body>
</html>
