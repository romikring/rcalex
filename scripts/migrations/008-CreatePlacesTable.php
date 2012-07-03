<?php

class CreatePlacesTable extends Akrabat_Db_Schema_AbstractChange {

    const TABLE = 'places';
    
    private function _loadDependency() {
        $dir = dirname(__FILE__);
        $files = scandir($dir);
        foreach ( $files as $file ) {
            if ( preg_match('/^\d+-CreateProjectTable\.php/', $file) ) {
                require_once $dir.DIRECTORY_SEPARATOR.$file;
                break;
            }
        }
    }

    public function up() {
        $sql = "CREATE TABLE `". self::TABLE ."` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8";
        
        $this->_db->query($sql);
        
        $this->_loadDependency();
        
        $sql  = "ALTER TABLE  `".CreateProjectTable::TABLE."` ADD FOREIGN KEY (`performer_from_id`) ";
        $sql .= "REFERENCES `".self::TABLE."` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT";
        $this->_db->query($sql);
    }

    public function down() {
        $this->_loadDependency();
        $this->_db->query("ALTER TABLE `".CreateProjectTable::TABLE."` DROP FOREIGN KEY `".CreateProjectTable::TABLE."_ibfk_3`");
        $this->_db->query("DROP TABLE ".self::TABLE);
    }
}
