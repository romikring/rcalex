<?php

class Rabotal_Model_ProjectsFiles extends Zend_Db_Table_Abstract
{
    protected $_name = 'projects_files';
    protected $_primary = array('id', 'project_uniq_id');
    protected $_sequence = false;
    
    protected $_referenceMap = array(
        'Project' => array(
            'columns' => 'project_uniq_id',
            'refTableClass' => 'Rabotal_Model_Projects',
            'refColumns' => 'uniq_id'
        )
    );

}

