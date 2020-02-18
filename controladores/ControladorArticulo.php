<?php
require_once MODEL_PATH."articulo.php";
require_once CONTROLLER_PATH."ControladorBD.php";
require_once UTILITY_PATH."funciones.php";

class ControladorArticulo {

    static private $instancia = null;
    private function __construct() {
        //echo "Conector creado";
    }
    
    /**
     * Patrón Singleton. Ontiene una instancia del Manejador de la BD
     * @return instancia de conexion
     */
    public static function getControlador() {
        if (self::$instancia == null) {
            self::$instancia = new ControladorArticulo();
        }
        return self::$instancia;
    }
    
    /**
     * Lista el alumnado según el nombre o distribuidor
     * @param type $nombre
     * 
     */
//----------------------------------------------------------------------------------------------------
    public function listarArticulos($nombre, $tipo){
        $lista=[];
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();

        $consulta = "SELECT * FROM productos WHERE nombre LIKE :nombre OR tipo LIKE :tipo";
        $parametros = array(':nombre' => "%".$nombre."%", ':tipo' => "%".$tipo."%");

        $res = $bd->consultarBD($consulta,$parametros);
        $filas=$res->fetchAll(PDO::FETCH_OBJ);

        if (count($filas) > 0) {
            foreach ($filas as $a) {
                $articulo = new Articulo($a->id, $a->nombre, $a->marca, $a->modelo, $a->tipo, $a->disponible, $a->precio,$a->imagen);
                $lista[] = $articulo;
            }
            $bd->cerrarBD();
            return $lista;
        }else{
            return null;
        }    
    }
//----------------------------------------------------------------------------------------------------
    public function almacenarArticulo($nombre, $marca, $modelo, $tipo, $disponible, $precio, $imagen){
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        //muy importante respetar el orden de las columnas en el insert y corresponderse a la tabla de verdad!!
        // el resto se puede poner de orden que nos de la gana
        $consulta = "INSERT INTO productos (nombre,  marca, modelo,tipo, disponible, precio, imagen) VALUES ( :nombre,:marca,:modelo, :tipo, :disponible, :precio, :imagen)";
        
        $parametros=array(':nombre'=>$nombre, ':marca'=>$marca, ':modelo'=>$modelo, ':tipo'=>$tipo,  ':disponible'=>$disponible, ':precio'=>$precio,
        ':imagen'=>$imagen);
        $estado = $bd->actualizarBD($consulta,$parametros);
        $bd->cerrarBD();
        return $estado;
    }
//----------------------------------------------------------------------------------------------------
    public function buscarArticuloid($id){ 
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "SELECT * FROM productos WHERE id = :id";
        $parametros = array(':id' => $id);
        
        $filas = $bd->consultarBD($consulta, $parametros);
        $res = $bd->consultarBD($consulta,$parametros);
        $filas=$res->fetchAll(PDO::FETCH_OBJ);
        
        if (count($filas) > 0) {
            foreach ($filas as $a) {
                $Articulo = new Articulo($a->id, $a->nombre, $a->marca, $a->modelo,$a->tipo, $a->disponible, $a->precio,  $a->imagen);
            }
            $bd->cerrarBD();
            return $Articulo;
        }else{
            return null;
        }    
    }
//--------------------------------------------------------------------------------------------------
    public function buscarArticulo($nombre){ 
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "SELECT * FROM productos WHERE nombre = :nombre";
        $parametros = array(':nombre' => $nombre);
        $filas = $bd->consultarBD($consulta, $parametros);
        $res = $bd->consultarBD($consulta,$parametros);
        $filas=$res->fetchAll(PDO::FETCH_OBJ);
        if (count($filas) > 0) {
            foreach ($filas as $a) {
                $Articulo = new Articulo($a->id, $a->nombre, $a->marca, $a->modelo, $a->tipo, $a->disponible, $a->precio, $a->imagen);
            }
            $bd->cerrarBD();
            return $Articulo;
        }else{
            return null;
        }    
    }
//------------------------------------------------------------------------------------------------- 
    public function borrarArticulo($id){ 
        $estado=false;
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "DELETE FROM productos WHERE id = :id";
        $parametros = array(':id' => $id);
        $estado = $bd->actualizarBD($consulta,$parametros);
        $bd->cerrarBD();
        return $estado;
    }
//-------------------------------------------------------------------------------------------------  
    public function actualizarArticulo($id,$nombre,$marca, $modelo, $tipo, $disponible, $precio,$imagen){
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "UPDATE productos SET  nombre=:nombre, marca=:marca, modelo=:modelo, tipo=:tipo, disponible=:disponible, precio=:precio,   
             imagen=:imagen 
            WHERE id=:id";
        $parametros= array(':id'=>$id ,':nombre'=>$nombre,  ':marca'=>$marca,':modelo'=>$modelo,':tipo'=>$tipo,':disponible'=>$disponible, ':precio'=>$precio,
       ':imagen'=>$imagen);
        $estado = $bd->actualizarBD($consulta,$parametros);
        $bd->cerrarBD();
        return $estado;
    }
//-------------------------------------------------------------------------------------------------  



}
