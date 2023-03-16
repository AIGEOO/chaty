<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use React\EventLoop\Loop;
use React\Socket\Connector;
use React\Socket\ConnectionInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$username = $argv[1];
$url = $_ENV['TCP_ADDRESS_URL'] . ':' . $_ENV['TCP_ADDRESS_PORT'];
$loop = Loop::get();

$stdin = new ReadableResourceStream(STDIN, $loop);
$stdout = new WritableResourceStream(STDOUT, $loop);

$client = new Connector();

$client->connect($url)->then(function (ConnectionInterface $connection) use ($stdin, $stdout, $username) {
    $stdin->on('data', function ($data) use ($connection) {
        $connection->write($data);
    });

    $connection->on('data', function ($data) use ($stdout, $username) {
        $message = trim($data);
        $stdout->write("$username> $message");
    });
});

$loop->run();