<?php
class Mesa
{
    public $id;
    public $activo;
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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM mesas WHERE activo != 0");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id FROM mesas WHERE id = :id AND activo != 0");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function modificarMesa($id)
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





    public static function borrarMesa($id)
    {
        $mesaABorrar = self::obtenerMesa($id);
        
        if ($mesaABorrar != null && $mesaABorrar->libre == 1) {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET activo = 0 WHERE id = :id");
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
}