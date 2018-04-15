<?php

class UserPageController
{
    public function __construct()
    {
    }

    public function run()
    {
        $user = new User();

        if(!$user->isLoggedIn())
        {
            Session::flash('loginRequired', 'You must be logged in to perform this action.');
            Redirect::to('login.php');
        }
    }
}