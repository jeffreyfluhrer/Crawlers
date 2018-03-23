<html>

<head>
<title>VacaFun Ski Planning</title>        
<meta charset="utf-8" />
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
  crossorigin="anonymous">

<link rel="stylesheet" href="index.css">
</head>


<body>
<nav class="navbar navbar-top navbar-expand-sm">
<a class="navbar-link navbar-brand" href="index.html">VacaFun</a>

<div>
  <ul class="navbar-nav mr-auto">
    <li class="nav-item">
      <a class="nav-link" href="insert.php">Create</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="update.php">Update</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="search.php">Search</a>
    </li>
  </ul>
</div>
</nav>
<div class="container main-content">
<div class="row justify-content start">
  <div class="col-auto">
<?php

      include 'Connect.php';

      if (isset($_POST["resortName"]) || isset($_POST["city"]) || isset($_POST["state"]) || isset($_POST["liftPrice"])) {      
        $conn = ConnectDatabase();
        $conn = ChooseDatabase($conn);
        $returnMsg = InsertValue($conn, $_POST["resortName"], $_POST["city"],  $_POST["state"], $_POST["liftPrice"]);
      
        echo '<div class="alert alert-success" role="alert">' . $returnMsg . '</div>';
      }
  ?>
    <h2 class="content-title">Create new resort</h2>
    <form class="form-fixed" method="post" action="/insert.php">
      <div class="form-group">
        <label for="resortName">Resort name</label>
        <input type="text" class="form-control" name="resortName">
      </div>
      <div class="form-group">
          <label for="city">City</label>
          <input type="text" class="form-control" name="city">
      </div>
      <div class="form-group">
          <label for="state">State</label>
          <input type="text" class="form-control" name="state">
      </div>
      <div class="form-group">
          <label for="liftPrice">Lift price</label>
          <input type="text" class="form-control" name="liftPrice">
      </div>
      <button type="submit" class="btn btn-primary" >Submit</button>
    </form>
    </div>
    </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"></script>
</body>

</html>