<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gestion_tienda/dirs.php";
require_once CONTROLLER_PATH . "ControladorArticulo.php";
require_once UTILITY_PATH . "funciones.php";

error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
session_start();
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
    exit();
}

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = decode($_GET["id"]);
    $controlador = ControladorArticulo::getControlador();
    $articulo = $controlador->buscarArticuloid($id);
    if (is_null($articulo)) {
        header("location: error.php");
        exit();
    }
}else{
    header("location: error.php");
}
?>

<?php require_once VIEW_PATH . "navbar.php"; ?>
<!DOCTYPE html>
<html lang="es">

<head>
<style type="text/css">
.banner-section{background-image:url("../imagenes/ver.jpg"); background-size:1500px 650px ; height: 380px; left: 0; position: absolute; top: 0; background-position:0; background-repeat: no-repeat; }
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
<section class="post-content-section">
    <div class="container">
        <div class="row" >
            <div class="col-lg-12 col-md-12 col-sm-12 post-title-block">
            <div id='cabecera'> <h1 class="display-1 text-center">Ver Articulo</h1></div>
            <div id="menu">    
            <ul class="list-inline text-center">
               
                    <li>Jose F |</li>
                    <li>CRUD Gestion Tienda  |</li>
                    <li>Ver</li>
                
                </ul>
                </div>
            </div>


    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
    
                <div class="lead">
                <div class="well">
<table>
    <tr>
    <td class="col-xs-11" class="align-left">
            <label><h2>Nombre</h2></label>
            <p class="form-control-static"><div class="lead"><?php echo $articulo->getnombre(); ?></p>
        
   </td>
    <td class="col-xs-11" class="align-right">
            <label><h3>Fotografía</h3></label><br>
            <img src='<?php echo "../imagenes/fotos/" . $articulo->getimagen() ?>' class='rounded' class='img-thumbnail' width='160px' height='auto'>
    </td>
</tr>
</table>
<div class="form-group">
<label><h2>Marca</h2></label>
<p class="form-control-static"><?php echo $articulo->getmarca(); ?></p>
  </div>
<label><h2>Modelo</h2></label>
<div class="form-group">
<p class="form-control-static"><?php echo $articulo->getmodelo(); ?></p>
</div>
<div class="form-group">
<label><h2>Tipo</h2></label>
<p class="form-control-static"><?php echo $articulo->gettipo(); ?></p>
</div>
<div class="form-group">
<label><h2>Disponible</h2></label>
<p class="form-control-static"><?php echo $articulo->getdisponible(); ?></p>
</div>
<div class="form-group">
<label><h2>Precio</h2></label>
<p class="form-control-static"><?php echo $articulo->getprecio(); ?>€</p>
</div>
  <span class="list-group-item text-center">
     <a href='../index.php' class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span> Volver</a>
     </span>  
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
<?php require_once VIEW_PATH . "footer.php" ?>
