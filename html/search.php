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
    <h2 class="content-title">Search existing resorts</h2>
    <form action="/search.php">
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
          <label for="startDate">Start of Season Date</label>
          <input type="text" class="form-control" name="startDate">
      </div>
            <div class="form-group">
          <label for="endDate">End of Season Date</label>
          <input type="text" class="form-control" name="endDate">
      </div>
      <div class="form-group">
          <label for="URL">Image of Resort</label>
          <input type="text" class="form-control" name="URL">
      </div>
      <div class="form-group">
          <label for="rating">Resort rating (# stars out of five)</label>
          <input type="text" class="form-control" name="rating">
      </div>
      <div class="form-group">
          <label for="difficulty">Difficulty level on a scale of one to ten</label>
          <input type="text" class="form-control" name="difficulty">
      </div>  
      <button type="submit" class="btn btn-primary" >Submit</button>
    </form>
</div>
</div>

    <?php

    include 'Connect.php';

    if (isset($_GET["resortName"]) || isset($_GET["city"]) || isset($_GET["state"]) || isset($_GET["startDate"])
        || isset($_GET["endDate"]) || isset($_GET["URL"]) || isset($_GET["rating"]) || isset($_GET["difficulty"])
        || isset($_GET["delete"])) {
      $conn = ConnectDatabase();
      $conn = ChooseDatabase($conn);

      if (isset($_GET["delete"])) {
          $sql = FormDeleteResort($_GET["resortName"], $_GET["city"], $_GET["state"], $_GET["startDate"],
              $_GET["endDate"], $_GET["URL"],  $_GET["rating"], $_GET["difficulty"]);
          //printf("<br>Here is the deletion string %s", $sql);
        if ($conn->query($sql) === TRUE) {
            
          echo '<div class="alert alert-success" role="alert">Record(s) successfully deleted.</div>';
        } else {
          echo '<div class="alert alert-failure" role="alert">Error: ' . $sql . "<br>" . $conn->error . '</div>';
        }
      }

      $sql = FormSelectResort($_GET["resortName"], $_GET["city"], $_GET["state"], $_GET["startDate"],
          $_GET["endDate"], $_GET["URL"],  $_GET["rating"], $_GET["difficulty"]);
      
      //printf("<br>Here is the insertion string %s", $sql);
      PerformQuery($conn, $sql);
      
      $conn->close();      
    }

    ?> 

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"></script>
</body>

</html>