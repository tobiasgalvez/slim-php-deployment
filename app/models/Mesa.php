<?php
class Mesa
{
    public $id;
    public $activa;
    public $libre;

    public function crearMesa()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta
            ("INSERT INTO mesas () VALUES ()");
    
            $consulta->execute();
    
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) {
            return 'Error al crear la mesa: ' . $e->getMessage();
        }
    }

    public static function obtenerTodas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, activa, libre FROM mesas WHERE activa != 0");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, activa, libre FROM mesas WHERE id = :id AND activa != 0");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function ocuparMesa($id)
    {
        $mesaAModificar = self::obtenerMesa($id);
        
        if ($mesaAModificar != null) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas 
                SET libre = 0
                WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    
            if ($consulta->execute()) 
            {
                return "Mesa marcada como ocupada exitosamente";
            } 
            else 
            {
                return "Error al marcar la mesa como ocupada";
            }
        } 
        else 
        {
            return "No se encontró la mesa a modificar";
        }
    }

    public static function desocuparMesa($id)
    {
        $mesaAModificar = self::obtenerMesa($id);
        
        if ($mesaAModificar != null) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas 
                SET libre = 1
                WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    
            if ($consulta->execute()) 
            {
                return "Mesa marcada como libre exitosamente";
            } 
            else 
            {
                return "Error al marcar la mesa como libre";
            }
        } 
        else 
        {
            return "No se encontró la mesa a liberar";
        }
    }





    public static function borrarMesa($id)
    {
        $mesaABorrar = self::obtenerMesa($id);
        
        if ($mesaABorrar != null && $mesaABorrar->libre == 1) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET activa = 0 WHERE id = :id");
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            if ($consulta->execute()) 
            {
                return "Mesa eliminada exitosamente";
            } 
            else 
            {
                return "Error al eliminar la mesa";
            }
        } 
        else 
        {
            return "No se encontró la mesa a eliminar o la mesa está ocupada";
        }
    }


    public static function obtenerMesaMasUsada()
    {
        
        
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("SELECT mesas.id AS id_mesa, COUNT(pedidos.id) AS total_pedidos
            FROM mesas
            LEFT JOIN pedidos ON mesas.id = pedidos.id_mesa
            GROUP BY mesas.id
            ORDER BY total_pedidos DESC
            LIMIT 1;");
           
    
            $consulta->execute();
            
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
       
    }




}