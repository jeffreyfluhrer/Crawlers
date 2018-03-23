<?php

function ConnectDatabase() {
  
  $servername = "localhost";
  $username = "*********";
  $password = "*********";

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
  
    $sql = "USE *******";

  if ($conn->query($sql) === TRUE) {
      //echo "<br>Database selected";
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }
  return $conn;
}

function InsertValue($conn, $Name = "NULL", $City = "NULL", $State = "NULL", $LiftPrice= "NULL") {

  $sql = "INSERT INTO Resort (Name, City, State, LiftPrice) VALUES ('" . $Name . "', '" . $City . "', '" . $State . "'," . $LiftPrice . ")";

  if ($conn->query($sql) === TRUE) {
    $returnVal = "New record created successfully.";
  } else {
    $returnVal = "Error: " . $sql . "<br>" . $conn->error;
  }
  return $returnVal;
}

function FormSelectResort($Name, $City, $State, $LiftPrice) {

  $sql = "SELECT * FROM Resort";

  $where = "";

  // Form the WHERE clause here
  if (strlen($Name) != 0) {
    $where = $where . "Name = \"" . $Name . "\" ";
  }

  if (strlen($City) != 0) {
    if(strlen($where) != 0) {
      $where = $where . "AND ";
    }
    $where = $where . "City = \"" . $City . "\" ";
  }

  if (strlen($State) != 0) {
    if(strlen($where != 0)) {
      $where = $where . " AND ";
    }
    $where = $where . "State = \"" . $State . "\" ";
  }

  if (strlen($LiftPrice) != 0) {
    if(strlen($where) != 0) {
      $where = $where . " AND ";
    }
    $where = $where . "LiftPrice < \"" . $LiftPrice . "\" ";
  }

  if(strlen($where) != 0) {
    $sql = $sql . " WHERE " . $where;
  }
  return $sql;
}

function FormUpdateResort() {

$where = "";

// Form the WHERE clause here
if (strlen($_POST["oldResortName"]) != 0) {
  $where = $where . "Name = \"" . $_POST["oldResortName"] . "\" ";
}

if (strlen($_POST["oldCity"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . "AND ";
   }
  $where = $where . "City = \"" . $_POST["oldCity"] . "\" ";
}

if (strlen($_POST["oldState"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . "AND ";
  }
  $where = $where . "State = \"" . $_POST["oldState"] . "\" ";
}

if (strlen($_POST["oldLiftPrice"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . " AND ";
  }
  $where = $where . "LiftPrice = \"" . $_POST["oldLiftPrice"] . "\" ";
}

$set = "";

// Form the SET clause here
if (strlen($_POST["newResortName"]) != 0) {
  $set = $set . "Name = \"" . $_POST["newResortName"] . "\" ";
}

if (strlen($_POST["newCity"]) != 0) {
  if(strlen($set) != 0) {
    $set = $set . "AND ";
   }
  $set = $set . "City = \"" . $_POST["newCity"] . "\" ";
}

if (strlen($_POST["newState"]) != 0) {
  if(strlen($set) != 0) {
    $set = $set . "AND ";
  }
  $set = $set . "State = \"" . $_POST["newState"] . "\" ";
}

if (strlen($_POST["newLiftPrice"]) != 0) {
  if(strlen($set) != 0) {
    $set = $set . " AND ";
  }
  $set = $set . "LiftPrice = \"" . $_POST["newLiftPrice"] . "\" ";
}


$sql = "UPDATE Resort SET " . $set . " WHERE " . $where;

return $sql;

}

function FormDeleteResort($Name, $City, $State, $LiftPrice) {

  $where = "";

  // Form the WHERE clause here
  if (strlen($Name) != 0) {
    $where = $where . "Name = \"" . $Name . "\" ";
  }

  if (strlen($City) != 0) {
    if(strlen($where) != 0) {
      $where = $where . "AND ";
     }
    $where = $where . "City = \"" . $City . "\" ";
  }

  if (strlen($State) != 0) {
    if(strlen($where) != 0) {
      $where = $where . "AND ";
    }
    $where = $where . "State = \"" . $State . "\" ";
  }

  if (strlen($LiftPrice) != 0) {
    if(strlen($where) != 0) {
      $where = $where . " AND ";
    }
    $where = $where . "LiftPrice = \"" . $LiftPrice . "\" ";
  }


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
      echo "<th scope=\"col\">Lift Price</th>";
      echo "<th scope=\"col\">Delete?</th>";
      echo "</tr>";
      echo "<thead>";
      // output data of each row
      echo "<tbody>";
      while($row = $result->fetch_assoc()) {
        echo "<form method=\"post\" action=\"/search.php\">";
        echo "<td> <input type=\"text\" name=\"resortName\" value=\"" . $row["Name"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"city\" value=\"" . $row["City"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"state\" value=\"" . $row["State"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"liftPrice\" value=\"" . $row["LiftPrice"] . "\"></td>";
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

?>