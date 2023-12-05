<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './models/AutentificadorJWT.php';


class AuthMiddleware
{
 
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
    
        try {
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();

            //$payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $payload = json_encode(array('mensaje' => $e));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarToken(Request $request, RequestHandler $handler): Response
    {
        //echo "hola";
        $header = $request->getHeaderLine('Authorization');


        // $token = trim(explode("Bearer", $header)[1]);
        $token = str_replace('Bearer ', '', $header);
        //var_dump($token);
        
        try {
           // echo "hola2222";
            AutentificadorJWT::VerificarToken($token);
           // echo "hola3333";
            $response = $handler->handle($request);
        } catch (Exception $e) {
            //echo $e;
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }


public static function verificarRol($rolesRequeridos) {
    return function (Request $request, RequestHandler $handler) use ($rolesRequeridos) {
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $response = new Response();

        try {
            AutentificadorJWT::VerificarToken($token);
            $datos = AutentificadorJWT::ObtenerData($token);
            $rol = $datos->rol;

            if (in_array($rol, $rolesRequeridos)) {
                $response = $handler->handle($request);
            } else {
                $rolesRequeridosStr = implode(', ', $rolesRequeridos);
                $payload = json_encode(array('mensaje' => 'No es posible acceder a este recurso, necesitas ser uno de los siguientes roles: ' . $rolesRequeridosStr));
                $response->getBody()->write($payload);
            }
        } catch (Exception $e) {
            $payload = json_encode(array('mensaje' => $e));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    };
}
}