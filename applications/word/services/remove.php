<?php
    require_once(__DIR__ . '/../class/Document.php');

    $docMan = new DocumentManager();

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

    if(!isset($_POST['doc_id'])){
        answer(0, 'Invalid request');
    }

    $doc_id = intval($_POST['doc_id']);
    $doc = $docMan->getById($doc_id);

    if(!$doc){
        answer(0, 'The document wasn\'t found');
    }

    if($doc->serverKey() != $server->serverKey()){
        answer(0, 'Wrong server');
    }

    if($doc->steamId() == $player_key->steamId() || $player_key->admin()){
        if($docMan->remove($doc_id)){
            answer(1, 'The document was removed');
        }else{
            answer(0, 'Something went wrong.');
        }
    }else{
        answer(0, 'You\'re not allowed to remove this document');
    }