<?php

class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $tipo;
    public $usuario;
    public $clave;
    public $email;
    public $fechaBaja;
    public $fechaAlta;
    public $activo;


    public function crearUsuario()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta
            ("INSERT INTO usuarios (nombre, apellido, tipo, usuario, clave, email, fechaAlta) 
            VALUES (:nombre, :apellido, :tipo, :usuario, :clave, :email, :fechaAlta)");
            date_default_timezone_set('America/Argentina/Buenos_Aires');
    
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':fechaAlta', date('Y-m-d H:i:s'), PDO::PARAM_STR); // date representa fecha y hora actual
    
            $consulta->execute();
    
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) 
        {
            return 'Error al crear el usuario: ' . $e->getMessage();
        }
    }


    public function cargarUsuario()
    {
        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta
            ("INSERT INTO usuarios (nombre, apellido, tipo, usuario, clave, email, fechaBaja, fechaAlta, activo) 
            VALUES (:nombre, :apellido, :tipo, :usuario, :clave, :email, :fechaBaja, :fechaAlta, :activo)");
    
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':fechaBaja', $this->fechaBaja, PDO::PARAM_STR);
            $consulta->bindValue(':fechaAlta', $this->fechaAlta, PDO::PARAM_STR);
            $consulta->bindValue(':activo', $this->activo, PDO::PARAM_STR);
    
            $consulta->execute();
    
            return $objAccesoDatos->obtenerUltimoId();
        } catch (Exception $e) 
        {
            return 'Error al crear el usuario: ' . $e->getMessage();
        }
    }
    

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, tipo, usuario, clave, email, activo, fechaAlta, fechaBaja
        FROM usuarios WHERE activo != 0");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerTodosIncluidosInactivos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, tipo, usuario, clave, email, activo, fechaAlta, fechaBaja
        FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, tipo, usuario, clave, email, activo, fechaAlta, fechaBaja
        FROM usuarios WHERE id = :id AND activo != 0");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }



    public static function obtenerUsuarioPorNombreUsuario($nombre_usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, tipo, usuario, clave, email, activo
        FROM usuarios WHERE usuario = :nombre_usuario AND activo != 0");
        $consulta->bindValue(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }



    public static function obtenerRolDeUsuario($nombre_usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT tipo
        FROM usuarios WHERE usuario = :nombre_usuario AND activo != 0");
        $consulta->bindValue(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }



    
    public static function modificarUsuario(Usuario $usuarioIngresado)
    {
        $usuarioAModificar = self::obtenerUsuario($usuarioIngresado->id);
        
        if ($usuarioAModificar != null) 
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios 
                SET nombre = :nombre, apellido = :apellido, tipo = :tipo, usuario = :usuario, clave = :clave, email = :email
                WHERE id = :id");
            $consulta->bindValue(':nombre', $usuarioIngresado->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $usuarioIngresado->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $usuarioIngresado->tipo, PDO::PARAM_STR);
            $consulta->bindValue(':usuario', $usuarioIngresado->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $usuarioIngresado->clave, PDO::PARAM_STR);
            $consulta->bindValue(':email', $usuarioIngresado->email, PDO::PARAM_STR);
            $consulta->bindValue(':id', $usuarioIngresado->id, PDO::PARAM_INT);
            

            if ($consulta->execute()) 
            {
                return "Usuario modificado exitosamente";
            } 
            else 
            {
                return "Error al modificar el usuario";
            }
        } 
        else 
        {
            return "No se encontró el usuario a modificar";
        }
    }
    
    public static function borrarUsuario($id)
    {
        $idUsuarioAEliminar = self::obtenerUsuario($id);
        if($idUsuarioAEliminar != null)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET activo = 0, fechaBaja = NOW() WHERE id = :id");
            $fecha = new DateTime(date("d-m-Y"));
            $consulta->bindValue(':id', $id, PDO::PARAM_INT);
            //$consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
            if ($consulta->execute()) 
            {
                return "Usuario eliminado exitosamente";
            } 
            else 
            {
                return "Error al eliminar el usuario";
            }
        }
        else
        {
            return "No se encontró el id del usuario para eliminar";
        }
    }


}