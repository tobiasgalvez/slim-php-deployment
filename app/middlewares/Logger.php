<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './models/AutentificadorJWT.php';

class LoggerMiddleware
{

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        // Fecha antes
        $before = date('Y-m-d H:i:s');
        
        // Continua al controlador
        $response = $handler->handle($request); // Aquí es correcto usar $request

        $existingContent = json_decode($response->getBody());

        // Después
        $response = new Response();
        $existingContent->fechaAntes = $before;
        $existingContent->fechaDespues = date('Y-m-d H:i:s');
        
        $payload = json_encode($existingContent);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerificarRol(Request $request, RequestHandler $handler): Response
    {   
        // $parametros = $request->getQueryParams();

        // $rol = $parametros['rol'];

        // if ($rol === 'admin') {
        //     $response = $handler->handle($request);
        // } else {
        //     $response = new Response();
        //     $payload = json_encode(array('mensaje' => 'No sos Admin'));
        //     $response->getBody()->write($payload);
        // }

        // return $response->withHeader('Content-Type', 'application/json');

        $token = $request->getHeader('Authorization');
    $datos = AutentificadorJWT::ObtenerData($token);
    $rol = $datos['rol'];

    if ($rol === 'admin') {
        $response = $handler->handle($request);
    } else {
        $response = new Response();
        $payload = json_encode(array('mensaje' => 'No sos Admin'));
        $response->getBody()->write($payload);
    }

    return $response->withHeader('Content-Type', 'application/json');
    }
}
