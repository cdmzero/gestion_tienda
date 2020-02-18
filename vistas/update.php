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
$imagenAnterior ="";
$Infoimagen="";



if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];

   // NOMBRE
   $Valnombre = filtrado(($_POST["nombre"]));
   if (empty($Valnombre)) {
       $Errnombre = "Por favor introduzca un nombre válido con solo carácteres alfabéticos.";
       $errores[] = $Errnombre ;
   } elseif (!preg_match("/^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){2,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){3,36}$/iu", $Valnombre)) {
       $Errnombre = "Por favor introduzca un nombre válido con solo carácteres alfabéticos validos.";
       $errores[] = $Errnombre ;
    }

   $nombreAnterior = $_POST['nombreAnterior'];
   
   $controlador = ControladorArticulo::getControlador();
   $articulo = $controlador->buscarArticulo($Valnombre);
   if (isset($articulo) && $nombreAnterior != $Valnombre){
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
   }elseif(!(preg_match('/(^[1-9]?[0-9]?[0-9]?[0-9]+$)/', $Valprecio))){
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
               $Valtipo = "Debe elegir al menos un tipo";
               $errores[] = $Valtipo;
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

    //imagen
    if ($_FILES['imagen']['size'] > 0 && count($errores) == 0) {
        $propiedades = explode("/", $_FILES['imagen']['type']);
        $extension = $propiedades[1];
        $tam_max = 1000000; // 1MB 
        $tam = $_FILES['imagen']['size'];
        $mod = true;

        if ($extension != "jpg" && $extension != "jpeg") {
            $mod = false;
            $imagenErr = "Formato debe ser jpg/jpeg";
        }

        if ($tam > $tam_max) {
            $mod = false;
            $imagenErr = "Tamaño superior al limite de: " . ($tam_max / 1000) . " KBytes";
        }

        if ($mod) {
            // guardar
            $imagen = md5($_FILES['imagen']['tmp_name'] . $_FILES['imagen']['name'] . time()) . "." . $extension;
            $controlador = ControladorImagen::getControlador();
            if (!$controlador->salvarImagen($imagen)) {
                $Errimagen = "Error al procesar la imagen y subirla al servidor";
                $errores[] = $Errimagen;
            }

            // Borrar
            $imagenAnterior = trim($_POST["imagenAnterior"]);
            if ($imagenAnterior != $imagen) {
                if (!$controlador->eliminarImagen($imagenAnterior)) {
                    $Infoimagen = "Error al borrar la antigua imagen en el servidor";
                }
            }
        } else {
            // Si no la hemos modificado
            $imagen = trim($_POST["imagenAnterior"]);
        }
    } else {
        $imagen = trim($_POST["imagenAnterior"]);
    }

    if (empty($errores)){
        $controlador = ControladorArticulo::getControlador();
        $estado = $controlador->actualizarArticulo($id, $nombre , $marca , $modelo , $tipo , $disponible , $precio , $imagen );
        if ($estado) {
            alerta("Se ha creado correctamente","../index.php");
            exit();
        } else {
            alerta("Ha fallado la modificacion");
            exit();
        }
    } else {
        alerta("Hay errores al procesar el formulario revise los errores");
    }
}



if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id =  decode($_GET["id"]);
    $controlador = ControladorArticulo::getControlador();
    $articulo = $controlador->buscarArticuloid($id);
    if (!is_null($articulo)) {
        $nombre = $articulo->getnombre();
        $nombreAnterior = $nombre;
        $marca = $articulo->getmarca();
        $modelo = $articulo->getmodelo();
        $tipo = $articulo->gettipo();
        $disponible = $articulo->getdisponible();
        $precio = $articulo->getprecio();
        $imagen = $articulo->getimagen();
        $imagenAnterior = $imagen;
    } else {
        header("location: error.php");
        exit();
    }
} else {
    header("location: error.php");
    exit();
}

?>

<?php require_once VIEW_PATH . "navbar.php"; ?>
<head>
<style type="text/css">

.banner-section{background-image:url("../imagenes/update.jpg"); background-size:1500px 350px ; height: 380px; left: 0; position: absolute; top: 0; background-position:0; background-repeat: no-repeat; }
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
            <div id='cabecera'> <h1 class="display-1 text-center">Modificar articulo</h1> </div>
            <div id="menu">    
            <ul class="list-inline text-center">
               
                    <li>Jose F |</li>
                    <li>CRUD Gestion Tienda |</li>
                    <li>Modificar</li>
                
                </ul>
                </div>
            </div>
  </div>



<div class="list-group">
    <a class="list-group-item active"> 
    <h2 class="list-group-item-heading">Formulario de Modificacion </h2>
    <p class="list-group-item-text">Edita los campos para actualizar la ficha.</p>
    </a>
</div>
<div class="well">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-content">     

