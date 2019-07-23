<?php
use Workerman\Worker;
require_once __DIR__ . '../vendor/autoload.php';

$global_uid = 0;

function handle_connection($connection) {
    global $global_uid;
    $connection->uid = ++$global_uid;
}

function handle_message($connection, $data) {
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        $conn->send("user[{$connection->uid}] said: $data");
    }
}

function handle_close($connection) {
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        $conn->send("user[{$connection->uid}] logout");
    }
}

$text_worker = new Worker("text://0.0.0.0:2347");

$text_worker->count = 4;
$text_worker->onConnect = 'handle_connection';
$text_worker->onMessage = 'handle_message';
$text_worker->onClose = 'handle_close';

Worker::runAll();
