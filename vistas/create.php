<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/gestion_tienda/dirs.php";
require_once CONTROLLER_PATH . "ControladorArticulo.php";
require_once CONTROLLER_PATH . "ControladorImagen.php";
require_once UTILITY_PATH . "funciones.php";

error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
session_start();
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
    exit();
}elseif($_SESSION['tipo'] != 'admin'){
    header("location: error.php");
    exit();
}

// Variables a procesar
$nombre = $marca = $modelo = $tipo = $disponible = $precio = $imagen = "";
$Valmarca = $Valmodelo = $Valtipo = $Valdisponible = $Valprecio = $Valimagen = "";
$Valmarca = $Valmodelo = $Valtipo = $Valdisponible = $Valprecio = $Valimagen = "";
$errores = [];



if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["aceptar"]) {

    // NOMBRE
    $Valnombre = filtrado(($_POST["nombre"]));
    if (empty($Valnombre)) {
        $Errnombre = "Por favor introduzca un nombre válido con solo carácteres alfabéticos.";
        $errores[] = $Errnombre ;
    } elseif (!preg_match("/^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){2,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){3,36}$/iu", $Valnombre)) {
        $Errnombre = "Por favor introduzca un nombre válido con solo carácteres alfabéticos validos.";
        $errores[] = $Errnombre ;
    } else {
        $nombre = $Valnombre;
    }
    
    $controlador = ControladorArticulo::getControlador();
    $articulo = $controlador->buscarArticulo($nombre);
    if (isset($articulo)) {
        $Errnombre = "Ya existe un Articulo con este nombre en la Base de Datos";
        $errores[]= $Errnombre ;
    } else {
        $nombre = $Valnombre;
    }

    // Procesamos precio
    $Valprecio = filtrado($_POST["precio"]);
    if (empty($Valprecio)) {
        $Errprecio = "Debe elegir al menos una raza";
        $errores[]= $Errprecio ;
    }elseif(!(preg_match('/(^[1-9]?[0-9]?[0-9]*[0-9]+$)/', $Valprecio))){
        $Errprecio = "Por introduzca un precio del 1 al 999";
        $errores[]= $Errprecio;
    } else {
        $precio = $Valprecio;
    }

        //DISPONIBLE
        $Valdisponible = $_POST["disponible"];
        if (empty($Valdisponible)) {
            $Errdisponible = "Debe elegir al menos una opcion";
            $errores[]= $Errdisponible ;
        } else {
            $disponible = $Valdisponible;
        }

        //TIPO
        $Valtipo = $_POST["tipo"];
        if (empty($Valtipo)) {
                $Errtipo = "Debe elegir al menos un tipo";
                $errores[] = $Errtipo;
        } else {
            $tipo = implode(", ",$_POST["tipo"]);
            $tipo = $tipo;
        }

        //MODELO
        $Valmodelo = filtrado($_POST["modelo"]);
        if (empty($Valmodelo)) {
            $Errmodelo = "Debe elegir al menos un modelo";
            $errores[]= $Errmodelo ;
        } else {
            $modelo = $Valmodelo;
        }
        //MARCA
        $Valmarca = filtrado($_POST["marca"]);
        if (empty($Valmarca)) {
            $Errmarca = "Debe elegir al menos una marca";
            $errores[]= $Errmarca ;
        } else {
            $marca = $Valmarca;
        }

    // Procesamos la foto
    $propiedades = explode("/", $_FILES['imagen']['type']);
    $extension = $propiedades[1];
    $tam_max = 1000000; // 1 Mb
    $tam = $_FILES['imagen']['size'];
    $mod = true; // para modificar

    // Si no coicide la extensión
    if ($extension != "jpg" && $extension != "jpeg") {
        $mod = false;
        $imagenErr = "Formato debe ser jpg/jpeg";
    }
    // si no tiene el tamaño
    if ($tam > $tam_max) {
        $mod = false;
        $Errimagen = "Tamaño superior al limite de 1MB";
        $errores[]=  $Errimagen;
    }

    if ($mod) {
        //guardar imagen
        $imagen = md5($_FILES['imagen']['tmp_name'] . $_FILES['imagen']['name'] . time()) . "." . $extension;
        $controlador = ControladorImagen::getControlador();
        if (!$controlador->salvarImagen($imagen)) {
            $Errimagen = "Error al procesar la imagen y subirla al servidor";
            $errores[] =  $Errimagen;
        }
    }

    if (empty($errores)) {
        $controlador = ControladorArticulo::getControlador();
        $estado = $controlador->almacenarArticulo($nombre , $marca , $modelo , $tipo , $disponible , $precio , $imagen );
        if ($estado) {
        alerta('Producto creado correctamente','../index.php');
        } else {
            alerta('Producto no creado correctamente, revise los errores');
        }
    } else {
        alerta("Hay errores al procesar el formulario revise los errores");
    }

}
?>

<?php require_once VIEW_PATH . "navbar.php"; ?>
<head>
<style type="text/css">

.banner-section{background-image:url("../imagenes/crear.jpg"); background-size:1500px 500px ; height: 380px; left: 0; position: absolute; top: 0; background-position:0; background-repeat: no-repeat; }
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
            <div id='cabecera'> <h1 class="display-1 text-center">Nuevo Articulo</h1> </div>
            <div id="menu">    
            <ul class="list-inline text-center">
               
                    <li>Jose F |</li>
                    <li>CRUD Gestion Tienda |</li>
                    <li>Crear</li>
                
                </ul>
                </div>
            </div>
  </div>
