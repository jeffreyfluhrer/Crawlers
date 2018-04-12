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


if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["password2"])) {
    
    if($_POST["password"] !=$_POST["password2"]) {
        printf("<h2> Passwords do not agree, please try again <a href=\"signup.php\">here</a>");
    }
    else {
        printf("<h2> Begin setting up user information <a href=\"getStarted.php?username=" . $_POST["username"] . "&password=" . $_POST["password"] . "\">here</a>");
    }
}
    
?>
