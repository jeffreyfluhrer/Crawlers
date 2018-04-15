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
    
  <!-- Banner Section -->
  <section class="banner">
    <h1>Welcome to VacaFun:</h1>
    <p>Your Gateway to your optimal ski vacation!</p>
      <a href = "getStarted.php" class = "btn btn-primary btn-lg">Get Started</a>
  </section>
    
  <!--Three Column Options--> 
  <section class="my-3">
        <div class="d-flex">
            <article class="col">
                <header>
                    <h3>Create New Resort</h3>
                </header>
                <p>This option allows you to create a new resort by filling out the information necessary such as the resort name, where it is located, and the lift price at this resort.</p>
                <footer>
                    <a href="insert.php" class="btn btn-primary">Create</a>
                </footer>
            </article>
            <article class="col">
                <header>
                    <h3>Update Resort</h3>
                </header>
                <p>This option allows you to update the resort information by searching for the resort and either updating the name, where it is located, and/or the lift price.</p>
                <footer>
                    <a href="update.php" class="btn btn-primary">Update</a>
                </footer>
            </article>
            <article class="col">
                <header>
                    <h3>Search for Resort</h3>
                </header>
                <p>This option allows you to search for the resort by entering the information such as the resort name, where it is located, and/or the lift price into the search bar.</p>
                <footer>
                    <a href="search.php" class="btn btn-primary">Search</a>
                </footer>
            </article>
        </div>
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
