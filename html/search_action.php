<html>
   
<head>
<meta charset="utf-8" />
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
</head>

<body>
    
    
<h1> VACAFUN Website:  Your Gateway to your optimal ski vacation!!!</h1>

<h2>  Insert Data to the Database manually here </h2>
<br>
 <form action="/insert_action2.php">
   <table style="width:60%">
     <tr>
       <th>Name:</th>
       <th>City:</th>
       <th>State:</th>
       <th>Lift Price:</th>
     </tr>
     <tr>
       <td> <input type="text" name="resortName"> </td>
       <td> <input type="text" name="city"> </td>
       <td> <input type="text" name="state"> </td>
       <td> <input type="text" name="liftPrice"> </td>
     </tr>
   </table>
   <br>
   <input type="submit" value="Submit">
</form> 
<br>

<h2>  Update Data within the Database manually here </h2>
<br>
 <form action="/update_action.php">
   <table style="width:60%">
     <tr>
       <th>Name (Old -> New):</th>
       <th>City (Old -> New):</th>
       <th>State (Old -> New):</th>
       <th>Lift Price (Old -> New):</th>
     </tr>
     <tr>
       <td> <input type="text" name="oldResortName"> </td>
       <td> <input type="text" name="oldCity"> </td>
       <td> <input type="text" name="oldState"> </td>
       <td> <input type="text" name="oldLiftPrice"> </td>
     </tr>
     <tr>
       <td> <input type="text" name="newResortName"> </td>
       <td> <input type="text" name="newCity"> </td>
       <td> <input type="text" name="newState"> </td>
       <td> <input type="text" name="newLiftPrice"> </td>
     </tr>
   </table>
   <br>
   <input type="submit" value="Submit">
</form> 

<br>
<h2>  Search Data within the Database manually here </h2>
<br>
 <form action="/search_action.php">
   <table style="width:60%">
     <tr>
       <th>Name:</th>
       <th>City:</th>
       <th>State:</th>
       <th>Lift Price (less than):</th>
     </tr>
     <tr>
       <td> <input type="text" name="resortName"> </td>
       <td> <input type="text" name="city"> </td>
       <td> <input type="text" name="state"> </td>
       <td> <input type="text" name="liftPrice"> </td>
     </tr>
   </table>
   <br>
   <input type="submit" value="Submit">
</form> 

<br>
<h2> Search Results are as follows: </h2>

<!-- The entered resort is: <?php echo $_GET["resortName"]; ?><br>
The entered city is: <?php echo $_GET["city"]; ?> -->

<?php
$servername = "localhost";
$username = "****";
$password = "********";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "<br>Connected successfully";

$sql = "USE *****";

if ($conn->query($sql) === TRUE) {
    //echo "<br>Database selected";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "SELECT * FROM Resort";

$where = "";

// Form the WHERE clause here
if (strlen($_GET["resortName"]) != 0) {
  $where = $where . "Name = \"" . $_GET["resortName"] . "\" ";
}

if (strlen($_GET["city"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . "AND ";
  }
  $where = $where . "City = \"" . $_GET["city"] . "\" ";
}

if (strlen($_GET["state"]) != 0) {
  if(strlen($where != 0)) {
    $where = $where . " AND ";
  }
  $where = $where . "State = \"" . $_GET["state"] . "\" ";
}

if (strlen($_GET["liftPrice"]) != 0) {
  if(strlen($where) != 0) {
    $where = $where . " AND ";
  }
  $where = $where . "LiftPrice < \"" . $_GET["liftPrice"] . "\" ";
}

if(strlen($where) != 0) {
  $sql = $sql . " WHERE " . $where;
}

//echo "<br>" . $sql . "<br>";

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
//    echo "<th>Update:</th>";
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
//      echo "<td> <input type=\"submit\" value=\"Update\"></td>";
      echo "</tr>";
      echo "</form>";  
    }
    echo "</table>";

} else {
    echo "Sorry, no results found";
}

//echo "ResortName: " . $row["Name"]. " - City: " . $row["City"]. " State " . $row["State"] . " Price " . $row["LiftPrice"] . "<br>";

$conn->close();
?> 

<!-- <button class="btn btn-success" onclick="$(this).hide();"> Click me!</button> -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html><html>
<body>

</body>
</html> 
