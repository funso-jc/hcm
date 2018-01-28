
  <?php
  /*
  Iniciamos la sesi�n.  Esto se utiliza para el envio de variable de sesi�n.
  Mediante las variables de sesi�n enviamos la cadena de 5 caracteres que el 
  usuario debe introducir en el formulario para verificar que no se trata de
  un robot.

Si quieres saber m�s sobre sesiones visita:
http://www.php.net/manual/es/ref.session.php
*/
session_start();
/*
Creaci�n de una cadena aleatoria a partir de las funciones de fecha de php.
La funci�n md5() retorna una cadena de 32 caracteres alfanum�ricos, mediante el algoritmo de encriptaci�n md5.
*/
$md5 = md5(microtime() * mktime());
/*
Para este ejemplo s�lamente necesitamos 5 caracteres de los 32 que genera la funci�n md5() por lo tanto escojemos los 5 primeros caracteres de la cadena.
*/
$string = substr($md5,0,5);
/*
A continuaci�n creamos una imagen a partir de un fondo que hemos subido previamente al servidor. Generalmente este fondo se encuentra distorsionada.
*/
$captcha = imagecreatefrompng("http://www.cajaweb.heros.com.ve/captcha/botproofemail_31/botproofemail.php/captcha.png");
/*
Editamos los colores de la imagen, tanto de los caracteres y de las l�neas
*/
$color_letras = imagecolorallocate($captcha, 0, 0, 0);
$color_lineas = imagecolorallocate($captcha,255,0,0);

/*
A�adiremos unas cuantas l�neas a nuestra imagen para evitar que que los robots lean el contenido de la imagen.
*/
imageline($captcha,rand(0,100),0,rand(0,50),50,$color_lineas);
imageline($captcha,rand(0,100),0,rand(0,50),50,$color_lineas);
imageline($captcha,rand(0,100),0,rand(0,50),50,$color_lineas);
imageline($captcha,rand(0,100),0,rand(0,50),50,$color_lineas);
/*
Ahora escribimos la cadena generada aleatoriamente en la imagen
*/
imagestring($captcha, 5, 20, 10, $string, $color_letras);
/*
Encriptamos la variable de sesi�n con la funci�n md5() y la establecemos como una variable de sesi�n, para poder verificarla al enviar el formulario.
*/
$_SESSION['codigo_verificacion'] = md5($string);
/*
Devolvemos la imagen para mostrarla en el formulario.
*/
header("Content-type: image/png");

imagepng($captcha);
?>
