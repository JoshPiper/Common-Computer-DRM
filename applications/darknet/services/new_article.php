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

    if(!isset($_POST['name']) || !isset($_POST['imageUrl']) || !isset($_POST['description']) || !isset($_POST['class']) || !isset($_POST['price']) || !isset($_POST['category'])){
        answer(0, 'Invalid request');
    }

    $name = $_POST['name'];
    $description = $_POST['description'];
    $imageUrl = $_POST['imageUrl'];
    $class = $_POST['class'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    if(!$player_key->admin()){
        answer(0, 'You\'re not allowed to use this service');
    }

    if($artMan->countByServerKey($server->serverKey()) >= 255){
        answer(0, 'The maximum number of darknet_articles has been exceeded.');
    }
    
    $article = new Article(null, $name, $imageUrl, $server->serverKey(), $price, $description, $category, $class);
    if($id = $artMan->add($article)){
        answer(1, 'The article has been added !', array(
            'id' => $id,
            'name' => $name,
            'imageUrl' => $imageUrl,
            'price' => $price,
            'description' => $description,
            'category' => $category,
            'class' => $class
        ));
    }else{
        answer(0, 'Failed to insert the article');
    }