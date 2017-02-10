<?php

use Api\Data_Access\Dbhandler;
use Api\Core;

/*
* User Route
*/

$app->group('/user/', function () {

    $this->get('test', function ($req, $res, $args) {
        return $res->getBody()
                   ->write('Hello Users');
    });

    $this->get('', function ($req, $res, $args) {
        $um = new dbHandler();
        
        return $res
           ->withHeader('Content-type', 'application/json')
           ->getBody()
           ->write(
            json_encode(
                $um->getAll('users', '*', null, '')
            )
        );
    });
});

