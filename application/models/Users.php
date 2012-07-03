<?php

class Rabotal_Model_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_primary = 'id';
    protected $_sequence = true;
    
    /**
     * @param string $val Username or email
     * @return Zend_Db_Table_Row_Abstract
     */
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
    
    public function mklogin( $base ) {
        $base = preg_replace('/[^_a-z0-9]+/', '', $base);
        
        if ( empty($base) || !is_string($base) )
            throw new Zend_Exception("mklogin function required 1 string parameter");
        
        $db = $this->getAdapter();
        $result = $this->fetchRow($db->quoteInto('username = ?', $base));
        if ( $result ) {
            if ( preg_match('/(\d+)$/', $base, $m) ) {
                return $this->mklogin(substr($base, 0, -1*strlen($m[1])) . (1 + $m[1]));
            } else {
                return $this->mklogin($base."1");
            }
        }
        return $base;
    }


    public function __toString() {
        return $this->_name;
    }
}

