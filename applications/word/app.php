<?php
    if(!isset($_GET['player_key'])){
        echo('You\'re not logged in !');
        return;
    }

    require_once('class/PlayerKey.php');
    $pkMan = new PlayerKeyManager();
    $player_key = $pkMan->getByKey($_GET['player_key']);

    if(!$player_key){
        echo('Invalid account !');
        return;
    }

    require_once('class/Server.php');
    $servMan = new ServerManager();
    $server = $servMan->getById($player_key->server());

    if(!$server){
        echo('Are you in another space time ?');
        return;
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
        }elseif($id == 'browser'){
            include(__DIR__ . '/browser.php');
        }else{
            include(__DIR__ . '/view.php');
        }
    }else{
        include(__DIR__ . '/editor.php');
    }