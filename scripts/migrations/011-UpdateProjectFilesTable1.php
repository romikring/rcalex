<?php
class UpdateProjectFilesTable1 extends Akrabat_Db_Schema_AbstractChange
{
    private function _loadDependency() {
        $dir = dirname(__FILE__);
        $files = scandir($dir);
        foreach ( $files as $file ) {
            if ( preg_match('/^\d+-CreateProjectsFilesTable\.php/', $file) ) {
                require_once $dir.DIRECTORY_SEPARATOR.$file;
            }
        }
    }
    
    public function up() {
        $this->_loadDependency();
        
        $table = CreateProjectsFilesTable::TABLE;
        
        $sql = "ALTER TABLE  `{$table}` CHANGE  `id`  `id` CHAR(32) CHARACTER SET ASCII COLLATE ascii_bin NOT NULL";
        $this->_db->query($sql);
        $sql = "ALTER TABLE `{$table}` DROP `label`, DROP `path`";
        $this->_db->query($sql);
        $sql = "ALTER TABLE  `{$table}` DROP FOREIGN KEY  `{$table}_ibfk_1`";
        $this->_db->query($sql);
        $sql = "ALTER TABLE  `{$table}` CHANGE  `project_id`  `project_uniq_id` CHAR(32) CHARACTER SET ASCII COLLATE ascii_bin NOT NULL";
        $this->_db->query($sql);
        $sql = "ALTER TABLE {$table} DROP INDEX project_id";
        $this->_db->query($sql);
        $sql = "ALTER TABLE {$table} DROP PRIMARY KEY";
        $this->_db->query($sql);
        $sql = "ALTER TABLE  `{$table}` ADD PRIMARY KEY (`id`,`project_uniq_id`)";
        $this->_db->query($sql);
    }
    
    public function down() {
    }
}
