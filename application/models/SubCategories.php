<?php

class Rabotal_Model_SubCategories extends Zend_Db_Table_Abstract
{
    protected $_name = 'sub_categories';
    protected $_primary = 'id';
    protected $_sequence = true;
    
    protected $_referenceMap = array(
        'Category' => array(
            'columns' => 'category_id',
            'refTableClass' => 'Rabotal_Model_Categories',
            'refColumns' => 'id'
        )
    );
}
