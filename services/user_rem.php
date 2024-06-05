<?php
    function answer($code, $reason=NULL, $value=NULL){
        $answer = array(
            'status' => $code,
            'reason' => $reason,
            'value' => $value
        );

        header("Content-Type: application/json");
        echo(json_encode($answer));
        die();
    }

    if (!isset($_POST['steamid']) || !isset($_POST['server_key'])){
        answer(0, 'Invalid request.');
    }

    $steamid = $_POST['steamid'];
    $serverKey = $_POST['server_key'];

    require_once('class/Server.php');
    require_once('class/PlayerKey.php');
    require_once('class/ServerKey.php');

    $skMan = new ServerKeyManager();
    $serverKey = $skMan->getByKey($serverKey);

    if(!$serverKey){
        answer(0, 'Invalid key.'); // The key doesn't exists
    }

    if($serverKey->disabled()){
        answer(0, 'Your key is disabled.');
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    $servMan = new ServerManager();
    $serv = $servMan->getByServerKey($serverKey->id(), $ip);
    if(!$serv){
        answer(0, 'Invalid server.');
    }

    $serv->setLastActivity(new DateTime());
    if(!$servMan->update($serv)){
        answer(0, 'Failed to refresh the server.');
    }

    $pkMan = new PlayerKeyManager();
    if($pkMan->removeBySteamId($steamid, $serv->id())){
        answer(1, 'The player\'s key has been deleted.');
    }else{
        answer(0, 'Something went wrong while removing the player\'s key.');
    }