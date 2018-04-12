<html>
<head>
  <title>VacaFun Ski Planning</title>        
  <meta charset="utf-8" />
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    crossorigin="anonymous">
  <!-- http://glyphicons.com/license/ used for icon images: free icons as part of their licensing-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <link rel="stylesheet" href="index.css">
</head>
<body>
    
  <!--Navigation Bar-->
  <nav class="navbar navbar-top navbar-expand-sm">
    <a class="navbar-link navbar-brand" href="index.html">VacaFun</a>

    <div class = "container-fluid">
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
      <ul class ="navbar-nav ml-auto">
          <!--Icons from Glypicons -->
          <li class = "nav-item"><a class = "nav-link" href="signup.php"><span class="glyphicon glyphicon-user"></span>Sign Up</a></li>
            <li class = "nav-item"><a class = "nav-link" href="login.php"><span class="glyphicon glyphicon-log-in"></span>Login</a></li>
      </ul>
    </div>         
  </nav>

<?php
if(!strcmp($_GET["leftfeedback"],"want") || !strcmp($_GET["rightfeedback"],"want"))
    echo "<h1> Here is the info on the Resort you chose </h1>";
else
    echo "<h1> This is the optimizer page for VacaFun </h1>";

?>

<table style="width:80%">
  <tr>
<?php

include 'optLib.php';
include 'Connect.php';
ini_set("default_charset", "UTF-8");

// JRF:  This is a draft of the server optimizer code

// 1.  Get the current user id (acquired from get or post variables) and validate that it exists in the database
// 2.  Check out the user preferences and load them in
// 3.  If one of the selections was chosen, then go to a page that gives its info
// 4.  If values from a selection on the previous page were set, update the userhistory table
// 5.  Select two spots based on the preferences by performing a database query
// 6.  Show the two spots on the screen with three options for each case (Like, Don't Like, I pick this one)

// Step 1:  Connect to the database

//echo "<br>Connecting to the database";
$conn = ConnectDatabase();
$conn = ChooseDatabase($conn);
//echo "<br>Connected to the database";

// Step 2:  Get the current user id
// TODO:  Don't have a calling PHP code so will have to hardcode this for now (Later implement the receipt of the vars)
if(strlen($_GET["username"]) != 0) {
    //printf("<br>The user name is %s",$_GET["username"]);
    $username = $_GET["username"];
    $password = $_GET["password"];
    $tripDate = $_GET["date"];
    $tripDuration = $_GET["duration"];
    $level = $_GET["difficulty"];
    $location = $_GET["origin"];
    $budget = $_GET["price"];
    // Check if user is already in database otherwise add info to database
    //printf("<br> Checking the user info here with username = %s",$username);
    AddUserInfo($conn,$username,$password,$tripDate,$tripDuration,$level,$location,$budget);
}


// Form a query and extract user information
$where = "username = \"" . $username . "\"";
$userResult = GetQuery($conn, "*", "UserInfo", $where);
$userinfo = $userResult->fetch_assoc();

// TODO:  Check if the user is not in the database

// Extract the user information
$userLocation = $userinfo["location"];
$tripDate = $userinfo["tripDate"];
$tripDuration = $userinfo["tripDuration"];
$userLevel = $userinfo["level"];
$userBudget = $userinfo["budget"];

//printf("<br>The user location = %s, the date = %s, the duration = %d, level = %1.2f and the budget = %1.2f",
//    $userLocation,$tripDate,$tripDuration, $level, $budget);

// 3.  If one of the selections was chosen, then go to a page that gives its info
// TODO:  Implement this last
if(!strcmp($_GET["leftfeedback"],"want") || !strcmp($_GET["rightfeedback"],"want")) {
    // TODO:  Implement the selection screen
    if(!strcmp($_GET["leftfeedback"],"want"))
        $chosenResort = $_GET["_leftresort"];
    else
        $chosenResort = $_GET["_rightresort"];
    printf("<h1> You picked the following resort:  %s </h1>",$chosenResort);
    //printf("<br> The chosen resort is %s",$chosenResort);
    $chosenResortInfo = GetResortInfo($conn,$chosenResort);
    DisplayChosenImage($chosenResortInfo);
    $conn->close();
}
else {
    // 4.  If values from a selection on the previous page were set, update the userhistory table
    // TODO:  Make sure that both resortName and username are keys otherwise the below won't work
    if(strlen($_GET["leftfeedback"]) != 0) {
        HistoryManage($conn, $username, $_GET["leftfeedback"], $_GET["_leftresort"]);
    }

    if(strlen($_GET["rightfeedback"]) != 0) {
        HistoryManage($conn, $username, $_GET["rightfeedback"], $_GET["_rightresort"]);
    }

    // 6.  Select two spots based on the preferences by performing a database query
    // Use a routine that performs a query that checks for at least n valid responses
    // TODO:  !!! This is the most complex piece of this code.  Optimization will need to be performed here
    //        once this is working in principle !!!
    /*
    *  ------------ Algorithm outline ------------
    *  There are approximately three parameters to optimize on - rating, budget and difficulty
    *  Need to test the user on at least six cases and then start to estimate what is important and what is not
    *  The first six cases should start within the budget constraint to get at least six data points in the
    *  database.  After this, a scoring system can be used to select the next likely selections.
    *  The scoring system works by taking likes and dislikes of the resorts and comparing the
    *  budget, rating and level versus the average values of the resorts and penalizing for dislikes 
    *  and rewarding for likes.  The weights are then used to define the where clause of a query.
    */
    // Tunable parameters for the optimization algorithm
    $budgetIncreaseRate = 0.1;

    // TODO:  Loop over and increment the budgetRange in steps of 20 % until 2 hits are made
    //$checkForResorts = CheckForNValidResorts($conn, $n, $userLocation, $tripDate, $tripDuration, $userBudget, $budgetIncreaseRate);

    // Get two resorts as a starter (pick least and most expensive of the affordable group)
    //$twoResorts = GetValidResorts($conn, $n, $userLocation, $tripDate, $tripDuration, $userBudget, $budgetRange);
    //printf("<br>The first resort is %s and the second is %s",$twoResorts[0], $twoResorts[1]);
    $twoResorts = GetRandomResorts($conn, $username);


    // 7.  Show the two spots on the screen with three options for each case (Like, Don't Like, I pick this one)
    // Get the resort info
    $leftResortInfo = GetResortInfo($conn,$twoResorts[0]);
    $rightResortInfo = GetResortInfo($conn,$twoResorts[1]);

    DisplayImageAndTitle($leftResortInfo,$rightResortInfo);

    DisplayRatingandWeather($conn,$leftResortInfo,$rightResortInfo,$tripDate);

    // This is the end
    $conn->close();

    echo DefaultTableResponse($_GET["username"], $twoResorts[0], $twoResorts[1]);
}
?>

</body>
</html>