<div class="lead">
<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td class="col-xs-11" class="align-left">
            <div class="lead">
            <div class="form-group <?php echo (!empty($Errnombre)) ? 'error: ' : ''; ?>">
            <label>Nombre</label>
            <input type="text" required name="nombre" pattern="([^\s][A-zÀ-ž\s]+)" title="El nombre no puede contener números" value="<?php echo $nombre; ?>">
             <span class="help-block"><?php echo $Errnombre;?></span> 
            </td>
            <!-- Fotografía -->
            <td class="col-xs-11" class="align-right">
                <label>Fotografía</label><br>
                <img src='<?php echo "../imagenes/fotos/" . $articulo->getimagen() ?>' class='rounded' class='img-thumbnail' width='150' height='auto'>
            </td>
        </tr>
    </table>
 
        <!-- modelo-->
        <div class="form-group <?php echo (!empty($Errmodelo)) ? 'error: ' : ''; ?>">
    
    <label>Modelo</label>
    <input type="text" required name="modelo" minlegth='1' maxlegth='4' title="El modelo no puede contener números" value="<?php echo $modelo; ?>">
    <span class="help-block"><?php echo $Errmodelo;?></span> 
</div>
    <!-- marca -->
    <div class="form-group  <?php echo (!empty($Errmarca)) ? 'error: ' : ''; ?>">
        <label>Marca</label>
        <input type="radio" name="marca" value="Samsung" <?php echo (strstr($marca, 'Samsung')) ? 'checked' : ''; ?>>Samsung</input>
        <input type="radio" name="marca" value="Apple" <?php echo (strstr($marca, 'Apple')) ? 'checked' : ''; ?>>Apple</input>
        <input type="radio" name="marca" value="HP" <?php echo (strstr($marca, 'HP')) ? 'checked' : ''; ?>>HP</input>
        <input type="radio" name="marca" value="Microsoft" <?php echo (strstr($marca, 'Microsoft')) ? 'checked' : ''; ?>>Microsoft</input>
        <input type="radio" name="marca" value="Xiaomi" <?php echo (strstr($marca, 'Xiaomi')) ? 'checked' : ''; ?>>Xiaomi</input>
        <input type="radio" name="marca" value="Cisco" <?php echo (strstr($marca, 'Cisco')) ? 'checked' : ''; ?>>Cisco</input>
        <span class="help-block"><?php echo $Errmarca; ?></span> 
    </div>
    <!-- Precio -->
    <div class="form-group <?php echo (!empty($Errprecio)) ? 'error: ' : ''; ?>">
        <label>Precio</label>
        <input type="text" required name="precio" pattern="([1-9]?[0-9]?[0-9]?[0-9])" minlength="1" maxlength="3" title="Inserte un numero  del 1 al 999" value="<?php echo $precio; ?>">
        <span class="help-block"><?php  echo $Errprecio; ?></span> 
    </div>
    <!-- Disponible-->
    <div class="form-group <?php echo (!empty($Errdisponible)) ? 'error: ' : ''; ?>">
        <label>Disponible</label>
        <select name="disponible">
            <option value="SI" <?php echo (strstr($disponible, 'SI')) ? 'selected' : ''; ?>>SI</option>
            <option value="NO" <?php echo (strstr($disponible, 'NO')) ? 'selected' : ''; ?>>NO</option>
        </select>
        <span class="help-block"><?php  echo $Errdisponible; ?></span> 
    </div>
    <!-- Tipo -->
    <div class="form-group <?php echo (!empty($Errtipo)) ? 'error: ' : ''; ?>"s>
        <label>Tipo</label>
        <input type="checkbox" name="tipo[]" value="Hardware" <?php echo (strstr($tipo, 'Hardware')) ? 'checked' : ''; ?>>Hardware</input>
        <input type="checkbox" name="tipo[]" value="Programa util" <?php echo (strstr($tipo, 'Programa util ')) ? 'checked' : ''; ?>>Programa Util</input>
        <input type="checkbox" name="tipo[]" value="Productividad" <?php echo (strstr($tipo, 'Productividad ')) ? 'checked' : ''; ?>>Productividad </input>
        <input type="checkbox" name="tipo[]" value="Multiples Funcionalidades" <?php echo (strstr($tipo, 'Multiples Funcionalidades')) ? 'checked' : ''; ?>>Multiples Funcionalidades</input>
        <span class="help-block"><?php echo $Errtipo; ?></span> 
    </div>
        <!-- Foto-->
        <div <?php echo (!empty($Errimagen)) ? 'error: ' : ''; ?>>
            <label>Fotografía</label>
            <input type="file" name="imagen" id="imagen" accept="image/jpeg">
            <?php echo $Errimagen; ?>
        </div>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="imagenAnterior" value="<?php echo $imagenAnterior; ?>" />
        <input type="hidden" name="nombreAnterior" value="<?php echo $nombreAnterior; ?>" />
        </div>
        </div> 
            </div>
                </div>
                    </div>
                        </div> 
                            </div>
                                                          
    <span class="list-group-item text-center">
     <a onclick="history.back()" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span> Volver</a>
     <button type="submit" name= "aceptar" value="aceptar" class=" btn btn-success" ><span class="glyphicon glyphicon-ok"></span><strong> Modificar</h5></strong>  </button>


     </span>
    </div>
    </div>  
</form>

<br>
<br>
<br>
</section>

<?php require_once VIEW_PATH . "footer.php" ?>
