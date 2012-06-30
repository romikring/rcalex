<?php

class CreateProjectsFilesTable extends Akrabat_Db_Schema_AbstractChange {

    const TABLE = "projects_files";
    
    private function _loadDependency() {
        $dir = dirname(__FILE__);
        $files = scandir($dir);
        foreach ( $files as $file ) {
            if ( preg_match('/^\d+-CreateProjectTable\.php/', $file) ) {
                require_once $dir.DIRECTORY_SEPARATOR.$file;
            }
        }
    }

    public function up() {
        $sql = "CREATE TABLE `". self::TABLE ."` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `project_id` int(10) unsigned DEFAULT NULL,
            `label` varchar(32) NOT NULL,
            `date` int(10) unsigned NOT NULL,
            `name` varchar(250) NOT NULL,
            `path` varchar(100) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `project_id` (`project_id`),
            KEY `label` (`label`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $this->_db->query($sql);
        
        $this->_loadDependency();
        
        $sql  = "ALTER TABLE `".self::TABLE."` ADD FOREIGN KEY (`project_id`) ";
        $sql .= "REFERENCES  `".CreateProjectTable::TABLE."` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT";
        $this->_db->query($sql);
    }

    public function down() {
        $this->_loadDependency();
        
        $this->_db->query("ALTER TABLE `".self::TABLE."` DROP FOREIGN KEY `".self::TABLE."_ibfk_1`");
        $this->_db->query("DROP TABLE ".self::TABLE);
    }

}
