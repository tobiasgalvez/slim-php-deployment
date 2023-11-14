<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $mesa = new Mesa();
        $retorno = $mesa->crearMesa();

        if($retorno != null)
        {
          if(!is_numeric($retorno))
          {
            $payload = json_encode(array("error" => $retorno));
          }
          else
          {
            $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
          }
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $mesa = Mesa::obtenerMesa($id);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodas();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];

        $retorno = Mesa::modificarMesa($id);

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

        $retorno = Mesa::borrarMesa($id);

        if($retorno != null)
        {
          $payload = json_encode(array("mensaje" => $retorno));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}