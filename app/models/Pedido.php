<?php
class Pedido
{
    public $id;
    public $precio_total;
    public $id_usuario;
    public $id_mesa;
    public $codigo;
    public $estado;
    public $horario_llegada;
    public $horario_salida;
    public $nombre_cliente;
    public $tiempo_demora;
    public $activo;

    public function crearPedido()
    {
        try {

            var_dump($this->id_usuario);
            var_dump($this->id_mesa);
            var_dump($this->codigo);
            var_dump($this->nombre_cliente);

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            // Primero, realiza la inserción en la tabla "pedidos"
            $consulta = $objAccesoDatos->prepararConsulta
            ("INSERT INTO pedidos (id_usuario, id_mesa, codigo, nombre_cliente) 
            VALUES (:id_usuario, :id_mesa, :codigo, :nombre_cliente)");

            // Luego, realiza una consulta de actualización en la tabla "mesas"
            $consultaDos = $objAccesoDatos->prepararConsulta
            ("UPDATE mesas SET libre = 0 WHERE id = :id_mesa");
    
           // $consulta->bindValue(':precio_total', $this->precio_total, PDO::PARAM_STR);
            $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);

            $consultaDos->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);


    
            $consulta->execute();
            $consultaDos->execute();
    
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) 
        {
            return 'Error al crear el pedido: ' . $e->getMessage();
        }
    }
    
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio_total, id_usuario, id_mesa, 
        codigo, estado, horario_llegada, horario_salida, nombre_cliente, tiempo_demora, activo 
        FROM pedidos WHERE activo != 0");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio_total, id_usuario, id_mesa, 
        codigo, estado, horario_llegada, horario_salida, nombre_cliente, tiempo_demora, activo
        FROM pedidos WHERE id = :id AND activo != 0");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function modificarPedido(Pedido $pedidoIngresado)
    {
        $pedidoAModificar = self::obtenerPedido($pedidoIngresado->id);
        
        if ($pedidoAModificar != null) 
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos 
                SET precio_total = :precio_total, id_usuario = :id_usuario, 
                /*id_mesa = :id_mesa,*/ /*codigo = :codigo,*/ estado = :estado, /*horario_llegada = :horario_llegada,*/ 
               /* horario_salida = :horario_salida,*/ nombre_cliente = :nombre_cliente, tiempo_demora = :tiempo_demora
                WHERE id = :id");
            $consulta->bindValue(':precio_total', $pedidoIngresado->precio_total, PDO::PARAM_STR);
            $consulta->bindValue(':id_usuario', $pedidoIngresado->id_usuario, PDO::PARAM_STR);
            //$consulta->bindValue(':id_mesa', $pedidoIngresado->id_mesa, PDO::PARAM_STR);
            //$consulta->bindValue(':codigo', $pedidoIngresado->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $pedidoIngresado->estado, PDO::PARAM_STR);
            //$consulta->bindValue(':horario_llegada', $pedidoIngresado->horario_llegada, PDO::PARAM_STR);
           // $consulta->bindValue(':horario_salida', $pedidoIngresado->horario_salida, PDO::PARAM_STR);
            $consulta->bindValue('nombre_cliente', $pedidoIngresado->nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_demora', $pedidoIngresado->tiempo_demora, PDO::PARAM_STR);


            $consulta->bindValue(':id', $pedidoIngresado->id, PDO::PARAM_INT);
            

            if ($consulta->execute()) 
            {
                return "Pedido modificado exitosamente";
            } 
            else 
            {
                return "Error al modificar el pedido";
            }
        } 
        else 
        {
            return "No se encontró el pedido a modificar";
        }
    }




    public static function asignarHorarioDeSalidaPedido($id)
{
    $pedidoAModificar = self::obtenerPedido($id);
    
    if ($pedidoAModificar != null) 
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos
            SET horario_salida = NOW(), estado = 'finalizado' WHERE id = :id");


            // Luego, realiza una consulta de actualización en la tabla "mesas"
            $consultaDos = $objAccesoDato->prepararConsulta
            ("UPDATE mesas SET libre = 1 WHERE id = :id_mesa");

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);


       
        $consultaDos->bindValue(':id_mesa', $pedidoAModificar->id_mesa, PDO::PARAM_STR);

        $consultaDos->execute();

        if ($consulta->execute()) 
        {
            return "Horario de salida actualizado exitosamente";
        } 
        else 
        {
            return "Error al actualizar horario de salida del pedido";
        }
    } 
    else 
    {
        return "No se encontró el pedido para colocar horario de salida";
    }
}


    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET activo = 0 /*horario_salida = :horario_salida*/ WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        //$consulta->bindValue(':horario_salida', date_format($fecha, 'Y-m-d H:i:s'));
        if ($consulta->execute()) 
        {
            return "Pedido eliminado exitosamente";
        } 
        else 
        {
            return "Error al eliminar el pedido";
        }
    }





    public function agregarProducto($id_pedido, $id_producto, $id_usuario)
{
    try {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta
        ("INSERT INTO pedido_producto (id_pedido, id_producto, id_usuario, estado) 
        VALUES (:id_pedido, :id_producto, :id_usuario, :estado)");

        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_STR);
        $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_STR);
        //$consulta->bindValue(':cantidad', $cantidad, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    } catch (Exception $e) 
    {
        return 'Error al agregar producto al pedido: ' . $e->getMessage();
    }
}



