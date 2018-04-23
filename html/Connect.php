<?php

$base = $_SERVER['DOCUMENT_ROOT'];
require_once $base . '/core/init.php';

function ConnectDatabase() {
  
  $servername = Config::get('mysql/host');
  $username = Config::get('mysql/username');
  $password = Config::get('mysql/password');
  
  // Create connection
  $conn = new mysqli($servername, $username, $password);

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  //echo "<br>Connected successfully";

  return $conn;

}
  
function ChooseDatabase($conn) {
  
    $sql = "USE " . Config::get('mysql/db');

  if ($conn->query($sql) === TRUE) {
      //echo "<br>Database selected";
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }
  return $conn;
}

function InsertValue($conn, $Name = "NULL", $City = "NULL", $State = "NULL", $StartDate= "NULL", $EndDate= "NULL",
                     $URL= "NULL", $Map = "NULL", $Rating= "NULL", $Difficulty= "NULL") {

  $sql = "INSERT INTO Resort (ResortName, City, State, SeasonStartDate, SeasonEndDate, ImageURL, ImageMap, rating, difficulty) VALUES 
         ('" . $Name . "', '" . $City . "', '" . $State . "', '" . $StartDate . "', '" . $EndDate .
         "', '" . $URL . "', '" . $Map . "', " . $Rating . ", " . $Difficulty . ")";

  //printf("<br>Here is the insertion string %s", $sql);
  if ($conn->query($sql) === TRUE) {
    $returnVal = "New record created successfully.";
  } else {
    $returnVal = "Error: " . $sql . "<br>" . $conn->error;
  }
  return $returnVal;
}

function FormSelectResort($Name, $City, $State, $StartDate, $EndDate, $URL, $Map, $Rating, $Difficulty) {

  $sql = "SELECT * FROM Resort";

  $where = "";
  $where = FormWhereSection($where, "ResortName", $Name, 1, 1);
  $where = FormWhereSection($where, "City", $City, 0, 1);
  $where = FormWhereSection($where, "State", $State, 0, 1);
  $where = FormWhereSection($where, "SeasonStartDate", $StartDate, 0, 1);
  $where = FormWhereSection($where, "SeasonEndDate", $EndDate, 0, 1);
  $where = FormWhereSection($where, "ImageURL", $URL, 0, 1);
  $where = FormWhereSection($where, "ImageMap", $Map, 0, 1);
  $where = FormWhereSection($where, "rating", $Rating, 0, 1);
  $where = FormWhereSection($where, "Difficulty", $Difficulty, 0, 1);
    
  //printf("<br> Here is the where clause %s",$where);  

  if(strlen($where) != 0) {
    $sql = $sql . " WHERE " . $where;
  }
  return $sql;
}

function FormUpdateResort() {

// Form the WHERE clause here
$where = "";
$where = FormWhereSection($where, "ResortName", $_GET["oldResortName"], 1, 1);
$where = FormWhereSection($where, "City", $_GET["oldCity"], 0, 1);
$where = FormWhereSection($where, "State", $_GET["oldState"], 0, 1);
$where = FormWhereSection($where, "SeasonStartDate", $_GET["oldStartDate"], 0, 1);
$where = FormWhereSection($where, "SeasonEndDate", $_GET["oldEndDate"], 0, 1);
$where = FormWhereSection($where, "ImageURL", $_GET["oldURL"], 0, 1);
$where = FormWhereSection($where, "ImageMap", $_GET["oldMap"], 0, 1);
$where = FormWhereSection($where, "rating", $_GET["oldRating"], 0, 1);
$where = FormWhereSection($where, "Difficulty", $_GET["oldDifficulty"], 0, 1);

// Form the SET clause here
$set = "";
$set = FormWhereSection($set, "ResortName", $_GET["newResortName"], 1, 1);
$set = FormWhereSection($set, "City", $_GET["newCity"], 0, 1);
$set = FormWhereSection($set, "State", $_GET["newState"], 0, 1);
$set = FormWhereSection($set, "SeasonStartDate", $_GET["newStartDate"], 0, 1);
$set = FormWhereSection($set, "SeasonEndDate", $_GET["newEndDate"], 0, 1);
$set = FormWhereSection($set, "ImageURL", $_GET["newURL"], 0, 1);
$set = FormWhereSection($set, "ImageMap", $_GET["newMap"], 0, 1);
$set = FormWhereSection($set, "rating", $_GET["newRating"], 0, 1);
$set = FormWhereSection($set, "Difficulty", $_GET["newDifficulty"], 0, 1);

$sql = "UPDATE Resort SET " . $set . " WHERE " . $where;

printf("<br>The update command is:  %s",$sql);

return $sql;

}

function FormDeleteResort($Name, $City, $State, $StartDate, $EndDate, $URL, $Map,  $Rating, $Difficulty) {

  $where = "";

  // Form the WHERE clause here
  $where = FormWhereSection($where, "ResortName", $Name, 1, 1);
  $where = FormWhereSection($where, "City", $City, 0, 1);
  $where = FormWhereSection($where, "State", $State, 0, 1);
  $where = FormWhereSection($where, "SeasonStartDate", $StartDate, 0, 1);
  $where = FormWhereSection($where, "SeasonEndDate", $EndDate, 0, 1);
  $where = FormWhereSection($where, "ImageURL", $URL, 0, 1);
  $where = FormWhereSection($where, "ImageMap", $Map, 0, 1);
  $where = FormWhereSection($where, "rating", $Rating, 0, 1);
  $where = FormWhereSection($where, "Difficulty", $Difficulty, 0, 1);

  $sql = "DELETE FROM Resort WHERE " . $where;

  return $sql;

}

function PerformQuery($conn, $sql) {
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
      // Skeleton for the form

      echo "<table class=\"table\" style=\"width:40%\">";
      echo "<thead>";
      echo "<tr>";
      echo "<th scope=\"col\">Name</th>";
      echo "<th scope=\"col\">City</th>";
      echo "<th scope=\"col\">State</th>";
      echo "<th scope=\"col\">Start of Season Date</th>";
      echo "<th scope=\"col\">End of Season Date</th>";
      echo "<th scope=\"col\">Image URL</th>";
      echo "<th scope=\"col\">Trail URL</th>";
      echo "<th scope=\"col\">Rating</th>";
      echo "<th scope=\"col\">Difficulty</th>";
      echo "<th scope=\"col\">View pricing</th>";
      echo "<th scope=\"col\">Delete?</th>";
      echo "</tr>";
      echo "<thead>";
      // output data of each row
      echo "<tbody>";
      while($row = $result->fetch_assoc()) {
        echo "<form method=\"get\" action=\"/search.php\">";
        echo "<td> <input type=\"text\" name=\"resortName\" value=\"" . $row["ResortName"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"city\" value=\"" . $row["City"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"state\" value=\"" . $row["State"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"startDate\" value=\"" . $row["SeasonStartDate"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"endDate\" value=\"" . $row["SeasonEndDate"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"URL\" value=\"" . $row["ImageURL"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"Map\" value=\"" . $row["ImageMap"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"rating\" value=\"" . $row["rating"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"difficulty\" value=\"" . $row["Difficulty"] . "\"></td>";
        echo "<td> <a href=\"prices.php?ResortName=" . escape($row["ResortName"]) . "\">" . "Price" . "</a></td>";
        echo "<td> <input type=\"hidden\" name=\"delete\" value=\"true\"> <input type=\"submit\" value=\"Delete\"></td>";
        echo "</tr>";
        echo "</form>";
      }
      echo "</tbody>";
      echo "</table>";

  } else {
      echo "Sorry, no results found";
  }

} 

function FormWhereSection($where, $varName, $value, $startOfWhere, $stringVal) {

    // Form the WHERE clause here
    if (strlen($value) != 0) {
        if(!$startOfWhere && strlen($where) != 0)
            $where = $where . "AND ";
        if($stringVal)
            $where = $where .  $varName . " = \"" . $value . "\" ";
        else
            $where = $where .  $varName . " = " . $value;
    }
    return $where;
}

function GetUserInfo($conn,$username,$password) {
    
    $sql = "SELECT * FROM UserInfo WHERE username =\"" .  $username . "\" AND password = \"" . $password . "\"";
    //printf("<br> The userinfo sql command is %s",$sql);
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //printf("<br> Found user info");
        return $result->fetch_assoc();
    }
    else {
        //printf("<br> Missed user info");
        return array ();
    }
}


/*function CheckUserInfo($conn,$username) {
 printf("<br> Checking user info here");
 $sql = "SELECT * FROM UserInfo WHERE username =\"" .  $username . "\"";
 printf("<br> The userinfo sql command is %s",$sql);
 $result = $conn->query($sql);
 if ($result->num_rows > 0) {
 printf("<br> Found user info");
 return 1;
 }
 else {
 printf("<br> Userinfo not found");
 return 0;
 }
 }
 */


?>