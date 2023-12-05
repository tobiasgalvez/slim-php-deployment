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
use Fpdf\Fpdf;



require __DIR__ . '/../vendor/autoload.php';
require_once './db/AccesoDatos.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './middlewares/AuthMiddleware.php';
require_once './models/AutentificadorJWT.php';
require_once './controllers/CSVController.php';



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
})/*->add(new LoggerMiddleware())*/;


$app->group('/usuarios', function (RouteCollectorProxy $group)
{
    $group->post('[/]',      'UsuarioController:CargarUno');
    $group->get('/{id}',  'UsuarioController:TraerUno');
    $group->get('[/]',           'UsuarioController:TraerTodos')->add(\AuthMiddleware::class . ':verificarToken');
    $group->put('[/]',  'UsuarioController:ModificarUno');
    $group->delete('/{id}',  'UsuarioController:BorrarUno'); 

})->add(\AuthMiddleware::verificarRol(['admin', 'cocinero']));


$app->group('/productos', function (RouteCollectorProxy $group)
{
    $group->post('[/]',      'ProductoController:CargarUno');
    $group->get('/{id}',  'ProductoController:TraerUno');
    $group->get('[/]',           'ProductoController:TraerTodos');
    $group->put('[/]',  'ProductoController:ModificarUno');
    $group->delete('/{id}',  'ProductoController:BorrarUno'); 

});


$app->group('/mesa', function (RouteCollectorProxy $group)
{
    $group->post('[/]',      'MesaController:CargarUno');
    $group->get('/mesaMasUsada',  'MesaController:MesaMasUsada')->add(\AuthMiddleware::verificarRol(['admin']));;
    $group->get('/{id}',  'MesaController:TraerUno');
    $group->get('[/]',           'MesaController:TraerTodos')->add(\AuthMiddleware::verificarRol(['admin', 'mozo']));
    $group->put('[/]',  'MesaController:ModificarUno')->add(\AuthMiddleware::verificarRol(['admin', 'mozo']));
    $group->delete('/{id}',  'MesaController:BorrarUno'); 
    $group->put('/liberarMesa',  'MesaController:LiberarMesa');


})->add(\AuthMiddleware::class . ':verificarToken');

$app->group('/pedidos', function (RouteCollectorProxy $group)
{
    $group->post('[/]', 'PedidoController:CargarUno')->add(\AuthMiddleware::verificarRol(['admin', 'mozo']));
    $group->get('/estadisticas', 'PedidoController:Estadisticas30Dias')->add(\AuthMiddleware::verificarRol(['admin']));
    $group->get('/pedidosLargos', 'PedidoController:PedidosMasDeDosHoras')->add(\AuthMiddleware::verificarRol(['admin']));
    $group->get('/obtenerMejoresComentarios', 'PedidoController:MejoresComentarios')->add(\AuthMiddleware::verificarRol(['admin']));
    $group->get('/{id}', 'PedidoController:TraerUno')->add(\AuthMiddleware::verificarRol(['admin', 'mozo']));
    $group->get('[/]', 'PedidoController:TraerTodos')->add(\AuthMiddleware::verificarRol(['admin', 'mozo']));
    $group->put('/actualizarPrecioTotalPedido', 'PedidoController:ActualizarPrecioTotalAPedido')->add(\AuthMiddleware::verificarRol(['admin', 'mozo']));
    $group->put('/actualizarTiempoDemoraPedido', 'PedidoController:ActualizarTiempoEstimadoPedido')->add(\AuthMiddleware::verificarRol(['admin', 'mozo']));
    $group->put('/actualizarEstadoPedido', 'PedidoController:ActualizarEstadoPedido')->add(\AuthMiddleware::verificarRol(['bartender', 'cocinero', 'mozo']));
    $group->put('[/]', 'PedidoController:ModificarUno');
    $group->put('/{id}', 'PedidoController:AsignarHorarioSalidaPedido');
    $group->delete('/{id}', 'PedidoController:BorrarUno'); 
    $group->post('/tomarFotoPedido', 'PedidoController:SacarFotoDeMesa')->add(\AuthMiddleware::verificarRol(['mozo']));




    
})->add(\AuthMiddleware::class . ':verificarToken');

