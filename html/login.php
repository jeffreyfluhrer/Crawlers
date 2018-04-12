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
    <h1>Welcome to VacaFun:  Please Enter Your Account Information</h1>
  </section>
  
	<div class="container main-content">
    	<div class="row justify-content start">
      		<div class="col-auto">
    	<h2 class="content-title">Enter Account Info:</h2>
    	<form action="/verify_login.php" method="post">
    		<div class="form-group">
        		<label for="resortName">User Name:</label>
        		<input type="text" class="form-control" name="username">
      		</div>
    		<div class="form-group">
        		<label for="resortName">Password:</label>
        		<input type="password" class="form-control" name="password">
      		</div>
     		
      		<button type="submit" class="btn btn-primary" >Submit</button>
		</form>
<?php

?>