<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Extra;
use \App\Models\Upload;
use \App\Models\User;
use \App\Models\Title;
/**
 * Class Logged
 * 
 * PHP 7.3
 */
class Logged extends \Core\Controller
{
    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();
    }

    /**
     * Home of logged user
     * 
     * @return void
     */
    public function homeAction()
    {
        //$this->restrictToPathAndSession();
        $id = Extra::getPathUser()->id;
        $titles = Title::getTitles($id);
        $showprivate = Extra::canShowPrivate();
        View::renderTemplate('Logged/index.html', [
            'titles' => $titles,
            'showprivate' => $showprivate
        ]);
    }

    /**
     * Logout
     * 
     * @return void
     */
    public function logoutAction()
    {
        $this->restrictToPathAndSession();
        if (isset($_POST['logout'])) {
            Auth::logout();

            $this->redirect('/');
        }       
        $user_name = $this->routerPath();
        $this->redirect("/$user_name/");
    }

    /**
     * Show add
     * 
     * @return void
     */
    public function showAddAction()
    {
        $this->restrictToPathAndSession();
        View::renderTemplate('Logged/add.html');
    }

    /**
     * Go Upload add
     * 
     * @return void
     */
    public function goUploadAction()
    {
        $this->restrictToPathAndSession();
        $user_name = $this->routerPath();
        // Merging files and post array
        $a = array_merge($_POST, $_FILES['file']);
        $upload = new Upload($a);
        
        if ($upload->controlUpload()) {
            $this->redirect("/$user_name/");
        }
    }

    /**
     * Show titles 
     * 
     * @return void
     */
    public function showTitlesAction()
    {
        $title_id = Extra::getPathTitle()->title_id;
        if (Extra::getPathTitle()->is_private == 1 && !Extra::canShowPrivate()) {
            $this->restrictToPathAndSession();
        }
        $p_int = Extra::getPathTitle()->is_private;
        if ($p_int == 0) {
            $is_private = false;
        } else {
            $is_private = true;
        }
        $files = Upload::getAllFiles($title_id);
        View::renderTemplate('Logged/titles.html', [
            'files' => $files,
            'title_id' => $title_id,
            'is_private' => $is_private
        ]);
    }

    /**
     * Delete titles 
     * 
     * @return void
     */
    public function deleteAction()
    {
        $this->restrictToPathAndSession();
        
        if (isset($_POST['delete'])) {
            $title_id = Extra::getPathTitle()->title_id;
        
            if (Title::delete($title_id)) {
                $user_name = $this->routerPath();
                $this->redirect("/$user_name/");
            } else {
                $this->redirect("/$user_name/$title_id/");
            }
        }
        $user_name = $this->routerPath();
        $this->redirect("/$user_name/");
    }

    /**
     * Make private or public  
     * 
     * @return void
     */
    public function makePrivatePublicAction()
    {
        $this->restrictToPathAndSession();
        if (isset($_POST)) {
            $title_id = $_POST['privateOrpublic'];
            Title::updatePrivateStatus($title_id);
        }
        $user_name = $this->routerPath();
        $this->redirect("/$user_name/");
    }
}