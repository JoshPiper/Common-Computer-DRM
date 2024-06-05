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

    $id = $_POST['id'];

    $article = $artMan->getById($id);
    if(!$article){
        answer(0, 'Invalid article!');
    }

    if($article->serverKey() != $server->serverKey()){
        answer(0, 'You\'re not allowed to view this article');
    }

    answer(1, 'The article has been sent', array(
        'id' => $article->id(),
        'name' => $article->name(),
        'imageUrl' => $article->imageUrl(),
        'description' => $article->description(),
        'class' => $article->class(),
        'price' => $article->price()
    ));