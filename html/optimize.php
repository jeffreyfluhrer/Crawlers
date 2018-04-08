<html>
<head>
  <title>VacaFun Ski Optimizer</title>        
  <meta charset="utf-8" />
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    crossorigin="anonymous">

  <link rel="stylesheet" href="index.css">
</head>

<body>

<h1> This is the optimizer page for VacaFun </h1>

<table style="width:80%">
  <tr>
<?php

include 'optLib.php';

// JRF:  This is a draft of the server optimizer code

// 1.  Get the current user id (acquired from get or post variables) and validate that it exists in the database
// 2.  Check out the user preferences and load them in
// 3.  If one of the selections was chosen, then go to a page that gives its info
// 4.  If values from a selection on the previous page were set, update the userhistory table
// 5.  Select two spots based on the preferences by performing a database query
// 6.  Show the two spots on the screen with three options for each case (Like, Don't Like, I pick this one)

// Step 1:  Get the current user id
// TODO:  Don't have a calling PHP code so will have to hardcode this for now (Later implement the receipt of the vars)
if(strlen($_GET["username"]) != 0) {
    //printf("<br>The user name is %s",$_GET["username"]);
    $username = $_GET["username"];
}

// TODO: Remove this once the login page is finished and links to this
$username = "Jeffrey";

// Step 2:  Check out the user preferences and load them in

//echo "<br>Connecting to the database";
$conn = ConnectDatabase();
$conn = ChooseDatabase($conn);

// Form a query and extract user information
$where = "username = \"" . $username . "\"";
$userResult = PerformQuery($conn, "*", "UserInfo", $where);
$userinfo = $userResult->fetch_assoc();

// TODO:  Check if the user is not in the database

// Extract the user information
$userLocation = $userinfo["location"];
$tripDate = $userinfo["tripDate"];
$tripDuration = $userinfo["tripDuration"];
$userLevel = $userinfo["level"];
$userBudget = $userinfo["budget"];

// 3.  If one of the selections was chosen, then go to a page that gives its info
// TODO:  Implement this last

// 4.  If values from a selection on the previous page were set, update the userhistory table
// TODO:  Make sure that both resortName and username are keys otherwise the below won't work
if(strlen($_GET["leftfeedback"]) != 0) {
    HistoryManage($conn, $username, $_GET["leftfeedback"], $_GET["_leftresort"]);
}

if(strlen($_GET["rightfeedback"]) != 0) {
    HistoryManage($conn, $username, $_GET["rightfeedback"], $_GET["_rightresort"]);
}

// 5.  Select two spots based on the preferences by performing a database query
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
$checkForResorts = CheckForNValidResorts($conn, $n, $userLocation, $tripDate, $tripDuration, $userBudget, $budgetIncreaseRate);

// Get two resorts as a starter (pick least and most expensive of the affordable group)
$twoResorts = GetValidResorts($conn, $n, $userLocation, $tripDate, $tripDuration, $userBudget, $budgetRange);
//printf("<br>The first resort is %s and the second is %s",$twoResorts[0], $twoResorts[1]);



// 6.  Show the two spots on the screen with three options for each case (Like, Don't Like, I pick this one)
// Get the resort info
$leftResortInfo = GetResortInfo($conn,$twoResorts[0]);
$rightResortInfo = GetResortInfo($conn,$twoResorts[1]);

DisplayImageAndTitle($leftResortInfo,$rightResortInfo);

DisplayRatingandWeather($conn,$leftResortInfo,$rightResortInfo,$tripDate);

// This is the end
$conn->close();

?>
		</tr>
			<tr>
				<form action="/optimize.php">
				<td>
  					<input type="radio" name="leftfeedback" value="like" checked> Like<br>
  					<input type="radio" name="leftfeedback" value="dislike"> Dislike<br>
  					<input type="radio" name="leftfeedback" value="want"> I want this!!<br><br>
  					<input name="_leftresort" type="hidden" value="<?php echo $twoResorts[0]?>">
				</td>
				<td>
  					<input type="radio" name="rightfeedback" value="like" checked> Like<br>
  					<input type="radio" name="rightfeedback" value="dislike"> Dislike<br>
  					<input type="radio" name="rightfeedback" value="want"> I want this!!<br><br>
  					<input name="_rightresort" type="hidden" value="<?php echo $twoResorts[1]?>">
  					<input name="username" type="hidden" value="<?php echo $username?>">
  					<input type="submit">
				</td>
				</form> 
			</tr> 
    </table>
</body>
</html>