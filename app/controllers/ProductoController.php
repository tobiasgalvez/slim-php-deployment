<?php

require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $tipo = $parametros['tipo'];
        $precio = $parametros['precio'];
        $descripcion = $parametros['descripcion'];

        // Creamos el producto
        $prod = new Producto();
        $prod->nombre = $nombre;
        $prod->tipo = $tipo;
        $prod->precio = $precio;
        $prod->descripcion = $descripcion;

        $retorno = $prod->crearProducto();

        if($retorno != null)
        {
          if(!is_numeric($retorno))
          {
            $payload = json_encode(array("error" => $retorno));
          }
          else
          {
            $payload = json_encode(array("mensaje" => "Producto creado con exito"));
          }
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos producto por id
        $id = $args['id'];
        $producto = Producto::obtenerProducto($id);
        $payload = json_encode($producto);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $productoAModificar = new Producto();
        $productoAModificar->id = $parametros['id'];
        $productoAModificar->nombre = $parametros['nombre'];
        $productoAModificar->tipo = $parametros['tipo'];
        $productoAModificar->precio = $parametros['precio'];
        $productoAModificar->descripcion = $parametros['descripcion'];

        $retorno = Producto::modificarProducto($productoAModificar);

        if($retorno != null)
        {
          $payload = json_encode(array("mensaje" => $retorno));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        //$parametros = $request->getParsedBody();

        $id = $args['id'];

        $retorno = Producto::borrarProducto($id);

        if($retorno != null)
        {
          $payload = json_encode(array("mensaje" => $retorno));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
