<?php
class AddUniqIdInProjects extends Akrabat_Db_Schema_AbstractChange
{
    const TABLE = "projects";
    
    public function up() {
        $sql = "ALTER TABLE  `".self::TABLE."` ADD `uniq_id` CHAR(32) CHARACTER SET ASCII COLLATE ascii_bin NOT NULL AFTER  `owner_id`,
                ADD UNIQUE (`uniq_id`)";
        
        $this->_db->query($sql);
    }
    public function down() {}
}
