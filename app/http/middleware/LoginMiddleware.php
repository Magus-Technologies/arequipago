<?php

class LoginMiddleware extends Middleware{
    public function valid()
    {
        if (isset($_SESSION['usuario_id'])){
            return true;
        }
        return false;
    }
    public function is_false()
    {
        header('Location: '.URL::to('login'));
        die();
    }

}