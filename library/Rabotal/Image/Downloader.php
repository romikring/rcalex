<?php

class Rabotal_Image_Downloader {
    private $_target;
    private $_url;
    private $_user_agent = 'Mozilla/5.0 (X11; Linux x86) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.46 Safari/535.11';
    
    public function __construct(array $options = null) {
        if ( $options ) {
            if ( !empty($options['target']) )   $this->setTarget($options['target']);
            if ( !empty($options['url']) )      $this->setImageUrl($options['url']);
            if ( !empty($options['agent']) )    $this->setUserAgent($options['agent']);
        }
    }
    
    public function setUserAgent( $agent ) {
        $this->_user_agent = $agent;
    }

        /**
     * @param string $path 
     */
    public function setTarget( $path ) {
        if ( !is_string($path) ) {
            throw new Rabotal_Filter_Exception('Incorrect path type. It should be string');
        }
        $path = realpath(trim($path));
        if ( empty($path) ) {
            throw new Rabotal_Filter_Exception('Path required and cannot be empty');
        }
        
        if ( !is_dir($path) ) {
            throw new Rabotal_Filter_Exception('Path string should be path to directory');
        }
        
        if ( !is_writable($path) ) {
            throw new Rabotal_Filter_Exception('Directory doesn\'t writable: '.$path);
        }
        
        $this->_target = $path;
    }
    
    public function setImageUrl( $url ) {
        if ( !is_string($url) ) {
            throw new Rabotal_Filter_Exception('Image URL should be string');
        }
        $this->_url = trim($url);
    }
    
    public function download() {
        if ( empty($this->_target) ) {
            throw new Rabotal_Filter_Exception('Target directory is required and can\'t be empty');
        }
        if ( empty($this->_url) ) {
            throw new Rabotal_Filter_Exception('Image url is required and can\'n be empty');
        }
        
        $ch = curl_init($this->_url);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ( $this->_user_agent ) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->_user_agent);
        }
        $data = curl_exec($ch);
        
        if ( curl_errno($ch) ) {
            curl_close($ch);
            throw new Rabotal_Filter_Exception('Image download error by url: '.$this->_url);
        }
        curl_close($ch);
        
        if ( FALSE === ($image = imagecreatefromstring($data)) ) {
            throw new Rabotal_Filter_Exception('Image create error. Did you paste image link?');
        }
        
        $hash = md5($data).'.png';
        $dir = substr($hash, 0, 5);
        $fullTarget = $this->_target.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$hash;
        
        if ( !file_exists($this->_target.DIRECTORY_SEPARATOR.$dir) ) {
            if ( FALSE === @mkdir($this->_target.DIRECTORY_SEPARATOR.$dir) ) {
                imagedestroy($image);
                throw new Rabotal_Filter_Exception('I can\'t create directory for save an image');
            }
        }
        
        if ( FALSE === @imagepng($image, $fullTarget, 8) ) {
            imagedestroy($image);
            throw new Rabotal_Filter_Exception('Image save error.');
        }
        imagedestroy($image);
        
        return DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$hash;
    }
}
