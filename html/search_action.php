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

include 'Connect.php';

$conn = ConnectDatabase();

$conn = ChooseDatabase($conn);

$sql = FormSelectResort($_GET["resortName"], $_GET["city"], $_GET["state"], $_GET["liftPrice"]);

echo "<br>" . $sql . "<br>";

PerformQuery($conn, $sql);

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
