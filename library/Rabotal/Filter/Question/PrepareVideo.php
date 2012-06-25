<?php

class Rabotal_Filter_Question_PrepareVideo implements Zend_Filter_Interface {
    public function filter($value) {
        $plugin = new Rabotal_Controller_Action_Helpers_BBPrepare;
        
        return $plugin->prepareVideoLinks($value);
    }
}
