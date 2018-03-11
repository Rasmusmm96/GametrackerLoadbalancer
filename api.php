<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

require 'vendor/autoload.php';
require_once 'DLL/loadbalancer.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$c = new \Slim\Container($configuration);
$app = new App($c);
$loadbalancer = new LoadBalancer();

$app->get("/", function (Request $request, Response $response) {
    return $response->withStatus(200)->write(file_get_contents("docs.html"));
});

$app->get("/test", function (Request $request, Response $response) {
    global $loadbalancer;

    $result = $loadbalancer->pickServer();

    return $response->withStatus(200)->write(json_encode($result));
});

$app->get('/games', function (Request $request, Response $response, array $args) {
    global $loadbalancer;

    $result = $loadbalancer->getGames();

    return $response->withStatus(200)->write($result);
});

$app->get('/games/{id}', function (Request $request, Response $response, array $args) {
    global $loadbalancer;

    $id = $args['id'];

    $result = $loadbalancer->getGame($id);

    return $response->withStatus(200)->write($result);
});

$app->run();
