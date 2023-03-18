<?php

use function Termwind\{render};
use React\Socket\Connector;
use React\Socket\ConnectionInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;

function client() {
	global $loop, $url;

	$stdin = new ReadableResourceStream(STDIN, $loop);
	$stdout = new WritableResourceStream(STDOUT, $loop);

	$client = new Connector();

	$client->connect($url)->then(function (ConnectionInterface $connection) use ($stdin, $stdout) {
        $stdin->on('data', function ($data) use ($connection) {

	    $time = date('g:i a'); // e.g. 5:37 pm
            $connection->write("[$time]  <span class='font-bold'>$data</span> ");
        });

        $connection->on('data', function ($data) use ($stdout) {
            $message = trim($data);
            $stdout->write(render(<<<HTML
            <div>
                <div class="flex space-x-1">
                    <span class="flex-1 px-1 bg-gray-600">{$data}</span>
                </div>
            </div>
            HTML) . "> ");
        });
    });

	$loop->run();
}
