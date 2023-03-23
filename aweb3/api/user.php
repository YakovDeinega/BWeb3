<?php

require "../database/DatabaseConnector.php";
require "../database/PersonController.php";

use Controller\PersonController;
use System\DatabaseConnector;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$dbConnection = (new DatabaseConnector())->getConnection();
$requestMethod = $_SERVER['REQUEST_METHOD'];

$username = $_GET['username'] ?? null;

$controller = new PersonController($dbConnection, $requestMethod, $username);
$controller->processRequest();


