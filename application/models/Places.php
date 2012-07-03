<?php

class Rabotal_Model_Places extends Zend_Db_Table_Abstract
{
    protected $_name = 'places';
    protected $_primary = 'id';
    protected $_sequence = true;
    
    public function getIdByNameOrSave($place_name) {
        $place = $this->fetchRow($this->getAdapter()->quoteInto("name = ?", $place_name));
        if ( $place ) {
            return $place->id;
        } else {
            return $this->insert(array('name' => $place_name));
        }
    }
}