<div class="list-group">
    <a class="list-group-item active"> 
    <h2 class="list-group-item-heading">Formulario </h2>
    <p class="list-group-item-text">Todos los campos son requeridos para completarse la creacion.</p>
    </a>
</div>
<div class="well">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-content">     

<div class="lead">
<!-- Formulario-->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <!-- Nombre-->
    <div class="form-group <?php echo (!empty($Errnombre)) ? 'error: ' : ''; ?>">
    
        <label>Nombre</label>
        <input type="text" required name="nombre" pattern="([^\s][A-zÀ-ž\s]+)" maxlength="25" title="El nombre no puede contener números" value="<?php echo $Valnombre; ?>">
        <span class="help-block"><?php echo $Errnombre;?></span> 
    </div>
        <!-- modelo-->
        <div class="form-group <?php echo (!empty($Errmodelo)) ? 'error: ' : ''; ?>">
        <label>Modelo</label>
        <input type="text" required name="modelo" maxlength="25" title="El modelo no puede contener números" value="<?php echo $Valmodelo; ?>">
        <span class="help-block"><?php echo $Errmodelo;?></span> 
</div>
    <!-- marca -->
    <div class="form-group  <?php echo (!empty($Errmarca)) ? 'error: ' : ''; ?>">
        <label>Marca</label>
        <input type="radio" name="marca" required value="Samsung" <?php echo (strstr($Valmarca, 'Samsung')) ? 'checked' : ''; ?>>Samsung</input>
        <input type="radio" name="marca" required value="Apple" <?php echo (strstr($Valmarca, 'Apple')) ? 'checked' : ''; ?>>Apple</input>
        <input type="radio" name="marca" required value="HP" <?php echo (strstr($Valmarca, 'HP')) ? 'checked' : ''; ?>>HP</input>
        <input type="radio" name="marca" required value="Microsoft" <?php echo (strstr($Valmarca, 'Microsoft')) ? 'checked' : ''; ?>>Microsoft</input>
        <input type="radio" name="marca" required value="Xiaomi" <?php echo (strstr($Valmarca, 'Xiaomi')) ? 'checked' : ''; ?>>Xiaomi</input>
        <input type="radio" name="marca" required value="Cisco" <?php echo (strstr($Valmarca, 'Cisco')) ? 'checked' : ''; ?>>Cisco</input>
        <span class="help-block"><?php echo $Errmarca; ?></span> 
    </div>
    <!-- Precio -->
    <div class="form-group <?php echo (!empty($Errprecio)) ? 'error: ' : ''; ?>">
        <label>Precio</label>
        <input type="text" required name="precio" pattern="([1-9]?[0-9]?[0-9]?[0-9])" minlength="1" maxlength="4" title="Inserte un numero  del 1 al 999" value="<?php echo $Valprecio; ?>">
        <span class="help-block"><?php  echo $Errprecio; ?></span> 
    </div>
    <!-- Disponible-->
    <div class="form-group <?php echo (!empty($Errdisponible)) ? 'error: ' : ''; ?>">
        <label>Disponible</label>
        <select name="disponible">
            <option value="SI" required <?php echo (strstr($Valdisponible, 'SI')) ? 'selected' : ''; ?>>SI</option>
            <option value="NO" required <?php echo (strstr($Valdisponible, 'NO')) ? 'selected' : ''; ?>>NO</option>
        </select>
        <span class="help-block"><?php  echo $Errdisponible; ?></span> 
    </div>
    <!-- Tipo -->
    <div class="form-group <?php echo (!empty($Errtipo)) ? 'error: ' : ''; ?>"s>
        <label>Tipo</label>
        <input type="checkbox"  name="tipo[]" value="Hardware" <?php echo (strstr($tipo, 'Hardware')) ? 'checked' : ''; ?>>Hardware</input>
        <input type="checkbox"  name="tipo[]" value="Programa util" <?php echo (strstr($tipo, 'Programa util ')) ? 'checked' : ''; ?>>Programa Util</input>
        <input type="checkbox"  name="tipo[]" value="Productividad" <?php echo (strstr($tipo, 'Productividad ')) ? 'checked' : ''; ?>>Productividad </input>
        <input type="checkbox" checked name="tipo[]" value="Multiples Funcionalidades" <?php echo (strstr($tipo, 'Multiples Funcionalidades')) ? 'checked' : ''; ?>>Multiples Funcionalidades</input>
        <span class="help-block"><?php echo $Errtipo; ?></span> 
    </div>
    <!-- Foto-->
    <div class="form-group <?php echo (!empty($imagenErr)) ? 'error: ' : ''; ?>">
        <label>Fotografía</label>
        <input type="file" required name="imagen" id="imagen" accept="image/jpeg">
        <span class="help-block"><?php echo $imagenErr; ?></span> 
    </div>
        </div> 
            </div>
                </div>
                    </div>
                        </div> 
                            </div>
                                </div>                             
    <span class="list-group-item text-center">
     <a onclick="history.back()" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span> Volver</a>
     <button type="reset" value="reset" class="btn btn-info"> <span class="glyphicon glyphicon-repeat"></span>  Limpiar</button> 
     <button type="submit" name= "aceptar" value="aceptar" class=" btn btn-success" ><span class="glyphicon glyphicon-ok"></span><strong> Crear</h5></strong>  </button>


     </span>
    </div>
</form>
<br>
<br>
<br>
</section>

<?php require_once VIEW_PATH . "footer.php" ?>

