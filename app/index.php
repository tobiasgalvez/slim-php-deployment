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
require_once './controllers/AuthController.php';
require_once './middlewares/AuthMiddleware.php';
require_once './middlewares/Logger.php';

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
// $dotenv->safeLoad();

// print_r($_ENV);
//php -S localhost:666 -t app
// Instantiate App
$app = AppFactory::create();


// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new LoggerMiddleware());




// $app->group('/usuarios', function ($app) {
//     $app->get('[/]', 'AuthController:getProtectedResource');
//  })->add(new AuthMiddleware('tu_clave_secreta'));


$app->group('/usuarios', function (RouteCollectorProxy $group)
{
   $group->get('[/]', 'AuthController:getProtectedResource');
    $group->post('[/]',      'UsuarioController:CargarUno');
    $group->get('/{id}',  'UsuarioController:TraerUno');
    $group->get('[/]',           'UsuarioController:TraerTodos');
    $group->put('[/]',  'UsuarioController:ModificarUno');
    $group->delete('/{id}',  'UsuarioController:BorrarUno'); 

})->add(new AuthMiddleware("tu_clave_secreta"));


$app->group('/productos', function (RouteCollectorProxy $group)
{
    $group->post('[/]',      'ProductoController:CargarUno');
    $group->get('/{id}',  'ProductoController:TraerUno');
    $group->get('[/]',           'ProductoController:TraerTodos');
    $group->put('[/]',  'ProductoController:ModificarUno');
    $group->delete('/{id}',  'ProductoController:BorrarUno'); 

});


$app->group('/mesas', function (RouteCollectorProxy $group)
{
    $group->post('[/]',      'MesaController:CargarUno');
    $group->get('/{id}',  'MesaController:TraerUno');
    $group->get('[/]',           'MesaController:TraerTodos');
    $group->put('[/]',  'MesaController:ModificarUno');
    $group->delete('/{id}',  'MesaController:BorrarUno'); 

});

$app->group('/pedidos', function (RouteCollectorProxy $group)
{
    $group->post('[/]',      'PedidoController:CargarUno');
    $group->get('/{id}',  'PedidoController:TraerUno');
    $group->get('[/]',           'PedidoController:TraerTodos');
    $group->put('[/]',  'PedidoController:ModificarUno');
    $group->put('/{id}',  'PedidoController:AsignarHorarioPedido');
    $group->delete('/{id}',  'PedidoController:BorrarUno'); 

});

$app->group('/pedidosProducto', function (RouteCollectorProxy $group)
{
        $group->post('[/]', 'PedidoController:AgregarProductoAPedido');
    
        $group->get('/{id}', 'PedidoController:TraerProductosDePedido');

});




$app->group('/login', function (RouteCollectorProxy $group)
{
   $group->post('[/]', 'AuthController:login');
});





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
