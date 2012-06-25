<?php
class CreateUserProfileTable extends Akrabat_Db_Schema_AbstractChange
{
    protected $table = 'users_profile';
    
    public function up() {
        $sql = "CREATE TABLE `{$this->table}` (
            `user_id` int(10) unsigned NOT NULL,
            `fullname` varchar(150) DEFAULT NULL,
            `forgot_key` char(32) DEFAULT NULL,
            PRIMARY KEY (`user_id`),
            UNIQUE KEY `forgot_key` (`forgot_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $this->_db->query($sql);
    }
    
    public function down() {
        $this->_db->query("DROP TABLE `{$this->table}`");
    }
}
