<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    //$precio_total = $parametros['precio_total'];
    //$id_producto = $parametros['id_producto'];
    $id_usuario = $parametros['id_usuario'];
    $id_mesa = $parametros['id_mesa'];
    $codigo = $parametros['codigo'];
    $nombre_cliente = $parametros['nombre_cliente'];
    //$tiempo_demora = $parametros['tiempo_demora'];

    $pedido = new Pedido();
    //$pedido->precio_total = $precio_total;
    //$pedido->id_producto = $id_producto;
    $pedido->id_usuario = $id_usuario;
    $pedido->id_mesa = $id_mesa;
    $pedido->codigo = $codigo;
    $pedido->nombre_cliente = $nombre_cliente;
    //$pedido->tiempo_demora = $tiempo_demora;

    $retorno = $pedido->crearPedido();

    if ($retorno != null) {
      if (!is_numeric($retorno)) {
        $payload = json_encode(array("error" => $retorno));
      } else {
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
    //$pedidoAModificar->id_producto = $parametros['id_producto'];
    $pedidoAModificar->id_usuario = $parametros['id_usuario'];
    $pedidoAModificar->id_mesa = $parametros['id_mesa'];
    $pedidoAModificar->codigo = $parametros['codigo'];
    $pedidoAModificar->nombre_cliente = $parametros['nombre_cliente'];
    $pedidoAModificar->tiempo_demora = $parametros['tiempo_demora'];

    $retorno = Pedido::modificarPedido($pedidoAModificar);

    if ($retorno != null) {
      $payload = json_encode(array("mensaje" => $retorno));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  public function AsignarHorarioSalidaPedido($request, $response, $args)
  {

    $id = $args['id'];

    $retorno = Pedido::asignarHorarioDeSalidaPedido($id);
    $pedido = Pedido::obtenerPedido($id);

    if ($retorno != null) { 
      $payload = json_encode(array("mensaje" => $retorno . " Cuenta cobrada, la cual fue de un total de $" . $pedido->precio_total));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  public function BorrarUno($request, $response, $args)
  {
    //$parametros = $request->getParsedBody();

    $id = $args['id'];

    $retorno = Pedido::borrarPedido($id);

    if ($retorno != null) {
      $payload = json_encode(array("mensaje" => $retorno));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }



  public function AgregarProductoAPedido($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id_pedido = $parametros['id_pedido'];
    $id_producto = $parametros['id_producto'];
    $id_usuario = $parametros['id_usuario'];
    // $cantidad = $parametros['cantidad'];

    $pedido = new Pedido();
    $respuesta = $pedido->agregarProducto($id_pedido, $id_producto,  $id_usuario);

    if ($respuesta != null) {
      if (!is_numeric($respuesta)) {
        $payload = json_encode(array("error" => $respuesta));
      } else {
        $payload = json_encode(array("mensaje" => "Producto agregado al pedido con exito"));
      }
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }




  public function TraerProductosDePedido($request, $response, $args)
  {
    $id_pedido = $args['id'];

    $pedido = new Pedido();
    $productos_pedido = $pedido->obtenerProductosPedido($id_pedido);

    if ($productos_pedido != null) {
      $payload = json_encode(array("productos_pedido" => $productos_pedido));
    } else {
      $payload = json_encode(array("mensaje" => "No se encontraron productos para este pedido"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  public function SacarFotoDeMesa($request, $response, $args)
{
    $parametros = $request->getParsedBody();
    $archivos = $request->getUploadedFiles();

    // Verifica si el archivo 'foto' está presente en los archivos subidos
    if (isset($archivos['foto'])) {
        $foto = $archivos['foto'];
        $idPedido = $parametros['idPedido'];

        $pedido = new Pedido();
        $pedido = $pedido->obtenerPedido($idPedido);

        if ($pedido != null) {
            $carpetaDestino = "Mesas";

            // Verifica si la carpeta de destino no existe
            if (!file_exists($carpetaDestino)) {
                mkdir($carpetaDestino, 0777, true);
            }

            // Obtiene la extensión del archivo
            $extension = pathinfo($foto->getClientFilename(), PATHINFO_EXTENSION);

            // Crea un nombre de archivo único
            $nombreArchivo = "{$pedido->codigo}-{$pedido->id_mesa}.{$extension}";

            // Construye la ruta completa del destino
            $destino = "{$carpetaDestino}/{$nombreArchivo}";

            // Mueve el archivo a la carpeta de destino
            $foto->moveTo($destino);

            $payload = json_encode(array("mensaje" => "Foto tomada con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "No se pudo tomar la foto, no existe el id de pedido"));
        }
    } else {
        $payload = json_encode(array("mensaje" => "No se proporcionó el archivo 'foto'"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
}


public function ActualizarPrecioTotalAPedido($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id_pedido = $parametros['id_pedido'];
    // $cantidad = $parametros['cantidad'];

    $pedido = new Pedido();
    $respuesta = $pedido->agregarPrecioTotalPedido($id_pedido);

    if ($respuesta != null) {
      if (!is_numeric($respuesta)) {
        $payload = json_encode(array("error" => $respuesta));
      } else {
        $payload = json_encode(array("mensaje" => "Precio total del pedido actualizado con exito"));
      }
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  public function ActualizarTiempoEstimadoPedido($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $id_pedido = $parametros['id_pedido'];
    // $cantidad = $parametros['cantidad'];

    $pedido = new Pedido();
    $respuesta = $pedido->agregarTiempoDemoraPedido($id_pedido);

    if ($respuesta != null) {
      if (!is_numeric($respuesta)) {
        $payload = json_encode(array("error" => $respuesta));
      } else {
        $payload = json_encode(array("mensaje" => "Tiempo de demora..."));
      }
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }



  public function MostrarTiempoDemoraPedido($request, $response, $args)
  {
    $id_pedido = $args['id'];
    $codigo = $args['codigo'];
    $tiempoDemora = Pedido::tiempoDemoraYCodigoPedido($id_pedido);
    $payload = json_encode(array("mensaje" => "El Tiempo de demora del pedido de código " . $codigo . " es de " . $tiempoDemora . " minutos"));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  public function CambiarEstadoProducto($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $id_pedido = $parametros['id_pedido'];
    $id_usuario = $parametros['id_usuario'];
    $nuevo_estado = $parametros['nuevo_estado'];
    $retorno = Pedido::cambiarEstadoDeProducto($id_pedido, $id_usuario, $nuevo_estado);
    if($retorno != null)
    {

      $payload = json_encode(array("mensaje" => $retorno));
    }
    else
    {
      $payload = json_encode(array("error" => "Error"));
    }
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function ObtenerPendientesEmpleado($request, $response, $args)
{
   $id_pedido = $args['id'];
   $id_usuario = $args['id_usuario'];
   $lista = Pedido::obtenerProductosPendientesEmpleado($id_pedido, $id_usuario);
   $lista_json = json_encode($lista); // Convertir el array de productos en una cadena JSON
   $payload = json_encode(array("mensaje" => "Productos pendientes del usuario de id ". $id_usuario . " : <br>" .$lista_json));

   $response->getBody()->write($payload);
   return $response->withHeader('Content-Type', 'application/json');
}


public function ActualizarEstadoPedido($request, $response, $args)
  {
    echo "holaaa";
    $parametros = $request->getParsedBody();
    $id_pedido = $parametros['id_pedido'];
    $nuevo_estado = $parametros['nuevo_estado'];
    $retorno = Pedido::actualizarEstadoDelPedido($id_pedido, $nuevo_estado);
    if($retorno != null)
    {

      $payload = json_encode(array("mensaje" => $retorno));
    }
    else
    {
      $payload = json_encode(array("error" => "Error"));
    }
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }



  public function EncuestaCliente($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $codigo_pedido = $parametros['codigo_pedido'];
    $encuesta = $parametros['encuesta'];
    $valoracion = $parametros['valoracion'];
 
    $retorno = Pedido::agregarEncuestaAlPedido($codigo_pedido, $encuesta, $valoracion);

    if($retorno != null)
    {
      $payload = json_encode(array("mensaje" => $retorno));
    }
    else
    {
      $payload = json_encode(array("error" => "Error"));
    }
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  public function MejoresComentarios($request, $response, $args)
{
    try {
        $lista = Pedido::obtenerMejoresValoracionesPedidos();

        // Verificar si se obtuvieron resultados
        if (!empty($lista)) {
            $mensaje = "Mejores valoraciones de pedidos:<br>";

            foreach ($lista as $pedido) {
                $encuesta = $pedido['encuesta'];
                $codigo = $pedido['codigo'];
                $valoracion = $pedido['valoracion'];

                $mensaje .= "Encuesta: $encuesta, Codigo: $codigo, Valoracion: $valoracion estrellas<br>";
            }
        } else {
            $mensaje = "No se encontraron mejores valoraciones de pedidos.";
        }

        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $mensajeError = 'Error al obtener mejores valoraciones: ' . $e->getMessage();
        $payloadError = json_encode(array("error" => $mensajeError));

        $response->getBody()->write($payloadError);
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
}




public function PedidosMasDeDosHoras($request, $response, $args)
{
    $lista = Pedido::obtenerPedidosLargos();

    if (!empty($lista)) {
      $mensaje = "Pedidos con más de dos horas de transcurso:<br>";

      foreach ($lista as $pedido) {
          $codigo = $pedido['codigo'];
          $horario_llegada = $pedido['horario_llegada'];
          $horario_salida = $pedido['horario_salida'];

          $mensaje .= "codigo: $codigo, horario_llegada: $horario_llegada, horario_salida: $horario_salida<br>";
      }
  } else {
      $mensaje = "No se encontraron pedidos con más de dos horas de transcurso.";
  }

  $payload = json_encode(array("mensaje" => $mensaje));

  $response->getBody()->write($payload);
  return $response->withHeader('Content-Type', 'application/json');
}


public function Estadisticas30Dias($request, $response, $args)
{
    $resultado = Pedido::obtenerRecaudacionUltimos30Dias();

    if (!empty($resultado)) {
        $total_recaudado = $resultado['total_recaudado'];
        $total_pedidos = $resultado['total_pedidos'];

        $mensaje = "Estadísticas últimos 30 días: Cantidad de dinero recaudada: $total_recaudado con un total de $total_pedidos pedidos";
        $payload = json_encode(array("mensaje" => $mensaje));
    } else {
        $payload = json_encode(array("mensaje" => "No se encontraron resultados para la mesa más utilizada"));
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
}






}
