<?php
    require_once(__DIR__ . '/../class/Article.php');
    $artMan = new ArticleManager();

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

    if(!isset($_POST['id'])){
        answer(0, 'Invalid request');
    }

    $art_id = $_POST['id'];
    
    $article = $artMan->getById($art_id);
    if(!$article){
        answer(0, 'Invalid article');
    }

    if(!$player_key->admin() || $article->serverKey() != $server->serverKey()){
        answer(0, 'You\'re not allowed to remove this article');
    }

    if($artMan->remove($art_id)){
        answer(1, 'The article has been removed');
    }else{
        answer(0, 'An error occured');
    }