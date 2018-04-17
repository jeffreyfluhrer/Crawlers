<?php
$base = $_SERVER['DOCUMENT_ROOT'];
require_once $base . '/core/init.php';
?>

<html>
<?php
    PageHeader::render('VacaFun Ski Planning');
?>
<body>
    
  <!--Navigation Bar-->
  <?php NavBar::render(); ?>

  <div class="container main-content">
  <div class="row justify-content start">
    <div class="col-auto">
        <?php
            include 'Connect.php';

            if (isset($_POST["resortName"]) || isset($_POST["city"]) || isset($_POST["state"]) || isset($_POST["startDate"]) 
                || isset($_POST["endDate"]) || isset($_POST["URL"]) || isset($_POST["map"]) || isset($_POST["rating"]) || isset($_POST["difficulty"])) {      
              $conn = ConnectDatabase();
              $conn = ChooseDatabase($conn);
              $returnMsg = InsertValue($conn, $_POST["resortName"], $_POST["city"],  $_POST["state"], $_POST["startDate"],
                  $_POST["endDate"], $_POST["URL"], $_POST["map"],  $_POST["rating"], $_POST["difficulty"]);
            
              echo '<div class="alert alert-success" role="alert">' . $returnMsg . '</div>';
            }
        ?>
        <h2 class="content-title">Create new resort</h2>
        <form class="form-fixed" method="post" action="/insert.php">
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
              <label for="URL">Image of Trail Map</label>
              <input type="text" class="form-control" name="map">
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
</body>

</html>