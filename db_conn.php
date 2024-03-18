<?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = 'mongodb+srv://saikumar2366063:knsaikumar@cluster0.v2wg102.mongodb.net/fullstack?retryWrites=true&w=majority';

// Create a MongoDB client instance
$client = new MongoDB\Client($uri);

// Select the database
$databaseName = 'trift_store'; 
$db = $client->$databaseName;

?>
