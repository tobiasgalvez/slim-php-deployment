<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './models/JwtHandler.php';

class AuthMiddleware
{
    private $secret_key;
    private $jwtHandler;

    public function __construct($secret_key) {
        $this->secret_key = $secret_key;
        $this->jwtHandler = new JwtHandler();
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
       // echo "hola";
        $auth_header = $request->getHeader('Authorization');

        if (empty($auth_header)) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Token no proporcionado'));
            $response->getBody()->write($payload);
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $jwt_token = str_replace('Bearer ', '', $auth_header[0]);

        try {

            $decoded_payload = $this->jwtHandler->validate_jwt_token($jwt_token, $this->secret_key);
            $id_usuario = $decoded_payload->sub;

            $request = $request->withAttribute('id_usuario', $id_usuario);
            $response = $handler->handle($request);
            return $response;
        } catch (Exception $e) {
            echo $e;
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'Token no valido'));
            $response->getBody()->write($payload);
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}