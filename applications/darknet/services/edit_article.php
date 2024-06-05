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

    if(!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['imageUrl']) || !isset($_POST['description']) || !isset($_POST['class']) || !isset($_POST['price'])){
        answer(0, 'Invalid request');
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $imageUrl = $_POST['imageUrl'];
    $description = $_POST['description'];
    $class = $_POST['class'];
    $price = $_POST['price'];

    $article = $artMan->getById($id);
    if(!$article){
        answer(0, 'Invalid article');
    }

    if(!$player_key->admin() || $article->serverKey() != $server->serverKey()){
        answer(0, 'You\'re not allowed to edit this article');
    }

    $article->setName($name);
    $article->setImageUrl($imageUrl);
    $article->setDescription($description);
    $article->setClass($class);
    $article->setPrice($price);

    if($artMan->update($article)){
        answer(1, 'The article has been updated', array(
            'id' => $id,
            'name' => $name,
            'imageUrl' => $imageUrl,
            'description' => $description,
            'class' => $class,
            'price' => $price
        ));
    }else{
        answer(0, 'An error occured');
    }