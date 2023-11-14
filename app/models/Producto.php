<?php
class Producto
{
    public $id;
    public $nombre;
    public $tipo;
    public $precio;
    public $descripcion;
    public $activo;

    public function crearProducto()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta
            ("INSERT INTO productos (nombre, tipo, precio, descripcion) 
            VALUES (:nombre, :tipo, :precio, :descripcion)");
    
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
            $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
    
            $consulta->execute();
    
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) 
        {
            return 'Error al crear el producto: ' . $e->getMessage();
        }
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, tipo, precio, descripcion, activo 
        FROM productos WHERE activo != 0");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProducto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, tipo, precio, descripcion,activo 
        FROM productos WHERE id = :id AND activo != 0");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public static function modificarProducto(Producto $productoIngresado)
    {
        $productoAModificar = self::obtenerProducto($productoIngresado->id);
        
        if ($productoAModificar != null) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE productos 
                SET nombre = :nombre, tipo = :tipo, precio = :precio, descripcion = :descripcion
                WHERE id = :id");
            $consulta->bindValue(':nombre', $productoIngresado->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $productoIngresado->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':precio', $productoIngresado->precio, PDO::PARAM_STR);
            $consulta->bindValue(':descripcion', $productoIngresado->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':id', $productoIngresado->id, PDO::PARAM_INT);

            if ($consulta->execute()) 
            {
                return "Producto modificado exitosamente";
            } 
            else 
            {
                return "Error al modificar el producto";
            }
        } 
        else 
        {
            return "No se encontró el producto a modificar";
        }
    }
    
    public static function borrarProducto($id)
    {
        $idProductoAEliminar = self::obtenerProducto($id);
        if($idProductoAEliminar != null)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET activo = 0 WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            if ($consulta->execute()) 
            {
                return "Producto eliminado exitosamente";
            } 
            else 
            {
                return "Error al eliminar el producto";
            }
        }
        else
        {
            return "No se encontró un producto con id '{$id}'";
        }
    }
}
