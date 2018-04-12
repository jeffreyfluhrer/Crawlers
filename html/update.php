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

      if (isset($_GET["oldResortName"]) || isset($_GET["oldCity"]) || isset($_GET["oldState"]) || isset($_GET["oldStartDate"])
          || isset($_GET["oldEndDate"]) || isset($_GET["oldURL"]) || isset($_GET["oldRating"]) || isset($_GET["oldDifficulty"])) { 
        $conn = ConnectDatabase();
        $conn = ChooseDatabase($conn);
        $sql = FormUpdateResort();

        if ($conn->query($sql) === TRUE) {
          echo '<div class="alert alert-success" role="alert">Record(s) successfully updated.</div>';
        } else {
          echo '<div class="alert alert-failure" role="alert">Error: ' . $sql . "<br>" . $conn->error . '</div>';
        }
      }
  ?>
        <h2 class="content-title">Update existing resort</h2>
        <form method="get" action="/update.php">
        <table> 
        <tr>
            <td>
              <label for="oldResortName">Old Resort name</label>
              <input type="text" class="form-control" name="oldResortName">
            </td>
            <td>
              <label for="newResortName">New Resort name</label>
              <input type="text" class="form-control" name="newResortName">
            </td>
         </tr>
        <tr>
            <td>
              <label for="oldCity">Old City</label>
              <input type="text" class="form-control" name="oldCity">
            </td>
            <td>
              <label for="newCity">New City</label>
              <input type="text" class="form-control" name="newCity">
            </td>
         </tr>
        <tr>
            <td>
              <label for="oldState">Old State</label>
              <input type="text" class="form-control" name="oldState">
            </td>
            <td>
              <label for="newState">New State</label>
              <input type="text" class="form-control" name="newState">
            </td>
         </tr>
        <tr>
            <td>
         	  <label for="oldStartDate">Old Start Date</label>
          	  <input type="text" class="form-control" name="oldStartDate">
            </td>
            <td>
         	  <label for="newStartDate">New Start Date</label>
          	  <input type="text" class="form-control" name="newStartDate">
            </td>
         </tr>
        <tr>
            <td>
          	  <label for="oldEndDate">Old End Date</label>
          	  <input type="text" class="form-control" name="oldEndDate"">
            </td>
            <td>
          	  <label for="newEndDate">New End Date</label>
          	  <input type="text" class="form-control" name="newEndDate"">
            </td>
         </tr>
        <tr>
            <td>
        	  <label for="oldURL">Old Image</label>
          	  <input type="text" class="form-control" name="oldURL">
            </td>
            <td>
        	  <label for="newURL">New Image</label>
          	  <input type="text" class="form-control" name="newURL">
            </td>
         </tr>
        <tr>
            <td>
        	  <label for="oldRating">Old Rating</label>
          	  <input type="text" class="form-control" name="oldRating">
            </td>
            <td>
        	  <label for="newRating">New Rating</label>
          	  <input type="text" class="form-control" name="newRating">
            </td>
         </tr>
        <tr>
            <td>
        	  <label for="oldDifficulty">Old Difficulty</label>
          	  <input type="text" class="form-control" name="oldDifficulty">
            </td>
            <td>
        	  <label for="newDifficulty">New Difficulty</label>
          	  <input type="text" class="form-control" name="newDifficulty">
            </td>
         </tr> 
      	</table>
        <button type="submit" class="btn btn-primary" >Submit</button>
        </form>
      </div>
    </div>
  </div>

  <!-- <button class="btn btn-success" onclick="$(this).hide();"> Click me!</button> -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
    crossorigin="anonymous"></script>
</body>

</html>