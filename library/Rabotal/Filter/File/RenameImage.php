<?php

class Rabotal_Filter_File_RenameImage extends Zend_Filter_File_Rename {
    public function getNewName($value, $source = false) {
        $fileData = $this->_getFileName($value);
        $fileOptions = $this->getFile();
        
        $uploadedFile = $fileData['source'];
        if ( !file_exists($uploadedFile) ) {
            throw new Zend_File_Transfer_Exception('Uploaded file not found: '.$uploadedFile);
        }
        if ( !is_readable($uploadedFile) ) {
            throw new Zend_File_Transfer_Exception('Uploaded file doesn\'r readable: '.$uploadedFile);
        }
        
        $file = file_get_contents($fileData['source']);
        $hash = md5($file).".png";
        $targetPath = $fileOptions[0]['target'];
        $dest = $targetPath.DIRECTORY_SEPARATOR.substr($hash, 0, 5);
        
        if ( !file_exists($dest) && FALSE === @mkdir($dest, 0755, true) ) {
            throw new Zend_File_Transfer_Exception("Directory didn't create: $dest");
        }
        if ( !is_writable($targetPath) ) {
            throw new Zend_File_Transfer_Exception("Path doesn't writable: $targetPath");
        }
        
        $fileData['target'] = $dest.DIRECTORY_SEPARATOR.$hash;
                
        return $fileData;
    }
    
    public function filter($value) {
        $file   = $this->getNewName($value, true);
        if (is_string($file)) {
            return $file;
        }
        if ( FALSE !== ($img = imagecreatefromstring(file_get_contents($file['source']))) ) {
            if ( imagepng($img, $file['target'], 8) ) {
                imagedestroy($img);
                return $file['target'];
            }
        }

        require_once 'Zend/Filter/Exception.php';
        throw new Zend_Filter_Exception(sprintf("File '%s' could not be renamed. An error occured while processing the file.", $value));
    }
}
