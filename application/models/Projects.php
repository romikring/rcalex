<?php

class Rabotal_Model_Projects extends Zend_Db_Table_Abstract
{
    protected $_name = 'projects';
    protected $_primary = 'id';
    protected $_sequence = true;
    
    protected $_referenceMap = array(
        'Owner' => array(
            'columns' => 'owner_id',
            'refTableClass' => 'Rabotal_Model_Users',
            'refColumns' => 'id'
        ),
        'Category' => array(
            'columns' => 'category_id',
            'refTableClass' => 'Rabotal_Model_Categories',
            'refColumns' => 'id'
        )
    );
}

