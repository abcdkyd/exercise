<?php
use Workerman\Worker;
require_once __DIR__ . '/../vendor/autoload.php';

$json_worker = new Worker('JsonNL://0.0.0.0:2348');
$json_worker->onMessage = function ($connection, $data) {
    echo $data;

    $connection->send(['code' => 0, 'msg' => '0k']);
};

Worker::runAll();
