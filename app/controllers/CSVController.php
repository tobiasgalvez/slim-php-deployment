<?php

require '../vendor/autoload.php';
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require_once './models/CSV.php';

class CSVController
{
//    private $CSV;

//    public function __construct()
//    {
//        $this->CSV = new CSV();
//    }

   public function cargarCSV(Request $request, Response $response, array $args)
   {
       $archivo = $request->getUploadedFiles()['archivo'];
       $rutaArchivo = $archivo->getStream()->getMetadata('uri');
       if($rutaArchivo != null)
       {
           CSV::cargarCSV($rutaArchivo);
           echo "CSV cargado con exito";
       }
       else
       {
            echo "Error al cargar CSV";
       }

  

     return $response->withStatus(200);
   }

   public function descargarCSV(Request $request, Response $response, array $args)
   {
      try {
          $archivo = 'C:\\Users\\Tobias Galvez\\Postman\\files\\users.csv';
          if (!file_exists($archivo)) {
              throw new Exception('El archivo ' . $archivo . ' no existe');
          }
          $usuarios = Usuario::obtenerTodosIncluidosInactivos();
          CSV::descargarCSV($archivo, $usuarios);
          return $response->withHeader('Content-Type', 'application/octet-stream')
              ->withHeader('Content-Disposition', 'attachment; filename=' . basename($archivo))
              ->withHeader('Pragma', 'no-cache')
              ->withBody((new \Slim\Psr7\Stream(fopen($archivo, 'rb'))));
      } catch (Exception $e) {
          $response->getBody()->write($e->getMessage());
          return $response;
      }
   }
}