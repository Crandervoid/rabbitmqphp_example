
<?php

require_once('vendor/autoload.php');
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if (empty($_POST["name"])) {
    die("Name is required");
}

if ( ! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if ( ! preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if ( ! preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

//$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
$password = $_POST['password'];
$email = $_POST["email"];
$name = $_POST["name"];

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest', 'testHost');
$channel = $connection->channel();

$channel->queue_declare('testQueue', true);

$registerCreds = json_encode(['name'=> $name, 'email' => $email, 'password' => $password]);

$msg = new AMQPMessage($registerCreds);

$channel->basic_publish($msg, 'testExchange', 'user');


echo "sent message";

$channel->close();
$connection->close();

/*
$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO users (uname, email, password_hash)
        VALUES (?, ?, ?)";
        
$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash);
                  
if ($stmt->execute()) {

    header("Location: signup-success.html");
    exit;
    
} else {
    
    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
 */






