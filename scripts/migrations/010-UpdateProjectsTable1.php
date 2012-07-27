<?php
class UpdateProjectsTable1 extends Akrabat_Db_Schema_AbstractChange
{
    private function _loadDependency() {
        $dir = dirname(__FILE__);
        $files = scandir($dir);
        foreach ( $files as $file ) {
            if ( preg_match('/^\d+-CreateCategoryTable\.php/', $file) ) {
                require_once $dir.DIRECTORY_SEPARATOR.$file;
            }
            if ( preg_match('/^\d+-CreateSubcategoryTable\.php/', $file) ) {
                require_once $dir.DIRECTORY_SEPARATOR.$file;
            }
            if ( preg_match('/^\d+-CreateProjectTable\.php/', $file) ) {
                require_once $dir.DIRECTORY_SEPARATOR.$file;
            }
            if ( preg_match('/^\d+-CreateUsersTable\.php/', $file) ) {
                require_once $dir.DIRECTORY_SEPARATOR.$file;
            }
        }
    }
    
    public function up() {
        $this->_loadDependency();
        
        $sql = "ALTER TABLE  `".CreateProjectTable::TABLE."` CHANGE `category_id` `category_id` INT(10) UNSIGNED NULL,
                CHANGE  `performer_from_id`  `performer_from_id` INT( 10 ) UNSIGNED NULL";
        
        $this->_db->query($sql);
        
        $sql = "ALTER TABLE `".self::TABLE."` DROP FOREIGN KEY `".self::TABLE."_ibfk_1`, ADD FOREIGN KEY (`owner_id`)
            REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE";
        $this->_db->query($sql);
        
        $sql = "ALTER TABLE `".self::TABLE."` DROP FOREIGN KEY `".self::TABLE."_ibfk_2`, ADD FOREIGN KEY (`category_id`)
            REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE";
        $this->_db->query($sql);
        
        $sql = "ALTER TABLE `".self::TABLE."` DROP FOREIGN KEY `".self::TABLE."_ibfk_3`, ADD FOREIGN KEY (`performer_from_id`)
            REFERENCES `places`(`id`) ON DELETE SET NULL ON UPDATE CASCADE";
        $this->_db->query($sql);
    }
    
    public function down() {
        
    }
}
