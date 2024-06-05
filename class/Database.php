<?php

class Database{
    private static $instance;

    public static function init($dsn, $user, $pass){
        if(!is_null(self::$instance)){
            throw new Exception('The database is already initialized !');
            return;
        }

        self::$instance = new PDO($dsn, $user, $pass);
    }

    public static function getInstance(){  
        return self::$instance;
    }
}