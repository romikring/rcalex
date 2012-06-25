<?php

class Rabotal_Controller_Action_Helpers_BBPrepare extends Zend_Controller_Action_Helper_Abstract {
    
    /**
     * @param string $text
     * @return string
     */
    public function prepareVideoLinks($text) {
        if ( !$text )
            return $text;
        
        $m = array();
        if ( preg_match_all('/(\[video=([^\s\]]+)[^\]]*])/i', $text, $m) ) {
            for ( $i = 0; $i < count($m[2]); ++$i ) {
                $link = $m[2][$i];
                if ( FALSE !== strpos(strtolower($link), 'youtube.com/') ||
                     FALSE !== strpos(strtolower($link), 'youtu.be/') ||
                     FALSE !== strpos(strtolower($link), 'y2u.be/')
                ) {
                    if ( FALSE !== ($id = $this->getYoutubeID($link)) ) {
                        $text = str_replace($m[2][$i], 'http://www.youtube.com/embed/'.$id, $text);
                    }
                } else {
                    // Get rid unknown video link
                    $text = str_replace($m[1][$i], '', $text);
                }
            }
        }
        
        return $text;
    }
    
    private function getYoutubeID($videoLink) {
        $m = array();
        
        if ( preg_match('/youtu\.be\/([^\s\?\#\/]+)/i', $videoLink, $m) ) {
            return $m[1];
        }
        
        if ( FALSE !== ($parts = parse_url($videoLink)) && $parts['query'] ) {
            parse_str($parts['query'], $m);
            if ( !empty($m['v']) )
                return $m['v'];
        }
        
        if ( preg_match('/.*\/v\/([^\/\?\#\/]+)/i', $videoLink, $m) ) {
            return $m[1];
        }
        
        return FALSE;
    }
}
