<?php
class UpdateTableRelations extends Akrabat_Db_Schema_AbstractChange
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
        
        $sql = "ALTER TABLE `".CreateProjectTable::TABLE."` ADD FOREIGN KEY (`owner_id`) ";
        $sql .= "REFERENCES  `".CreateUsersTable::TABLE."` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT";
        $this->_db->query($sql);
        
        $sql = "ALTER TABLE `".CreateProjectTable::TABLE."` ADD FOREIGN KEY (`category_id`) ";
        $sql .= "REFERENCES  `".CreateCategoryTable::TABLE."` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT";
        $this->_db->query($sql);
        
        $sql = "ALTER TABLE `".CreateSubcategoryTable::TABLE."` ADD FOREIGN KEY (`category_id`) ";
        $sql .= "REFERENCES  `".CreateCategoryTable::TABLE."` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT";
        $this->_db->query($sql);
        
    }
    
    public function down() {
        $this->_loadDependency();
        
        $this->_db->query("ALTER TABLE `".CreateProjectTable::TABLE."` DROP FOREIGN KEY `".CreateProjectTable::TABLE."_ibfk_2`");
        $this->_db->query("ALTER TABLE `".CreateProjectTable::TABLE."` DROP FOREIGN KEY `".CreateProjectTable::TABLE."_ibfk_1`");
        $this->_db->query("ALTER TABLE `".CreateSubcategoryTable::TABLE."` DROP FOREIGN KEY  `".CreateSubcategoryTable::TABLE."_ibfk_1`");
    }
}