public function obtenerPrecioProducto($id_producto)
{
    try {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT precio FROM productos WHERE id = :id_producto");
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_OBJ)->precio;
    } catch (Exception $e)
    {
        return 'Error al obtener el precio del producto: ' . $e->getMessage();
    }
}



public function actualizarPrecioTotal($id_pedido, $precio_total)
{
    try {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET precio_total = :precio_total WHERE id = :id_pedido");
        $consulta->bindValue(':precio_total', $precio_total, PDO::PARAM_STR);
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    } catch (Exception $e)
    {
        return 'Error al actualizar el precio total del pedido: ' . $e->getMessage();
    }
}




public function obtenerProductosPedido($id_pedido)
{
    try {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto FROM pedido_producto WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS);
    } catch (Exception $e)
    {
        return 'Error al obtener los productos del pedido: ' . $e->getMessage();
    }
}



public function agregarPrecioTotalPedido($id_pedido)
{

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos
            SET precio_total = (
                SELECT SUM(productos.precio)
                FROM pedido_producto
                INNER JOIN productos ON pedido_producto.id_producto = productos.id
                WHERE pedido_producto.id_pedido = pedidos.id
            )
            WHERE id = :id");
           
           
            $consulta->bindValue(':id', $id_pedido, PDO::PARAM_INT);
            

            if ($consulta->execute()) 
            {
                return "precio agregado exitosamente";
            } 
            else 
            {
                return "error al agregar precio total al pedido";
            }

}

public function agregarTiempoDemoraPedido($id_pedido)
{

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos
            SET tiempo_demora = (
                SELECT MAX(productos.tiempoEstimado)
                FROM pedido_producto
                INNER JOIN productos ON pedido_producto.id_producto = productos.id
                WHERE pedido_producto.id_pedido = pedidos.id
            )
            WHERE id = :id");
           
           
            $consulta->bindValue(':id', $id_pedido, PDO::PARAM_INT);
            

            if ($consulta->execute()) 
            {
                return "Tiempo de demora actualizado con éxito";
            } 
            else 
            {
                return "error al actualizar tiempo de demora";
            }

}


public static function tiempoDemoraYCodigoPedido($id_pedido)
{
    try {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tiempo_demora, codigo FROM pedidos WHERE id = :id_pedido");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_OBJ)->tiempo_demora;
    } catch (Exception $e)
    {
        return 'Error al obtener el tiempo de demora del pedido: ' . $e->getMessage();
    }
}

public static function cambiarEstadoDeProducto($id_pedido, $id_usuario, $nuevo_estado)
{

            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE pedido_producto
            SET estado = :estado WHERE id_pedido = :id AND id_usuario = :id_usuario");
           
           
            $consulta->bindValue(':id', $id_pedido, PDO::PARAM_INT);
            $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $nuevo_estado, PDO::PARAM_STR);

            if ($consulta->execute()) 
            {
                return "estado cambiado a 'listo para servir' exitoso";
            } 
            else 
            {
                return "error al cambiar estado a 'listo para servir'";
            }

}





public function obtenerProductosPendientesEmpleado($id_pedido, $id_usuario)
{
    try {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT productos.nombre
        FROM pedido_producto
        JOIN productos ON pedido_producto.id_producto = productos.id
        WHERE pedido_producto.id_pedido = :id_pedido
          AND pedido_producto.id_usuario = :id_usuario AND pedido_producto.estado = :estado");
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':id_usuario', $id_usuario, PDO::PARAM_STR);
        $consulta->bindValue(':estado', "pendiente", PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e)
    {
        return 'Error al obtener los productos del pedido del usuario: ' . $e->getMessage();
    }
}




public function actualizarEstadoDelPedido($id_pedido, $nuevo_estado)
{

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id_pedido");
        $consulta->bindValue(':estado', $nuevo_estado, PDO::PARAM_STR);
        $consulta->bindValue(':id_pedido', $id_pedido, PDO::PARAM_STR);
        
        if ($consulta->execute()) 
        {
            return "estado del pedido actualizado a 'en preparacion' con éxito";
        } 
        else 
        {
            return "error al actualizar estado del pedido a 'en preparacion'";
        }


      
}


public function agregarEncuestaAlPedido($codigo_pedido, $encuesta, $valoracion)
{

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE pedidos SET encuesta = :encuesta, valoracion = :valoracion WHERE codigo = :codigo_pedido");
        $consulta->bindValue(':encuesta', $encuesta, PDO::PARAM_STR);
        $consulta->bindValue(':valoracion', $valoracion, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $codigo_pedido, PDO::PARAM_STR);

        
        if ($consulta->execute()) 
        {
            return "encuesta y valoracion agregada al pedido con exito";
        } 
        else 
        {
            return "error al agregar encuesta y valoracion al pedido";
        }


      
}


public function obtenerMejoresValoracionesPedidos()
{
    try {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT encuesta, codigo, valoracion
        FROM pedidos
        WHERE valoracion >= 4");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e)
    {
        return 'Error al obtener mejores valoraciones: ' . $e->getMessage();
    }
}


public static function obtenerPedidosLargos()
{
    $objAccesoDato = AccesoDatos::obtenerInstancia();
    
    $consulta = $objAccesoDato->prepararConsulta("SELECT codigo, horario_llegada, horario_salida
                                                FROM pedidos
                                                WHERE TIMESTAMPDIFF(HOUR, horario_llegada, horario_salida) > 2
                                                ORDER BY horario_llegada DESC;");
    
    $consulta->execute();
    
    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}
    
}
