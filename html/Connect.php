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

  echo "<br>" . $sql;

  if ($conn->query($sql) === TRUE) {
    $returnVal = "<br>New record created successfully";
  } else {
    $returnVal =  "Error: " . $sql . "<br>" . $conn->error;
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
if (strlen($_GET["oldResortName"]) != 0) {
  $where = $where . "Name = \"" . $_GET["oldResortName"] . "\" ";
}

if (strlen($_GET["oldCity"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . "AND ";
   }
  $where = $where . "City = \"" . $_GET["oldCity"] . "\" ";
}

if (strlen($_GET["oldState"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . "AND ";
  }
  $where = $where . "State = \"" . $_GET["oldState"] . "\" ";
}

if (strlen($_GET["oldLiftPrice"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . " AND ";
  }
  $where = $where . "LiftPrice = \"" . $_GET["oldLiftPrice"] . "\" ";
}

$set = "";

// Form the SET clause here
if (strlen($_GET["newResortName"]) != 0) {
  $set = $set . "Name = \"" . $_GET["newResortName"] . "\" ";
}

if (strlen($_GET["newCity"]) != 0) {
  if(strlen($set) != 0) {
    $set = $set . "AND ";
   }
  $set = $set . "City = \"" . $_GET["newCity"] . "\" ";
}

if (strlen($_GET["newState"]) != 0) {
  if(strlen($set) != 0) {
    $set = $set . "AND ";
  }
  $set = $set . "State = \"" . $_GET["newState"] . "\" ";
}

if (strlen($_GET["newLiftPrice"]) != 0) {
  if(strlen($set) != 0) {
    $set = $set . " AND ";
  }
  $set = $set . "LiftPrice = \"" . $_GET["newLiftPrice"] . "\" ";
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

      echo "<table style=\"width:40%\">";
      echo "<tr>";
      echo "<th>Name:</th>";
      echo "<th>City:</th>";
      echo "<th>State:</th>";
      echo "<th>Lift Price:</th>";
      echo "<th>Delete:</th>";
      echo "</tr>";
      // output data of each row
      while($row = $result->fetch_assoc()) {
        echo "<form action=\"/update_delete_page.php\">";
        echo "<tr>";
        echo "<td> <input type=\"text\" name=\"resortName\" value=\"" . $row["Name"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"city\" value=\"" . $row["City"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"state\" value=\"" . $row["State"] . "\"></td>";
        echo "<td> <input type=\"text\" name=\"liftPrice\" value=\"" . $row["LiftPrice"] . "\"></td>";
        echo "<td> <input type=\"submit\" value=\"Delete\"></td>";
        echo "</tr>";
        echo "</form>";  
      }
      echo "</table>";

  } else {
      echo "Sorry, no results found";
  }

} 

?>