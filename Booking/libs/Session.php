<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 3/10/2016
 * Time: 1:11 AM
 */
class Session
{
    public $data = array();

    public function __construct( $session_id='',$key = 'default')
    {
        
        session_start();

        if ($session_id) {
            session_id($session_id);
        }

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = array();

        }

        $this->data =& $_SESSION[$key];
    }



    public function start()
    {
        return session_start();
    }

    public function destroy()
    {
        return session_destroy();
    }
}