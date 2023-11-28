<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use function PHPSTORM_META\type;

//require_once './JwtHandler.php';

class AuthController
{
    public function login(Request $request, Response $response) {
        // Get the username and password from the request body
        $data = $request->getParsedBody();
        $nombre_usuario = $data['usuario'];
        $clave = $data['clave'];
        
     
        // Assuming you have a function to validate the username and password
        // If the username and password are valid, generate a JWT token
        $usuarioEncontrado = Usuario::obtenerUsuarioPorNombreUsuario($nombre_usuario);

        

        $verificarHash = password_verify($clave, $usuarioEncontrado->clave);
     
        if ($usuarioEncontrado != null && $verificarHash) {
            $secret_key = 'tu_clave_secreta';
     
            $jwt_token = JwtHandler::generate_jwt_token($usuarioEncontrado->id, $usuarioEncontrado->tipo, $secret_key);
            //echo $jwt_token;
            $response_data = array('token' => $jwt_token);
            $response->getBody()->write(json_encode($response_data));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            // If the username and password are not valid, return an error response
            $response->getBody()->write('Invalid username or password');
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
     }
 
   function getProtectedResource(Request $request, Response $response) {

    $auth_header = $request->getHeader('Authorization');
  
    if (empty($auth_header)) {
        $response->getBody()->write('Unauthorized');
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
   
    $jwt_token = str_replace('Bearer ', '', $auth_header[0]);
    $secret_key = 'tu_clave_secreta';
   
    try {
        $decoded_payload = JwtHandler::validate_jwt_token($jwt_token, $secret_key);
        $user_id = $decoded_payload->sub;
        $role = $decoded_payload->role; // Aquí obtienes el rol del usuario del payload del token
   
        // Fetch the protected resource for the user
        if ($role === 'admin') {
            // Si el usuario es admin, permite el acceso a todos los recursos de la API
            $resource = 'Protegido para admin';
            $response->getBody()->write($resource);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            // Si el usuario no es admin, solo permite el acceso a algunos recursos de la API
            $resource = 'Protegido para usuario';
            $response->getBody()->write($resource);
            return $response->withHeader('Content-Type', 'application/json');
        }
    } catch (Exception $e) {
        $response->getBody()->write('Unauthorized');
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }


  
}

}