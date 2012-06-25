<?php

class Rabotal_View_Helper_Splitter extends Zend_View_Helper_Abstract {
    public function splitter( $separator = '|' ) {
        $args = func_get_args();
        // Remove separator
        unset( $args[0] );
        
        if ( count($args) <= 0 )
            return array();
        
        $output = array();
        foreach ( $args as $arg ) {
            $parts = explode($separator, $arg);
            for ( $i = 0; $i < count($parts); ++$i ) {
                if ( !isset($output[$i]) )
                    $output[$i] = array($parts[$i]);
                else
                    $output[$i][] = $parts[$i];
            }
        }
            
        return $output;
    }
}
