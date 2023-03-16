<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use React\Socket\ConnectionInterface;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$url = $_ENV['TCP_ADDRESS_URL'] . ':' . $_ENV['TCP_ADDRESS_PORT'];
$loop = Loop::get();

$socket = new SocketServer($url, [], $loop);

$clients = new \SplObjectStorage();

$socket->on('connection', function (ConnectionInterface $connection) use ($clients) {
    echo "New connection from " . $connection->getRemoteAddress() . "\n";

    $clients->attach($connection);

    $connection->write("Welcome to the chat!\n");

    $connection->on('data', function ($data) use ($connection, $clients) {
        foreach ($clients as $client) {
            if ($client !== $connection) {
                $client->write($data);
            }
        }
    });
});

echo "Listening on " . $socket->getAddress() . "\n";

$loop->run();