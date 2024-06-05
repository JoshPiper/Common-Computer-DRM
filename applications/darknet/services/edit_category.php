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

    if(!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['imageUrl']) || !isset($_POST['description']) || !isset($_POST['footer'])){
        answer(0, 'Invalid request');
    }

    $cat_id = $_POST['id'];
    $name = $_POST['name'];
    $imageUrl = $_POST['imageUrl'];
    $description = $_POST['description'];
    $footer = $_POST['footer'];

    $category = $catMan->getById($cat_id);
    if(!$category){
        answer(0, 'Invalid category');
    }

    if(!$player_key->admin() || $category->serverKey() != $server->serverKey()){
        answer(0, 'You\'re not allowed to edit this category');
    }

    $category->setName($name);
    $category->setImageUrl($imageUrl);
    $category->setDescription($description);
    $category->setFooter($footer);

    if($catMan->update($category)){
        answer(1, 'The category has been updated', array(
            'id' => $cat_id,
            'name' => $name,
            'imageUrl' => $imageUrl,
            'description' => $description,
            'footer' => $footer
        ));
    }else{
        answer(0, 'An error occured');
    }