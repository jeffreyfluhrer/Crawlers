<html>
<head>
  <title>VacaFun Signup Page</title>        
  <meta charset="utf-8" />
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    crossorigin="anonymous">
  <!-- http://glyphicons.com/license/ used for icon images: free icons as part of their licensing-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <link rel="stylesheet" href="index.css">
</head>

  <!-- Banner Section -->
  <section class="banner">
    <h1>New Account Verification</h1>
  </section>

<?php 

include 'Connect.php';

$username = $_POST["username"];
$password = $_POST["password"];

if(isset($username) && isset($password)) {
    
    // Connect to the database
    $conn = ConnectDatabase();
    $conn = ChooseDatabase($conn);
    
    // Check that the username and password is in the database
    $userinfo = GetUserInfo($conn,$username,$password);
    if(array_key_exists("username", $userinfo)) {
        // Extract all the user info here
        $username = $userinfo["username"];
        $password = $userinfo["password"];
        $tripDate = $userinfo["tripDate"];
        $tripDuration = $userinfo["tripDuration"];
        $level = $userinfo["level"];
        $location = $userinfo["location"];
        $enc_location = urlencode($location);
        $budget = $userinfo["budget"];
        $opt_link = "optimize.php?username=" . $username . "&password=" . $password . "&tripDate=" . $tripDate . 
        "&tripDuration=" . $tripDuration . "&level=" . $level . "&location=" . $enc_location . "&budget=" . $budget;
        //$opt_link = urlencode($opt_link);
        printf("<h2> Please continue to the automated Ski Trip Planning Site <a href=" . $opt_link .">here</a>");
    } else {
        printf("<h2> Sorry, login not found in database, please try again <a href=\"index.php\">here</a> ");
    }
}
// Didn't receive proper login info
else {
    printf("<h2> Sorry, didn't receive proper login/password combo, please try again <a href=\"index.php\">here</a> ");
}
    
?>

