<?php
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
session_start();
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: /gestion_tienda/vistas/login.php");
    exit();
}
?>


<style type="text/css">
.banner-section{background-image:url("imagenes/menu.jpg"); background-size:1500px 500px ; height: 380px; left: 0; position: absolute; top: 0; background-position:0; background-repeat: no-repeat; }
#cabecera {
    font-weight: bold;
  font-size: 68px;
  font-family: "Arial";
  text-shadow: 0 1px 0 #ccc, 
               0 2px 0 #c9c9c9,
               0 3px 0 #bbb,
               0 4px 0 #b9b9b9,
               0 5px 0 #aaa,
               0 6px 1px rgba(0,0,0,.1),
               0 0 5px rgba(0,0,0,.1),
               0 1px 3px rgba(0,0,0,.3),
               0 3px 5px rgba(0,0,0,.2),
               0 5px 10px rgba(0,0,0,.25),
               0 10px 10px rgba(0,0,0,.2),
               0 20px 20px rgba(0,0,0,.15);
  color: #FFF;
  text-align: center;}
  #menu {
  font-weight: bold;
  font-size: 20px;
  font-family: "Arial";
  text-shadow: 0 0.5px 0 #ccc, 
               0 1px 0 #c9c9c9,
               0 1.5px 0 #bbb,
               0 2px 0 #b9b9b9,
               0 2.5px 0 #aaa,
               0 3px 0.5px rgba(0,0,0,.1),
               0 0 2.4px rgba(0,0,0,.1),
               0 0.5px 1.5px rgba(0,0,0,.3),
               0 1.5px 2.5px rgba(0,0,0,.2),
               0 2.5px 5px rgba(0,0,0,.25),
               0 5px 5px rgba(0,0,0,.2),
               0 10px 10px rgba(0,0,0,.1);
  color: #FFF;}

</style>
</head>
<body>
    

<section class="post-content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 post-title-block">
            <div id='cabecera'> <h1 class="display-1 text-center">Lista de Productos</h1></div>
            <div id="menu">    
            <ul class="list-inline text-center">
               
                    <li>Jose F |</li>
                    <li>CRUD Gestion Tienda |</li>
                    <li>Lista</li>
                
                </ul>
                </div>
            </div>
    <!-- Botones-->
  <div class="btn-group btn-group-justified">
  <a href="javascript:window.print()"  class="btn btn-primary"> Imprimir</a>
  <a href="utilidades/descargar.php?opcion=TXT" class="btn btn-primary">TXT</a>
  <a href="utilidades/descargar.php?opcion=PDF" target="_blank" class="btn btn-primary">PDF</a>
  <a href="utilidades/descargar.php?opcion=XML" target="_blank"class="btn btn-primary">XML</a>
  <a href="utilidades/descargar.php?opcion=JSON" target="_blank"class="btn btn-primary">JSON</a>
  <?php if($_SESSION['tipo'] =='admin'){ ?>
  <a href="vistas/create.php" class="btn btn-success"> Añadir Producto</a>
  <?php }?>
</div>    
    <br>
    <br>
    <br>
<?php
require_once CONTROLLER_PATH . "ControladorArticulo.php";
require_once CONTROLLER_PATH . "Paginador.php";
require_once UTILITY_PATH . "funciones.php";

if (!isset($_POST["articulo"])) {
    $nombre = "";
    $raza = "";
} else {
    $nombre = filtrado($_POST["articulo"]);
    $raza = filtrado($_POST["articulo"]);
}

$controlador = ControladorArticulo::getControlador();

//Paginador
$pagina = (isset($_GET['page'])) ? $_GET['page'] : 1;
$enlaces = (isset($_GET['enlaces'])) ? $_GET['enlaces'] : 10;

// Consulta 
$consulta = "SELECT * FROM productos WHERE nombre LIKE :nombre order by nombre ";
$parametros = array(':nombre' => "%" . $nombre . "%", ':nombre' => "%" . $nombre . "%");
$limite = 10; // Limite del paginador
$paginador  = new Paginador($consulta, $parametros, $limite);
$resultados = $paginador->getDatos($pagina);


if (count($resultados->datos)> 0) {
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Nombre</th>";
    echo "<th>Marca</th>";
    echo "<th>Modelo</th>";
    echo "<th>Tipo</th>";
    echo "<th>Disponible</th>";
    echo "<th>Precio</th>";
    echo "<th>Imagen</th>";
    echo "<th>Accion</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
  
    foreach ($resultados->datos as $a) {
     
        $articulo = new Articulo ($a->id, $a->nombre,$a->marca, $a->modelo, $a->tipo, $a->disponible, $a->precio,   $a->imagen);
        
        echo "<tr>";
            echo "<td>" . $articulo->getnombre() . "</td>";
            echo "<td>" . $articulo->getmarca() . "</td>";
            echo "<td>" . $articulo->getmodelo() . "</td>";
            echo "<td>" . $articulo->gettipo() . "</td>";
            echo "<td>" . $articulo->getdisponible() . "</td>";
            echo "<td>" . $articulo->getprecio() . "€</td>";
            echo "<td><img src='imagenes/fotos/" . $articulo->getimagen() . "' width='48px' height='48px'></td>";
            echo "<td>";
                echo "<a href='vistas/read.php?id=" . encode($articulo->getid()) . "' title='Ver ' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                if($_SESSION['tipo'] =='admin'){
                    echo "<a href='vistas/update.php?id=" . encode($articulo->getid()) . "' title='Actualizar ' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                    echo "<a href='vistas/delete.php?id=" . encode($articulo->getid()) . "' title='Borrar ' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                }
            echo "</td>";
        echo "</tr>";
        
    }
    
   
    echo "</tbody>";
    echo "</table>";
    echo "<ul class='pager' id='no_imprimir'>"; 
    echo $paginador->crearLinks($enlaces);
    echo "</ul>";
} else {
    echo "<p><em><h2>No se ha encontrado datos de articulo.</h2></em></p>";
}

?>

<?php



?>

<div id="no_imprimir">
    <?php if(isset($_SESSION['email'])){
        if (isset($_COOKIE['CONTADOR'])) {
            echo $contador;
            echo $acceso;
        } else {
            echo "Es tu primera visita hoy";
        }
    }
    ?>
</div>
</div>
</div>

</body>
</html>

<?php require_once VIEW_PATH . "footer.php" ?>