$app->group('/pedidosProducto', function (RouteCollectorProxy $group)
{
        $group->post('[/]', 'PedidoController:AgregarProductoAPedido')->add(\AuthMiddleware::verificarRol(['mozo']));
    
        $group->get('/{id}', 'PedidoController:TraerProductosDePedido')->add(\AuthMiddleware::verificarRol(['mozo', 'admin']));

        $group->put('[/]', 'PedidoController:CambiarEstadoProducto')->add(\AuthMiddleware::verificarRol(['bartender', 'cocinero']));

       // $group->put('/{id_usuario}', 'PedidoController:CambiarEstadoProductoAListoServir')->add(\AuthMiddleware::verificarRol(['bartender', 'cocinero']));

        $group->get('/{id}/{id_usuario}', 'PedidoController:ObtenerPendientesEmpleado')->add(\AuthMiddleware::verificarRol(['bartender', 'cocinero']));



})->add(\AuthMiddleware::class . ':verificarToken');

$app->group('/cliente', function (RouteCollectorProxy $group)
{
        $group->get('/{id}/{codigo}', 'PedidoController:MostrarTiempoDemoraPedido');
        $group->post('[/]', 'PedidoController:EncuestaCliente');


});



// JWT en login
$app->group('/auth', function (RouteCollectorProxy $group) {

    $group->post('/login', function (Request $request, Response $response) {    
      $parametros = $request->getParsedBody();
  
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];

              // Assuming you have a function to validate the username and password
        // If the username and password are valid, generate a JWT token
        $usuarioEncontrado = Usuario::obtenerUsuarioPorNombreUsuario($usuario);


        

        $verificarHash = password_verify($clave, $usuarioEncontrado->clave);
     
  
      if($usuarioEncontrado!=null && $verificarHash){ // EJEMPLO!!! Acá se deberia ir a validar el usuario contra la DB
        $datos = array('usuario' => $usuario,
                       'rol' => $usuarioEncontrado->tipo);
                       var_dump($datos);
  
        $token = AutentificadorJWT::CrearToken($datos);
        $payload = json_encode(array('jwt' => $token));
      } else {
        $payload = json_encode(array('error' => 'Usuario o clave incorrectos'));
      }
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    });
  
  });



 

// Cargar/Descargar CSV
$app->group('/csv', function (RouteCollectorProxy $group) {
  $group->post('/cargarUsuarios', \CSVController::class . ':cargarCSV');
  $group->get('/descargarUsuarios', \CSVController::class . ':descargarCSV');
})->add(\AuthMiddleware::class . ':verificarToken')->add(\AuthMiddleware::verificarRol(['admin']));




$app->get('/download', function(Request $request, Response $response, $args) {
  // Ruta al archivo JPEG
  $jpegPath = 'C:\xampp\htdocs\Programacion3\slim-php-deployment\app\Logo\logoEmpresa.jpeg'; // Reemplaza con la ruta correcta de tu archivo JPEG

      // Crear instancia de FPDF
      $pdf = new Fpdf();
      $pdf->AddPage();
      $pdf->Image($jpegPath, 10, 10, 190);
  
      // Obtener el contenido del PDF
      $pdfContent = $pdf->Output('S');
  
      // Configurar encabezados para la descarga del archivo
      $response = $response->withHeader('Content-Type', 'application/pdf');
      $response = $response->withHeader('Content-Disposition', 'attachment; filename=archivo.pdf');
  
      // Salida directa del contenido del PDF al cuerpo de la respuesta
      $response->getBody()->write($pdfContent);
  
      // Ruta de escritorio del usuario (puedes ajustar la ruta según el sistema operativo)
      $desktopPath = getenv('USERPROFILE') . '/Desktop';
  
      // Ruta completa para guardar el archivo en el escritorio
      $pdfPath = $desktopPath . '/logoEmpresa.pdf';
  
      // Guardar el archivo en el escritorio
      file_put_contents($pdfPath, $pdfContent);
  
      return $response;
})->add(\AuthMiddleware::class . ':verificarToken')->add(\AuthMiddleware::verificarRol(['admin']));




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
