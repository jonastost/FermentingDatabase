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
  border: 2px;
  padding: 5px;
}
th {
  text-align: left;
}
</style>
</head>
<body>
<?php
phpinfo();
$servername="localhost";
$username="jonastost";
$password="rachelrand";
$database="ferment_data";

//functions
function new_brewdata_table($brew_name_time, $sql) {
	$table = "CREATE TABLE " . $brew_name_time ."_data" . " (current_time charvar(255), timestamp charvar(255), current_temp int, average_temp int, rows int)";
	if($sql->query($table)) {
		return true;
	}
	return false;
}
function new_brewinfo_table($brew_name, $brew_type, $done, $rating, $bottled, $grains, $hops, $hops_times, $yeast, $sql) {
	$brew_time=0;
	while(!$sql->query("SELECT name  FROM " . $brew_name . "_" . $brew_time . "")) {
		$brew_time++;
	}
	$brew_name_time="" . $brew_name . "_" . $brew_time . "";
	$success = new_brewdata_table($brew_name_time, $sql);
	$table = "CREATE TABLE " . $brew_name_time . " (name charvar(255), type charvar(255), done BOOL, rating int, bottled bool, grains charvar(255), hops charvar(255), hops_times charvar(255), yeast charvar(255))";
	if(!mysqli_query($sql, $table) or !$success) {
		return  false;
	}
	//Create the strings for the grains, hops, and times.
	$table = "INSERT INTO " . $brew_name_time . "(name, type, done, rating, bottled, yeast) VALUES (".$brew_name.", ".$brew_type.", false, false,".$yeast.")";
	if(!$sql->query($table)) {
		return false;
	}
	foreach($grains as $type) {
		$table = "INSERT INTO ".$brew_name_time." (grains) VALUES (".$type.")";
		if(!$sql->query($table)) {
			return false;
		}
	}
	foreach($hops as $type) {
		$table = "INSERT INTO ".$brew_name_type." (hops) VALUES (".$type.")";
		if(!$sql->query($table)) {
			return false;
		}
	}
	foreach($hops_times as $type) {
		$table = "INSERT INTO ".$brew_name_time." (hops_times) VALUES (".$type.")";
		if(!$sql->query($table)) {
			return false;
		}
	}
	//Aded new brew to the total records table.
	$addnew = "INSERT INTO All_Records (brew_name_time, done) VALUES (".$brew_name_time.", false)";
	if(!$sql->query($addnew)) {
		return false;
	{
	return true;
}
function newbrew($brew_name, $brew_type, $grains, $hops, $hops_times. $yeast, $sql) {
	try {
		$success = new_brewinfo_table($brew_name, $brew_type, false, -1, false, $grains, $hops, $hops_times, $yeast);
	} catch (Exception $e) {
		echo "There was a problem with the creation of the tables. <br>";
		echo $e->getMessage();
	}
	if (!$success) {
		echo "There was a problem creating the tables, but no exception was thrown.";
	} else {
		echo "Everything is good! The new brew has been created.";
	}
}
function findbrew($brew_name_time, $sql, $open, $content) {
	if ($open) {
		echo "<table>";
		echo "<tr>";
		echo "<th>Name</th>";
		echo "<th>Type</th>";
		echo "<th>Grains</th>";
		echo "<th>Hops</th>";
		echo "<th>Times</th>";
		echo "<th>Yeast</th>";
		echo "</tr>";
	} else if ($content) {
		$find = "SELECT brew_name, brew_type, yeast FROM ". $brew_name_time."";
		$find2 = "SELECT grains, hops, hops_times FROM ".$brew_name_time."";
		if($result = $sql->query($find) and $result2 = $sql->query($find2)) {
			$row = mysqli_fetch_row($result);
			$grain_str = $hops_str = $hops_times_str = "";
			while ($row2 = mysqli_fetch_row($result2)) {
				$grain_str = $grain_str . $row2[0] . "<br>";
				$hops_str = $hops_str . $row2[1] . "<br>";
				$hops_times_str = $hops_times_str . $row2[2] . "<br>";
			}
			echo "<tr>";
			echo "<td>". $row[0] . "</td>";
			echo "<td>". $row[1] . "</td>";
			echo "<td>". $grain_str . "</td>";
			echo "<td>". $hops_str . "</td>";
			echo "<td>". $hops_times_str . "</td>";
			echo "<td>". $row[2] . "</td>";
			echo "</tr>";
		} else {
		echo "<span> This brew does not exist. Check the name and time and try again.<span>";
		}
	} else {
		echo "</table>";
	}
}
function currentbrews($sql) {
	$find = "SELECT brew_name_time FROM All_Records WHERE done=false";
	if($result=$sql->query($find)) {
		findbrew("open", $sql, true, false);
		while($row = mysqli_fetch_row($result)) {
			findbrew($row[0], $sql, false, true);
		}
		findbrew("close", $sql, false, false);
	} else {
		echo "<div>There is nothing currently in progress.</div>";
	}
}
function erase_brewdata($brew_name_time, $sql) {
	$drop = "DROP TABLE ".$brew_name_time;
	if(!$sql->query($drop)) {
		return false;
	}
	return true;
}
function allbrews($sql) {
	$find = "SELECT brew_name_time FROM All_Records WHERE done=true";
	if($result=$sql->query($find)) {
		findbrew("open", $sql, true, false);
		while($row = mysqli_fetch_row($result)) {
			findbrew($row[0], $sql, false, true);
		}
		findbrew("close", $sql, false, false);
	} else {
		echo "<div>There have been no past brews.</div>";
	}
}
function parse_recipe_str($recipe_str) {
	try {
		sscanf($recipe_str, "%s:%s:%s:%s:%s:%s", $A, $B, $C, $D, $E, $F);
		return array($A,$B,$C,$D,$E,$F);
	} catch (Exception $ex) {
		echo "<span>There was an error</span>";
	}
	return array("n","n","n","n","n","n");
}

echo "<span>We have gotten here.</span>";
//connect
$sql = new mysqli($servername, $username, $password, $database);
//check connection
if ($sql->connect_error){
  die("Connection error: " . $sql->connect_error);
}
$str = $_GET['str'];
$recipe_str = $_GET['recipe_str'];
echo "<span>The request was received</span>";
if ($str == "get_current") {
  currentbrews($sql);
} else if ($str == "get_past") {
  allbrews($sql);
} else if ($str == "create_new") {
  $ar = parse_recipe_str($recipe_str);
  if($ar[2] != "n") {
  	try {
  		sscanf($ar[2], "%s, %s, %s, %s, %s", $A, $B, $C, $D, $E);
		$ar[2] = array($A, $B, $C, $D, $E);
  		sscanf($ar[3], "%s, %s, %s, %s, %s", $A, $B, $C, $D, $E);
  		$ar[3] = array($A, $B, $C,$D, $E);
		sscanf($ar[4], "%s, %s, %s, %s, %s", $A, $B, $C, $D, $E);
		$ar[4] = array($A, $B, $C, $D, $E);
  		$worked = new_brew($ar[0],$ar[1],$ar[2],$ar[3],$ar[4],$ar[5],$sql);
		if (!$worked) {
			throw Exception("<span>The creation of the tables has failed.</span>");
		}
  	} catch (Exception $ex) {
  		echo "<span>Something went wrong!</span>";
		echo $ex->getMessage();
  	}
  }
} else {
  echo "<span>The string code that was requested is not currently available.</span>";
}

mysqli_close($sql);
?>
</body>
</html>

