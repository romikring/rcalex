<?php
class CreateCategoryTable extends Akrabat_Db_Schema_AbstractChange
{
    const TABLE = 'categories';
    
    public function up() {
        $sql = "CREATE TABLE `".self::TABLE."` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(150) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $this->_db->query($sql);
    }
    
    public function down() {
        $this->_db->query("DROP TABLE ".self::TABLE);
    }
}
