<?php
require_once('Route.php');

use Steampixel\Route;

Route::add('/', function () {
    $json = array(
        'status' => 'ok',
        'message' => 'Welcome to the api',
        'content' => 'This is the content'
    );
    header('Content-type: applicantion/json');
    return json_encode($json);
});
Route::add('/gallery', function () {
    $db = new mysqli('localhost', 'root', '', 'forum-wedkarskie');
    $result = $db->query('SELECT * FROM galeria');
    $photos = array();
    while ($row = $result->fetch_assoc()) {
        $photos[] = $row;
    }
    $json = array(
        'status' => 'ok',
        'message' => 'Here are the photos',
        'content' => $photos

    );
    header('Content-type: applicantion/json');
    return json_encode($json);
});
Route::run('/api');
