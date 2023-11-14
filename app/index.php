<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;


require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->safeLoad();

// print_r($_ENV);
//php -S localhost:666 -t app
// Instantiate App
$app = AppFactory::create();

// $app->setBasePath('/app');
//$app->setBasePath('/slim-php-deployment/app/');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/usuarios',      'UsuarioController:CargarUno');
$app->get('/usuarios/{id}',  'UsuarioController:TraerUno');
$app->get('/usuarios',           'UsuarioController:TraerTodos');
$app->put('/usuarios/{id}',  'UsuarioController:ModificarUno');
$app->delete('/usuarios/{id}',  'UsuarioController:BorrarUno');

$app->post('/productos',      'ProductoController:CargarUno');
$app->get('/productos/{id}',      'ProductoController:TraerUno');
$app->get('/productos',      'ProductoController:TraerTodos');


$app->get('/test', function (Request $request, Response $response) {
    $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/test', function (Request $request, Response $response) {
    $payload = json_encode(array('method' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
