<?php
class CreateSubcategoryTable extends Akrabat_Db_Schema_AbstractChange
{
    const TABLE = 'sub_categories';
    
    public function up() {
        $sql = "CREATE TABLE `".self::TABLE."` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `category_id` int(10) unsigned NOT NULL,
            `name` varchar(150) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `category_id` (`category_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $this->_db->query($sql);
    }
    
    public function down() {
        $this->_db->query("DROP TABLE ".self::TABLE);
    }
}
