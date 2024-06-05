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

    if (!isset($_POST['steamid']) || !isset($_POST['server_key']) || !isset($_POST['admin'])){
        answer(0, 'Invalid request');
    }

    $steamid = $_POST['steamid'];
    $serverKey = $_POST['server_key'];
    $isAdmin = $_POST['admin'] == "true" ? 1 : 0;
    
    require_once('class/Server.php');
    require_once('class/PlayerKey.php');
    require_once('class/ServerKey.php');

    $skMan = new ServerKeyManager();
    $serverKey = $skMan->getByKey($serverKey); // TODO Make a protection against Timing Attack (SQL)

    if(!$serverKey){
        answer(0, 'Invalid key'); // The key doesn't exists
    }
    
    if($serverKey->disabled()){
        answer(0, 'Your key is disabled.');
    }

    $ip = $_SERVER['REMOTE_ADDR'];

    // Create/Update server activity
    $servMan = new ServerManager();
    $serv = $servMan->getByServerKey($serverKey->id(), $ip);
    if(!$serv){
        if($servMan->countByServerKey($serverKey->id()) >= 16){
            answer(0, 'The maximum number of servers has been exceeded.');
        }

        $serv = new Server(null, $serverKey->id(), $ip, new DateTime());
        $id = $servMan->add($serv);

        if(!$id){
            answer(0, 'Failed to insert the new server.');
        }

        $serv->setId($id);
    }else{
        $serv->setLastActivity(new DateTime());
        if(!$servMan->update($serv)){
            answer(0, 'Failed to refresh the server.');
        }
    }

    $pkMan = new PlayerKeyManager();
    $playerKey = $pkMan->getBySteamId($steamid, $serv->id());
    if(!$playerKey){
        if($pkMan->countByServer($serv->id()) >= 255){
            answer(0, 'The maximum number of playerkey has been exceeded.');
        }
        
        while(true){
            $generated_key = bin2hex(random_bytes(16));
            if(!$pkMan->getByKey($generated_key)){
                break;
            }
        }
        
        $playerKey = new PlayerKey(null, $steamid, $serv->id(), $generated_key, $isAdmin, new DateTime());
        if(!$pkMan->add($playerKey)){
            answer(0, 'Failed to insert the player\'s key.');
        }
    }else{
        $playerKey->setAdmin($isAdmin);
        $playerKey->setLastActivity(new DateTime());
        if(!$pkMan->update($playerKey)){
            answer(0, 'Failed to update the player\'s key.');
        }
    }

    answer(1, 'The player\'s key has been sent', array(
        'key' => base64_encode($playerKey->key())
    ));
