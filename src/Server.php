<?php

use function Termwind\{render};
use React\Socket\SocketServer;
use React\Socket\ConnectionInterface;

function server() {
	global $loop, $url;	

	$socket = new SocketServer($url, [], $loop);

	$clients = new \SplObjectStorage();

	$socket->on('connection', function (ConnectionInterface $connection) use ($clients) {
	    echo "New connection from " . $connection->getRemoteAddress() . "\n";

	    $clients->attach($connection);
	    
	    $connection->write(render('<p class="justify-center bg-green-900 px-1 font-bold">User Connected successfully</p>'));

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
}

function isNotListening()
{
   return empty(exec('netstat -an | grep ' . $_ENV['TCP_ADDRESS_PORT']));
}
