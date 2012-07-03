<?php

class Rabotal_Model_ProjectsFiles extends Zend_Db_Table_Abstract
{
    protected $_name = 'projects_files';
    protected $_primary = 'id';
    protected $_sequence = true;
    
    protected $_referenceMap = array(
        'Project' => array(
            'columns' => 'project_id',
            'refTableClass' => 'Rabotal_Model_Projects',
            'refColumns' => 'id'
        )
    );

}

