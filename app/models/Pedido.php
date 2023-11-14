<?php
class Pedido
{
    public $id;
    public $precio_total;
    public $id_producto;
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
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta
            ("INSERT INTO pedidos (precio_total, id_producto, id_usuario, id_mesa, codigo, 
            nombre_cliente, tiempo_demora) 
            VALUES (:precio_total, :id_producto, :id_usuario, :id_mesa, :codigo, 
            :nombre_cliente, :tiempo_demora)");
    
            $consulta->bindValue(':precio_total', $this->precio_total, PDO::PARAM_STR);
            $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_STR);
            $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            //$consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            // $consulta->bindValue(':horario_llegada', $this->horario_llegada, PDO::PARAM_STR);
            // $consulta->bindValue(':horario_salida', $this->horario_salida, PDO::PARAM_STR);
            $consulta->bindValue('nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':tiempo_demora', $this->tiempo_demora, PDO::PARAM_STR);

    
            $consulta->execute();
    
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) 
        {
            return 'Error al crear el pedido: ' . $e->getMessage();
        }
    }
    
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio_total, id_producto, id_usuario, id_mesa, 
        codigo, estado, horario_llegada, horario_salida, nombre_cliente, tiempo_demora, activo 
        FROM pedidos WHERE activo != 0");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, precio_total, id_producto, id_usuario, id_mesa, 
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
                SET precio_total = :precio_total, id_producto = :id_producto, id_usuario = :id_usuario, 
                id_mesa = :id_mesa, codigo = :codigo, estado = :estado, horario_llegada = :horario_llegada, 
                horario_salida = :horario_salida, nombre_cliente = :nombre_cliente, tiempo_demora = :tiempo_demora
                WHERE id = :id");
            $consulta->bindValue(':precio_total', $pedidoIngresado->precio_total, PDO::PARAM_STR);
            $consulta->bindValue(':id_producto', $pedidoIngresado->id_producto, PDO::PARAM_STR);
            $consulta->bindValue(':id_usuario', $pedidoIngresado->id_usuario, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $pedidoIngresado->id_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $pedidoIngresado->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $pedidoIngresado->estado, PDO::PARAM_STR);
            $consulta->bindValue(':horario_llegada', $pedidoIngresado->horario_llegada, PDO::PARAM_STR);
            $consulta->bindValue(':horario_salida', $pedidoIngresado->horario_salida, PDO::PARAM_STR);
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

    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET activo = 0, horario_salida = :horario_salida WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':horario_salida', date_format($fecha, 'Y-m-d H:i:s'));
        if ($consulta->execute()) 
        {
            return "Pedido eliminado exitosamente";
        } 
        else 
        {
            return "Error al eliminar el pedido";
        }
    }
    
}
