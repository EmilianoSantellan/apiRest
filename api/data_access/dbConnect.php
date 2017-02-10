<?php

namespace Api\Data_Access;

use PDO;
use Api\Config;

class dbConnect {
    public static function StartUp()
    {
        //include_once '../config.php';

        $cnn = "mysql:host=localhost;dbname=shop;charset=utf8";

        $pdo = new PDO($cnn, "root", "");
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        
        return $pdo;
    }
}