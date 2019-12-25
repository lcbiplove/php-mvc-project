<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;

/**
 * Biplove Controller for the path
 * 
 * PHP 7.3
 */
class Admin extends \Core\Controller
{
    /**
     * Before filter
     */
    public function before()
    {
        $this->routerUser = $this->routerPath('user_name');
        
    }
    /**
     * Show login page if url is perfect 
     * 
     * @return void
     */
    public function showLoginAction()
    {
        $user_name = $this->routerUser;
        
        if (User::findByUsername($user_name)) {
            $user = User::findByUsername($user_name);

            View::renderTemplate('Admin/login.html', [
                'user' => $user
            ]);

        } else {
            $this->redirect('/');
        }
    }

    /**
     * Go Login, login button is clicked
     * 
     * @return void
     */
    public function goLoginAction()
    {
        $user = new User($_POST);

        if ($user->verifyPassword()) {
            $user = User::findByUsername($user->username);
            
            Auth::login($user);

            $this->redirect("/$user->username/");
        } 

        $this->redirect('/');

    }
}