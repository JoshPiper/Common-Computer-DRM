<?php
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    require_once('conf.php');
    require_once('vendor/Macaw.php'); // Small Rooter Lib
    use \NoahBuscher\Macaw\Macaw;
    
    Macaw::error(function(){
        echo('Congratulations! You\'re definitely lost...');
    });

    Macaw::post('keygen', function(){
        include('services/keygen.php');
    });

    Macaw::post('usergen', function(){
        include('services/user_gen.php');
    });

    Macaw::post('userrem', function(){
        include('services/user_rem.php');
    });

    Macaw::post('userreset', function(){
        include('services/user_reset.php');
    });

    Macaw::any('admin', function(){
        include('admin/admin.php');
    });

    Macaw::any('applications/(:any)', function($app_id){
        $folders = array_diff(scandir(APPLICATIONS_DIR), array('.', '..'));
        if(in_array($app_id, $folders)){
            include("applications/$app_id/app.php");
        }else{
            echo('Application not found !');
        }
    });

    Macaw::dispatch();