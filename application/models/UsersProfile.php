<?php

class Rabotal_Model_UsersProfile extends Zend_Db_Table_Abstract
{
    protected $_name = 'users_profile';
    protected $_primary = 'user_id';
    protected $_sequence = false;
    
    protected $_referenceMap = array(
        'User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Rabotal_Model_Users',
            'refColumns' => 'id'
        )
    );
}

