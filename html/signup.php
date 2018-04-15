<?php
$base = $_SERVER['DOCUMENT_ROOT'];
require_once $base . '/core/init.php';

$error = '';

if (Input::exists() && Input::validateToken())
{
	$validate = new Validate();
	$validation = $validate->check(
		$_POST,
		array(
			'username' => array(
				'required' => true,
			),
			'password' => array(
				'required' => true,
				'matches' => 'password2',
			),
	));

	if ($validation->passed())
	{
		$username = Input::get('username');
		$password = Input::get('password');
		$user = new User();

		if ($user->create($username, $password))
		{
			Session::flash('registered', 'Registration successful. Please log in to continue.');
			Redirect::to('login.php');
		}
		else
		{
			$error = $user->error();
		}
	}
}
?>

<html>
	<?php PageHeader::render('VacaFun Signup Page') ?>

  <!-- Banner Section -->
  <section class="banner">
    <h1>Welcome to VacaFun:  Please Enter Your New Account Here</h1>
  </section>
  
	<div class="container main-content">
    	<div class="row justify-content start">
      		<div class="col-auto">
    	<h2 class="content-title">Enter New Account Info:</h2>
    	<form method="post">
			<?php Alert::tryRender(Alert::WARNING, $error);?>

    		<div class="form-group">
        		<label for="resortName">User Name:</label>
        		<input type="text" class="form-control" name="username">
      		</div>
    		<div class="form-group">
        		<label for="resortName">Password:</label>
        		<input type="password" class="form-control" name="password">
      		</div>
    		<div class="form-group">
        		<label for="resortName">Reenter Password:</label>
        		<input type="password" class="form-control" name="password2">
				</div>      		
				<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
      	<button type="submit" class="btn btn-primary" >Submit</button>
		</form>
<?php

?>