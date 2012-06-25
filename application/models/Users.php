<?php

class Rabotal_Model_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_primary = 'id';
    protected $_sequence = true;
    
    public function getByEmailOrLogin($val) {
        $query = $this->select();
        if ( FALSE !== strpos($val, '@') ) {
            $query->where('email = ?', $val);
        } else {
            $query->where('username = ?', $val);
        }
        
        return $this->fetchRow($query);
    }
    
    /**
     * Return all available roles
     * 
     * @return array|FALSE
     */
    public function getRoles() {
        $m = array();
        $desc = $this->getAdapter()->describeTable($this->_name);
        if ( preg_match_all('/(?:\'([^\']+)\')+/', $desc['role']['DATA_TYPE'], $m) ) {
            return $m[1];
        }
        return FALSE;
    }
    
    public function __toString() {
        return $this->_name;
    }
}

