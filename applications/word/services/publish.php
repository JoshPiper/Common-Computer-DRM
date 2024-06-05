<?php
    require_once(__DIR__ . '/../class/Document.php');
    require_once(__DIR__ . '/../class/Category.php');
    require_once(__DIR__ . '/../class/Background.php');

    $docMan = new DocumentManager();
    $bgMan = new BackgroundManager();
    $catMan = new CategoryManager();

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


    if(!isset($_POST['title']) || !isset($_POST['category']) || !isset($_POST['content']) || !isset($_POST['background'])){
        answer(0, 'Invalid request');
    }

    $title = trim($_POST['title']);
    $content = $_POST['content'];
    $cat_id = $_POST['category'];
    $bg_id = $_POST['background'];

    if(empty($title)){
        answer(0, 'The title can\'t be empty');
    }

    if(empty($content)){
        answer(0, 'The document is empty');
    }

    $category = $catMan->getById($cat_id);
    if(!$category || $category->serverKey() != $server->serverKey()){
        answer(0, 'Invalid category');
    }

    $bg = $bgMan->getById($bg_id);
    if(!$bg || $bg->serverKey() != $bg->serverKey()){
        answer(0, 'Invalid background');
    }

    if(isset($_POST['id'])){
        // Edit the document
        $doc_id = $_POST['id'];
        $doc = $docMan->getById($doc_id);

        if(!$doc){
            answer(0, 'The document wasn\'t found');
        }

		if($doc->serverKey() != $server->serverKey() || (!$player_key->admin() && $doc->steamId() != $player_key->steamId())){
            answer(0, 'You\'re not allowed to edit this document');
        }
        
        $doc->setTitle($title);
        $doc->setContent($content);
        $doc->setCategory($cat_id);
        $doc->setBackground($bg_id);

        if($docMan->update($doc)){
            answer(1, 'The document has been updated');
        }else{
            answer(0, 'Something went wrong');
        }
    }else{
        // Check if the server reaches the max documents (2048)
        if($docMan->countByServerKey($server->serverKey()) >= 2048){
            answer(0, 'The server has reached the maximum number of documents !');
        }

        if(!$player_key->admin() && $docMan->countBySteamId($server->serverKey(), $player_key->steamId()) >= 128){
            answer(0, 'You reached the maximum number of documents !');
        }

        $doc = new Document(null, $player_key->steamId(), $server->serverKey(), $cat_id, $bg_id, $title, $content, new DateTime());
        if($docMan->add($doc)){
            answer(1, 'A document has been created');
        }else{
            answer(0, 'Something went wrong');
        }
    }
