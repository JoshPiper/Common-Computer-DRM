<?php
    // See https://www.gmodstore.com/help/developers/topics/file-string-replacements
    if (!isset($_POST['steamid']) || !isset($_POST['script_id']) || !isset($_POST['version_name']) || !isset($_POST['extra'])) {
        echo('Variables aren\'t set, please contact Feeps !');
        return;
    }

    $steamid = $_POST['steamid'];
    $script_id = $_POST['script_id'];
    $version_name = $_POST['version_name'];
    $secretkey = $_POST['extra'];

    if (!hash_equals($secretkey, WEBHOOK_KEY)) { // TimingAttack Safe
        echo('Secret key isn\'t valid, please contact Feeps !');
        return;
    }

    require_once('class/ServerKey.php');
    $skMan = new ServerKeyManager();

    $server_key = $skMan->getBySteamId($steamid);
    if(!$server_key){
        // Generate a new key and be sure that it doesn't exists yet
        while(true){
            $generated_key = bin2hex(random_bytes(16));
            if(!$skMan->getByKey($generated_key)){
                break;
            }
        }

        $server_key = new ServerKey(null, $steamid, 0, $generated_key, new DateTime('NOW'));
        $skMan->add($server_key);
    }
    
    if($server_key->disabled()){
        echo("Your key is disabled, please contact the support !");
    }else{
        echo(base64_encode($server_key->key()));
    }
