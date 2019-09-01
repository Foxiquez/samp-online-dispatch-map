<?php

namespace DispatchMap;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Map implements MessageComponentInterface {

    protected $clients;
    protected $logger;

    public function __construct() {
        $this->clients = new \SplObjectStorage;

        $this->logger = new Logger('server');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../tmp/server.log', Logger::DEBUG));
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);

        $log = "New connection! IP: {$conn->remoteAddress}, ID: {$conn->resourceId}" . PHP_EOL;
        $this->logger->info($log);

        if (Config::$debug) echo $log;
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msgArray = json_decode($msg, true);

        if ($msgArray)
        {
            if ($msgArray['key'] == Config::$securityKey)
            {
                unset($msgArray['key']);
                $msgArray = json_encode($msgArray, JSON_UNESCAPED_UNICODE);

                foreach ($this->clients as $client)
                {
                    $client->send($msgArray);
                }
            }
            else
            {
                $log = "Unauthorized try to send message from: IP: {$from->remoteAddress}, ID: {$from->resourceId}" . PHP_EOL;
                $this->logger->info($log);

                if (Config::$debug) echo $log;
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);

        $log = "Connection {$conn->resourceId} has disconnected" . PHP_EOL;
        $this->logger->info($log);

        if (Config::$debug) echo $log;
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();

        $log = "An error has occurred: {$e->getMessage()}, with {$conn->resourceId}" . PHP_EOL;
        $this->logger->info($log);

        if (Config::$debug) echo $log;
    }
}