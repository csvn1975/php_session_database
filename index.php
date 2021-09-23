<?php
// error_reporting(E_ALL);
// ini_set("display_errors","On");

require_once 'autoload.php';
require_once './config/database.php';

$pdo = new PDO('mysql:host=' . DB_HOST . '; dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);

$sessionHandler = new Core\Classes\DBSessionHandler($pdo);
session_set_save_handler($sessionHandler, false);

session_start();

$currentUserId = 33333;
$_SESSION['currentUserId'] = $currentUserId;
$_SESSION['cart'] = [
    [
        'user_id' => $currentUserId,  
        'product_id' => random_int(1, 100000),  
    ]
];



# if call this => add new session, otherwise update session
#session_regenerate_id();
#session_gc(); # expired automatic delete
# session_destroy(); # => $sessionHandler->destroy

$pdo = null;