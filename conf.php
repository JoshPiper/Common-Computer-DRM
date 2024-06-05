<?php
    // Central key
    define('APPLICATIONS_DIR', 'applications/');
    define('WEBHOOK_KEY', '');

    // Initialize the database
    require_once('class/Database.php');
    Database::init('mysql:dbname=;host=localhost', '', '');
