<?php

class Rabotal_View_Helper_Bbcode extends Zend_View_Helper_Abstract {
    
    public function bbcode($text) {
        $text = $this->convertVideos($text);
        $text = $this->convertImages($text);
        
        return $text;
    }
    
    private function convertImages($text) {
        $m = array();
        
        if ( preg_match_all('/(\[img[^\]]+\])/i', $text, $m) ) {
            $idx = 0;
            do {
                $img = html_entity_decode($m[1][$idx], ENT_QUOTES);
                $src = ( preg_match('/img=[\'"]?([^\'"\s]+)\1?[^\]]*]/i', $img, $match)) ? $match[1] : NULL;
                
                if ( !empty($src) ) {
                    $alt = (int)( preg_match('/alt=[\'"]?([\w\s]+)\1?/i', $img, $match)) ? $match[1] : '';
                
                    $to_replace = sprintf('<img src="%s" alt="%s" />', $src, $alt);
                } else {
                    $to_replace = '';
                }
                
                $text = str_replace($m[1][$idx], $to_replace, $text);
            } while ( ++$idx < count($m[1]) );
        }
        
        return $text;
    }
    
    private function convertVideos($text) {
        $m = array();
        
        if ( preg_match_all('/(\[video[^\]]+\])/i', $text, $m) ) {
            $idx = 0;
            do {
                $iframe = html_entity_decode($m[1][$idx], ENT_QUOTES);
                $src = ( preg_match('/video=[\'"]?([^\'"\s]+)\1?[^\]]*]/i', $iframe, $match)) ? $match[1] : NULL;

                if ( !empty($src) ) {
                    $width = (int)( preg_match('/width=[\'"]?(\d+)\1?/i', $iframe, $match)) ? $match[1] : 540;
                    $width = min(( $width ) ? $width : 540, 540);

                    $height = (int)( preg_match('/height=[\'"]?(\d+)\1?/i', $iframe, $match)) ? $match[1] : 360;
                    $height = min(( $height ) ? $height : 360, 360);

                    $to_replace = sprintf('<iframe width="%d" height="%d" src="%s" scrolling="no" frameborder="0" allowfullscreen></iframe>', $width, $height, $src);
                } else {
                    $to_replace = '';
                }
                
                $text = str_replace($m[1][$idx], $to_replace, $text);
            } while ( ++$idx < count($m[1]) );
        }
        
        return $text;
    }
}
