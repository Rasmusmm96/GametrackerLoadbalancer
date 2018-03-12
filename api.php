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

    return $response->withStatus(200)->write('Testing');
});

$app->get('/games', function (Request $request, Response $response, array $args) {
    global $loadbalancer;

    $result = $loadbalancer->getGames();

    if (!$result) {
        return $response->withStatus(500)->write("Servers are offline");
    }

    return $response->withStatus(200)->write($result);
});

$app->get('/games/{id}', function (Request $request, Response $response, array $args) {
    global $loadbalancer;

    $id = $args['id'];

    $result = $loadbalancer->getGame($id);

    if (!$result) {
        return $response->withStatus(500)->write("Servers are offline");
    }

    return $response->withStatus(200)->write($result);
});

$app->run();
