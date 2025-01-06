<?php
namespace app\file;

class SecureImport {
    public static function includeFile(string $fileFullPath) : void {
        if(!file_exists($fileFullPath)) {
            throw new \Exception("No se ha podido cargar el fichero \"{$fileFullPath}\".");
        }
        include_once $fileFullPath;
    }
    
    public static function fixFileResourcePath(string $originalResource, string $defaultResource) : string {
        return self::getImageBase64(file_exists($originalResource) ? $originalResource : $defaultResource);
    }
    
    public static function getImageBase64(string $path) : string {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    
    public static function getFileBase64(string $path) : string {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:text/' . $type . ';base64,' . base64_encode($data);
    }
}
