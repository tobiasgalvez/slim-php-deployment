<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        print("holaaaaaaaaaa");
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $tipo = $parametros['tipo'];
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $email = $parametros['email'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->nombre = $nombre;
        $usr->apellido = $apellido;
        $usr->tipo = $tipo;
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->email = $email;


        $retorno = $usr->crearUsuario();

        if($retorno != null)
        {
          if(!is_numeric($retorno))
          {
            $payload = json_encode(array("error" => $retorno));
          }
          else
          {
            $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
          }
        }


        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
      print("holaaaaaaaaaa traigo unooooooo");
        // Buscamos usuario por nombre
        $id = $args['id'];
        $usuario = Usuario::obtenerUsuario($id);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        
        $usuarioAModificar = new Usuario();
        $usuarioAModificar->id = $parametros['id'];
        $usuarioAModificar->nombre = $parametros['nombre'];
        $usuarioAModificar->apellido = $parametros['apellido'];
        $usuarioAModificar->tipo = $parametros['tipo'];
        $usuarioAModificar->usuario = $parametros['usuario'];
        $usuarioAModificar->clave = $parametros['clave'];
        $usuarioAModificar->email = $parametros['email'];



        $retorno = Usuario::modificarUsuario($usuarioAModificar);

        if($retorno != null)
        {
          $payload = json_encode(array("mensaje" => $retorno));
        }


        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];

        $retorno = Usuario::borrarUsuario($id);

        if($retorno != null)
        {
          $payload = json_encode(array("mensaje" => $retorno));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}