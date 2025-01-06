<?php
namespace app\file;

/**
 * Esta clase es la responsable de gestionar y subir de ficheros al servidor.
 * @author David González Requejo
 * @version 0.0.1
 */
class UploadFile {
    private static $_types = ['application/pdf' => 'pdf', 'image/gif' => 'gif', 'image/jpeg' => 'jpg'];
    
    /**
     * Permite subir un fichero al servidor.
     * @param $file array Fichero ($_FILE) del fichero que se va a subir al servidor.
     * @param $uploadPath string Ruta relativa donde se va a alojar el fichero.
     * @param $finalName string Nombre con el que se guardará el fichero en el servidor.
     * @param $mimeTypesAllowed array Posibles tipos que a los que tiene que ajustarse el fichero que se está subiendo al servidor.
     * @param $limitFileSize int Tamaño máximo, en bytes, que puede tener el fichero que está subiendo al servidor.
     * @return array Devuelve la lista de errores que se han producido durante la subida. Si todo ha ido bien el array estará vacío.
     */
    public static function upload(array &$file, string $uploadPath, string $finalName, array $mimeTypesAllowed, int $limitFileSize, bool $replace, int $width = null, int $height = null) : array {
        $errors = [];
        // Se comprueba que el nombre temporal y el tipo del fichero estén definidos.
        if(!isset($file['tmp_name'], $file['type']) || $file['tmp_name'] === '' || $file['type'] === '') {
            array_push($errors, 'El nombre temporal o el tipo del fichero "' . $file['type'] . '" no están definidos.');
            return $errors;
        }
        // Se comprueba si el fichero tiene un tipo permitido para ser subido al servidor.
        if(!self::checkMimeType($file['type'], $mimeTypesAllowed)) {
            array_push($errors, 'El tipo "' . $file['type'] . '" no está permitido.');
            return $errors;
        }
        // Se comprueban las dimensiones del fichero si es una imagen.
        if(!empty($width) && !empty($height)) {
            $imageInfo = getimagesize($file["tmp_name"]);
            if(!empty($imageInfo[0]) || $imageInfo[0] > $width || !empty($imageInfo[1]) || $imageInfo[1] > $height) {
                array_push($errors, 'La imagen no puede superar los ' . $width . 'x' . $height . ' píxeles.');
                return $errors;
            }
        }
        // Se define el que tendrá el fichero en el servidor.
        $fileFullPath = $uploadPath . DIRECTORY_SEPARATOR . $finalName . '.' . self::$_types[$file['type']];
        // Se comprueba que el fichero a subir no tenga errores.
        if(!isset($file['error']) || $file['error'] != \UPLOAD_ERR_OK) {
            array_push($errors, 'No se ha podido subir el fichero.');
        }
        // Se valida el tamaño del fichero.
        if(!isset($file['size']) || $file['size'] > $limitFileSize) {
            array_push($errors, 'El fichero supera el tamaño máximo permitido.');
        }
        // Se comprueba si existe el directorio de destino.
        if(!file_exists($uploadPath) || !is_dir($uploadPath)) {
            array_push($errors, 'La ruta donde se intenta subir el fichero no es válida.');
        }
        // Se comprueba si se tienen permisos de escritura sobre el directorio de destino.
        if(!is_writable($uploadPath)) {
            array_push($errors, 'No se disponen de permisos suficientes para escribir en la ruta indicada.');
        }
        // Si no se permiten reemplazos, se verifica que el fichero no exista.
        if(!$replace && file_exists($fileFullPath)) {
            array_push($errors, 'El fichero "' . $fileFullPath . '" ya existe en el servidor.');
        }
        // Se comprueba si se han produccido errores durante las validaciones.
        if(sizeof($errors) != 0) {
            return $errors;
        }
        // Se sube el fichero.
        if(!move_uploaded_file($file['tmp_name'], $fileFullPath)) {
            array_push($errors, 'No se ha podido subir el fichero "' . $file['tmp_name'] . '".');
        }
        // Se devuelve true si todo ha ido bien.
        return $errors;
    }
    
    /**
     * Comprueba si un mimetype está permitido o no.
     * @param $mimeType string Tipo a validar.
     * @param $mimeTypesAllowed array Tipos permitidos.
     * @return bool Devuelve true si es un tipo válido y false si no lo es.
     */
    public static function checkMimeType(string $mimeType, array $mimeTypesAllowed) : bool {
        foreach($mimeTypesAllowed as $type) {
            if(array_key_exists($type, self::$_types) && in_array($mimeType, $mimeTypesAllowed)) {
                return true;
            }
        }
        return false;
    }
}
