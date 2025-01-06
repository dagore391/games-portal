<?php
    namespace app\security;
    
    class Captcha {
		public static function check(string $captcha) : bool {
			$result = Session::exists('captcha') && Session::get('captcha') == $captcha;
            return $result;
        }
		
        public static function generate() : string {
			// Se genera un código aleatorio para el captcha y se almacena en una variable de sesión.
			Session::put('captcha', strtoupper(Security::generateRandomString(7)));
			// Se prepara el "lienzo" de la imagen.
			$img = imagecreatetruecolor(100,20);
			imagealphablending($img, false);
			imagesavealpha($img, true);
			// Se ajusta el fondo de la imagen.
			$bg = imagecolorallocatealpha($img, 0, 0, 0, 127);
			imagecolortransparent($img, $bg);
			imagefill($img, 0, 0, $bg);
			// Se define el color que se utilizará para dibujar los elementos de la imagen.
			$color = imagecolorallocatealpha($img, 0, 0, 0,0);
			// Se dibujan el código y las líneas de la imagen.
			imagestring($img, 5, 18, 3, Session::get('captcha'), $color);
			imageline($img, 10, 8, 87, 8, $color);
			imageline($img, 13, 12, 90, 12, $color);
			// Se genera la imagen.
			ob_start();
			imagepng($img);
			// Se codifica en base 64.
			$img64 = base64_encode(ob_get_clean());
			return 'data:image/png;base64,'.$img64;
        }
    }
?>
