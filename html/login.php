<?php
$base = $_SERVER['DOCUMENT_ROOT'];
require_once $base . '/core/init.php';

$user = new User();
$errors = array();

if($user->isLoggedIn()){
	Redirect::to('index.php');
}

if(Input::exists() && Input::validateToken()){
	$validate = new Validate();
	$validation = $validate->check($_POST,
		array(
			'username' => array(
				'required' => true,
			),
			'password' => array(
				'required' => true,
			)	
	));
	
	if($validation->passed()){
		$user = new User();
		$remember = (Input::get('remember') === 'on');
		$login = $user->login(Input::get('username'), Input::get('password'), $remember);
		
		if($login){
			Redirect::to('index.php');
		} else {
			$errors[] = $user->error();
		}
	} else {
		$errors = $validation->errors();
	}
}

?>

<html>    
  <?php PageHeader::render('VacaFun Signup Page'); ?>

  <!-- Banner Section -->
  <section class="banner">
    <h1>Welcome to VacaFun:  Please Enter Your Account Information</h1>
  </section>
  
	<div class="container main-content">
		<?php
			Alert::tryRender(Alert::SUCCESS, Session::consume('registered'));
			Alert::tryRender(Alert::INFO, Session::consume('loginRequired'));
			Alert::tryRender(Alert::WARNING, $errors);
		?>

    	<div class="row justify-content start">
      		<div class="col-auto">
    			<h2 class="content-title">Enter Account Info:</h2>
    			<form method="post">
					<div class="form-group">
						<label for="resortName">User Name:</label>
						<input type="text" class="form-control" name="username">
					</div>
					<div class="form-group">
						<label for="resortName">Password:</label>
						<input type="password" class="form-control" name="password">
					</div>
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="remember">
						<label class="form-check-label" for="remember">Remember me</label>
					</div>
					<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
      				<button type="submit" class="btn btn-primary" >Submit</button>
				</form>
			</div>
		</div>
	</div>
</html>