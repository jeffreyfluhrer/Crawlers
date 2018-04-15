<?php

class NavBar
{
    public static function render()
    {
        
        $user = new User();
        
        if ($user->isLoggedIn())
        {
            $navBarRight = '<span class="navbar-text mr-1">' . "Logged in as " .  $user->data()->username .  "." . '</span>';
            $navBarRight = $navBarRight . '<li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>';
        }
        else
        {
            $navBarRight = @'
                <li class="nav-item">
                    <a class="nav-link" href="signup.php">
                        <i class="fas fa-user-plus"></i>
                        <span>Sign Up</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                </li>
            ';
        }

        echo @'
        <nav class="navbar navbar-top navbar-expand-sm">
            <a class="navbar-link navbar-brand" href="index.php">VacaFun</a>
    
            <div class="container-fluid">
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
                <ul class="navbar-nav ml-auto">'
                    . $navBarRight
                . @'</ul>
        </div>         
      </nav>
        ';
    }
}