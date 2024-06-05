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

    if(!isset($_POST['id'])){
        answer(0, 'Invalid request');
    }

    $cat_id = $_POST['id'];
    
    $category = $catMan->getById($cat_id);
    if(!$category){
        answer(0, 'Invalid category');
    }

    if(!$player_key->admin() || $category->serverKey() != $server->serverKey()){
        answer(0, 'You\'re not allowed to remove this category');
    }

    if($catMan->remove($cat_id)){
        answer(1, 'The category has been removed');
    }else{
        answer(0, 'An error occured');
    }