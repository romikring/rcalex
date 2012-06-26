<?php
class CreateUsersTable extends Akrabat_Db_Schema_AbstractChange
{
    const TABLE = "users";
    
    public function up() {
        $sql =
            "CREATE TABLE IF NOT EXISTS `" . self::TABLE . "` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `email` varchar(60) NOT NULL,
                `username` varchar(20) NOT NULL,
                `password` char(40) NOT NULL,
                `avatar` varchar(250) NOT NULL,
                `role` enum('member','expert','moderator','administrator') NOT NULL DEFAULT 'member',
                `date` int(10) unsigned NOT NULL,
                `auto_signin_key` varchar(32) NOT NULL,
                `status` enum('active','blocked') NOT NULL DEFAULT 'active',
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $this->_db->query($sql);
    }
    
    public function down() {
        $this->_db->query("DROP TABLE IF EXISTS ".self::TABLE);
    }
}
