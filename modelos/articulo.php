<?php
class Articulo {
    private $id;
    private $nombre;
    private $marca;
    private $modelo;
    private $tipo;
    private $disponible;
    private $precio;
    private $imagen;

    
    // Constructor
    public function __construct($id, $nombre, $marca, $modelo, $tipo, $disponible, $precio, $imagen) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->tipo = $tipo;
        $this->disponible = $disponible;
        $this->precio = $precio;
        $this->imagen = $imagen;
    }
    
    // **** GETS 
    function getid() {
        return $this->id;
    }

    function getnombre() {
        return $this->nombre;
    }

    
    function getmarca() {
        return $this->marca;
    }

    function getmodelo() {
        return $this->modelo;
    }

    function gettipo() {
        return $this->tipo;
    }

    function getdisponible() {
        return $this->disponible;
    }

    function getprecio() {
        return $this->precio;
    }

    function getimagen() {
        return $this->imagen;
    }


    //SETS
    function setid($id) {
        $this->id = $id;
    }

    function setnombre($nombre) {
        $this->nombre = $nombre;
    }

    function setmarca($marca) {
        $this->marca = $marca;
    }
    
    function setmodelo($modelo) {
        $this->modelo = $modelo;
    } 

    function settipo($tipo) {
        $this->tipo = $tipo;
    } 

    function setdisponible($disponible) {
        $this->disponible = $disponible;
    } 

    function setprecio($precio) {
        $this->precio = $precio;
    }
    

    function setimagen($imagen) {
        $this->imagen = $imagen;
    } 
}
?>