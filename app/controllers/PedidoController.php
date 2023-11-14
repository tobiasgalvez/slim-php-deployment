<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $precio_total = $parametros['precio_total'];
        $id_producto = $parametros['id_producto'];
        $id_usuario = $parametros['id_usuario'];
        $id_mesa = $parametros['id_mesa'];
        $codigo = $parametros['codigo'];
        $nombre_cliente = $parametros['nombre_cliente'];
        $tiempo_demora = $parametros['tiempo_demora'];

        $pedido = new Pedido();
        $pedido->precio_total = $precio_total;
        $pedido->id_producto = $id_producto;
        $pedido->id_usuario = $id_usuario;
        $pedido->id_mesa = $id_mesa;
        $pedido->codigo = $codigo;
        $pedido->nombre_cliente = $nombre_cliente;
        $pedido->tiempo_demora = $tiempo_demora;

        $retorno = $pedido->crearPedido();

        if($retorno != null)
        {
          if(!is_numeric($retorno))
          {
            $payload = json_encode(array("error" => $retorno));
          }
          else
          {
            $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
          }
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $pedido = Pedido::obtenerPedido($id);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedidoAModificar = new Pedido();
        $pedidoAModificar->id = $parametros['id'];
        $pedidoAModificar->precio_total = $parametros['precio_total'];
        $pedidoAModificar->id_producto = $parametros['id_producto'];
        $pedidoAModificar->id_usuario = $parametros['id_usuario'];
        $pedidoAModificar->id_mesa = $parametros['id_mesa'];
        $pedidoAModificar->codigo = $parametros['codigo'];
        $pedidoAModificar->nombre_cliente = $parametros['nombre_cliente'];
        $pedidoAModificar->tiempo_demora = $parametros['tiempo_demora'];

        $retorno = Pedido::modificarPedido($pedidoAModificar);

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

        $retorno = Pedido::borrarPedido($id);

        if($retorno != null)
        {
          $payload = json_encode(array("mensaje" => $retorno));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
