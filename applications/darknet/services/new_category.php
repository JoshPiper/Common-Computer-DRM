<?php
    require_once(__DIR__ . '/../class/Category.php');
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

    if(!isset($_POST['name']) || !isset($_POST['imageUrl']) || !isset($_POST['description']) || !isset($_POST['footer'])){
        answer(0, 'Invalid request');
    }

    $name = $_POST['name'];
    $imageUrl = $_POST['imageUrl'];
    $description = $_POST['description'];
    $footer = $_POST['footer'];

    if(!$player_key->admin()){
        answer(0, 'You\'re not allowed to use this service');
    }

    if($catMan->countByServerKey($server->serverKey()) >= 128){
        answer(0, 'The maximum number of darknet_categories has been exceeded.');
    }

    $category = new Category(null, $name, $imageUrl, $server->serverKey(), $description, $footer);
    if($id = $catMan->add($category)){
        answer(1, 'Category created', array(
            'id' => $id,
            'name' => $name,
            'imageUrl' => $imageUrl,
            'description' => $description,
            'footer' => $footer
        ));
    }else{
        answer(0, 'Failed to insert category.');
    }
    