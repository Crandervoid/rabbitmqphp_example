<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest','testHost');
$channel = $connection->channel();

$channel->queue_declare('testQueue',false,true,false,false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
  echo ' [x] Received ', $msg->body, "\n";
  
  $registerCreds = json_decode($msg->body, true); 
  $name = $registerCreds['name'];
  $email = $registerCreds['email'];
  $password = $registerCreds['password'];
  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  // echo $password;
  // echo $password_hash;
  $mysqli = require __DIR__ . "/database.php";

  $sql = "INSERT INTO users (uname, email, password_hash)
        VALUES (?, ?, ?)";

  $stmt = $mysqli->stmt_init();

  if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
  }

  $stmt->bind_param("sss", $name, $email, $password_hash);

  if ($stmt->execute()) {
   // header("Location: signup-success.html");
    exit;
} else {
    
    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
};

$channel->basic_consume('testQueue','', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}
