<?php
class CreateProjectTable extends Akrabat_Db_Schema_AbstractChange
{
    const TABLE = 'projects';
    
    public function up() {
        $sql = "CREATE TABLE `". self::TABLE ."` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `owner_id` int(10) unsigned NOT NULL,
            `title` varchar(70) NOT NULL,
            `description` varchar(550) NOT NULL,
            `status` enum('active','blocked') NOT NULL DEFAULT 'active',
            `date` int(10) unsigned NOT NULL,
            `budget` varchar(30) NOT NULL,
            `demand_period` int(11) NOT NULL,
            `category_id` int(10) unsigned NOT NULL,
            `sub_category_id` int(10) unsigned NOT NULL,
            `performer_from_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `owner_id` (`owner_id`),
            KEY `category_id` (`category_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $this->_db->query($sql);
    }
    public function down() {
        $this->_db->query('DROP TABLE '.self::TABLE);
    }
}
