<?php

use League\Csv\Reader;
use League\Csv\Writer;

class CSV
{
    public static function cargarCSV($archivo)
    {
     try {
       // Reiniciar la tabla de usuarios
       self::reiniciarTabla();
    
       $reader = Reader::createFromPath($archivo, 'r');
       $reader->setHeaderOffset(0);
       $records = $reader->getRecords();
    
       foreach ($records as $record) {
         $user = new Usuario();
         $user->id = $record['id'];
         $user->nombre = $record['nombre'];
         $user->apellido = $record['apellido'];
         $user->tipo = $record['tipo'];
         $user->usuario = $record['usuario'];
         $user->clave = $record['clave'];
         $user->email = $record['email'];
         $user->fechaBaja = $record['fechaBaja'];
         $user->fechaAlta = $record['fechaAlta']; //
         $user->activo = $record['activo'];

         $user->crearUsuario();
         


       }
     } catch (Exception $e) {
       // Manejar el error
       echo 'Error al cargar el CSV: ' . $e->getMessage();
     }
    }

   public static function descargarCSV($archivo, $usuarios)
   {
       $writer = Writer::createFromPath($archivo, 'w');
       $writer->insertAll([
           ['id', 'nombre', 'apellido', 'tipo', 'usuario', 'clave', 'email', 'fechaBaja', 'fechaAlta', 'activo'],
           // Aquí puedes insertar los usuarios desde la base de datos
       ]);

    foreach ($usuarios as $usuario) {
        $writer->insertOne([
            $usuario->id,
            $usuario->nombre,
            $usuario->apellido,
            $usuario->tipo,
            $usuario->usuario,
            $usuario->clave,
            $usuario->email,
            $usuario->fechaBaja,
            $usuario->fechaAlta,
            $usuario->activo
        ]);
    }

   }



   private static function reiniciarTabla()
{
   $objAccesoDatos = AccesoDatos::obtenerInstancia();
   $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM usuarios");
   $consulta->execute();

   $consulta = $objAccesoDatos->prepararConsulta("ALTER TABLE usuarios AUTO_INCREMENT = 1");
   $consulta->execute();
}


}