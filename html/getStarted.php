<?php
$base = $_SERVER['DOCUMENT_ROOT'];
require_once $base . '/core/init.php';

$userPage = new UserPageController();
$userPage->run();

?>


<html>
<?php
    PageHeader::render('VacaFun Ski Planning');
?>
<body>
    
  <!--Navigation Bar-->
  <?php NavBar::render(); ?>
    
  <!--Get Started Form--> 
  <!--NEED TO DO: have a php that uses the user's inputs as restrictions--> 
  <section class="form">
    <h1>Enter the following information:</h1>
      <form class="form-horizontal" action="optimize.php">
          <div class = "form-group">
              <label class="control-label col-sm-2" for="budget">Total Budget ($):</label>
              <div class="col-sm-10">
                  <input type="number" required name="price" min="0" step=".01" class ="form-control" id="number" placeholder="Enter Total Budget...">
              </div>
          </div>
          <div class="form-group">
              <label class="control-label col-sm-2" for="difficulty">Difficulty Level (Out of 10):</label>
              <div class="col-sm-10">
                  <input type="number" required name="difficulty" min="0" step="0.1" class="form-control" id="difficulty" placeholder="Enter Ideal Difficulty Level...">
              </div>
          </div>
          <div class="form-group">
              <label class="control-label col-sm-2" for="date">Trip Date:</label>
              <div class="col-sm-10">
                  <input type="date" required name="date" class="form-control" id="date">
              </div>
          </div>
                    <div class="form-group">
              <label class="control-label col-sm-2" for="duration">Number of days:</label>
              <div class="col-sm-10">
                  <input type="number" required name="duration" class="form-control" id="date" placeholder="How many days...">
              </div>
          </div>
          <div class="form-group">
              <label class="control-label col-sm-2" for="origin">Where you live:</label>
              <div class="col-sm-10">
                 <select name="origin" id="origin">
    				<option value="Atlanta">Atlanta</option>
    				<option value="Boston">Boston</option>
    				<option value="Chicago">Chicago</option>
    				<option value="Dallas">Dallas</option>
    				<option value="Los Angeles">Los Angeles</option>
    				<option value="Miami">Miami</option>
    				<option value="New York">New York</option>
    				<option value="San Francisco">San Francisco</option>
    				<option value="Seattle">Seattle</option>
    				<option value="Washington D.C.">Washington D.C.</option>
  				</select>
                  <!-- <input type="text" required name="origin" class="form-control" id="origin" placeholder="New York, Chicago, LA, Boston..."> -->
              </div>
          </div> 
                  <input name="username" type="hidden" value="<?php echo $_GET["username"]?>">
                  <input name="password" type="hidden" value="<?php echo $_GET["password"]?>">                             
          <div class="form-group"> 
              <div class="col-sm-offset-2 col-sm-10">
                  <button type="submit" class="btn btn-default">Submit</button>
              </div>
          </div>
      </form>
  </section>

  <!-- Footer -->
  <footer id="footer" class="navbar navbar-bottom navbar-expand-sm">
      <ul id="footer" class="navbar-nav mr-auto">
            <li><a class="nav-link" href="https://www.facebook.com/">Facebook</a></li>
            <li><a class="nav-link" href="https://twitter.com/?lang=en">Twitter</a></li>
            <li><a class="nav-link" href="https://www.linkedin.com/">LinkedIn</a></li>
            <li><a class="nav-link" href="https://www.pinterest.com/">Pinterest</a></li>
            <li><a class="nav-link" href="https://vimeo.com/">Vimeo</a></li>
      </ul>
      <li class="nav-link">&copy; VacaFun Ski Planning</li>                        
  </footer>
</body>

</html>
