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
   <table style="width:100%">
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
<!-- The entered resort is: <?php echo $_GET["resortName"]; ?><br>
The entered city is: <?php echo $_GET["city"]; ?> -->

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

<?php
$servername = "localhost";
$username = "****";
$password = "******";

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

//echo "<br>" . $sql;

if ($conn->query($sql) === TRUE) {
   echo "<br>record(s) successfully updated";
} else {
   echo "Error: " . $sql . "<br>" . $conn->error;
}


?> 


<br>
<h2>  Search Data within the Database manually here </h2>
<br>
 <form action="/search_action.php">
   <table style="width:100%">
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



<!-- <button class="btn btn-success" onclick="$(this).hide();"> Click me!</button> -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html><html>
<body>

</body>
</html> 
