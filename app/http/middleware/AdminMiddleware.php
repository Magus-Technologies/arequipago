<?php

class AdminMiddleware extends Middleware
{
    public function valid()
    {
        return isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1;
    }

    public function is_false()
    {
        header('Location: '.URL::base());
        exit();
    }

}