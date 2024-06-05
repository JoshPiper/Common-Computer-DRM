<?php
    // Hard coded for the moment ( Pas le time :( )
    if(isset($_GET['server_key'])){
        require_once('class/Server.php');
        require_once('class/ServerKey.php');

        $skMan = new ServerKeyManager();
        $sk = $skMan->getByKey($_GET['server_key']);

        if(!$sk){
            echo("oh no");
            die();
        }

        if($sk->disabled()){
            die();
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $servMan = new ServerManager();
        $server = $servMan->getByServerKey($sk->id(), $ip);

        if(!$server){
            echo("error");
            die();
        }
        
        require('services/article.php');
        die();
    }

    if(!isset($_GET['player_key'])){
        echo('You\'re not logged in !');
        die();
    }

    require_once('class/PlayerKey.php');
    $pkMan = new PlayerKeyManager();
    $player_key = $pkMan->getByKey($_GET['player_key']);

    if(!$player_key){
        echo('Invalid account !');
        die();
    }

    require_once('class/Server.php');
    $servMan = new ServerManager();
    $server = $servMan->getById($player_key->server());

    if(!$server){
        echo('Are you in another space time ?');
        die();
    }

    require_once('class/ServerKey.php');
    $skMan = new ServerKeyManager();
    $sk = $skMan->getById($server->serverKey());

    if(!$sk){
        echo('Invalid serial key');
        die();
    }

    if($sk->disabled()){
        echo('Your key is disabled');
        die();
    }

    if(isset($_GET['service'])){
        $service = $_GET['service'] . '.php';

        $folders = array_diff(scandir(__DIR__ . '/services'), array('.', '..'));
        if(in_array($service, $folders)){
            include(__DIR__ . "/services/$service");
        }else{
            echo('Service not found !');
        }
        
    }elseif(isset($_GET['view'])){
        $id = $_GET['view'];
        if($id == 'config'){
            if($player_key->admin()){
                include(__DIR__ . '/config.php');
            }else{
                echo('You\'re not allowed to view this page !');
            }
        }
    }else{
        include(__DIR__ . '/home.php');
    }