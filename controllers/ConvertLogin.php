<?php

class ConvertLogin
{
    protected $back; // Return path after a successful login
    protected $posted; // Login form was posted
    protected $name; // User login name (for redisplay purpose)
    protected $error; // Possible error message for user


    public function __construct()
    {
        $this->posted = empty($_POST['posted']) ? false : true;
        $this->error = '';
        $this->back = '';
    }

    
    public function request()
    {
        if (!$this->posted) {
            if (!empty($_GET['back'])) {
                $this->back = urldecode($_GET['back']);
            }
        } else {
            if (!empty($_POST['back'])) {
                $this->back = $_POST['back'];
            }

            if (empty($_POST['user']) || empty($_POST['password'])) {
                $this->error = 'Please enter both a user namer and a password';
            } else {
                $this->user = new ConvertUser();
                if (!ConvertUser::authorized($_POST['user'] ,$_POST['password'])) {
                    $this->error = 'Invalid or unknown user or password';
                }
            }

            if (empty($this->error) && !empty($this->back)) {
                $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->back;
                header('Location: ' . $url);
                exit();
            }
        }

        $args=array(
            'title' => 'Converter Login Page',
            'name' => (ConvertUser::isLoggedIn() ? ConvertUser::username() : ''),
            'alert' => ((!$this->posted || !empty($this->error)) ? $this->error : 'You are logged in'),
            'user' => (!empty($_POST['user']) ? $_POST['user'] : ''),
            'back' => $this->back
        );
        $view = new ConvertLoginView();
        $view->renderPage($args);
    }


    public function logout()
    {
        if (ConvertUser::isLoggedIn()) {
            ConvertUser::logout();
        }
        self::gotoLoginPage(false);
    }


    public static function gotoLoginPage($setBackPath=true)
    {
        $url = (empty($_SERVER['HTTPS'])?'http://':'https://') .
            $_SERVER['HTTP_HOST'] . PAGE_LOGIN .
            (!$setBackPath ? '' : 
                '?back='.urlencode($_SERVER['PHP_SELF'] == '/index.php' ? '/' : $_SERVER['PHP_SELF']));
        header('Location: '.$url);
        exit();
    }

}