<?php
class CreateUsersTable extends Akrabat_Db_Schema_AbstractChange
{
    protected $table = "users";
    
    public function up() {
        $config = Zend_Registry::get('config');
        
        $sql =
            "CREATE TABLE IF NOT EXISTS `{$this->table}` (
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
        
        $admin = array(
            'email' => $config->site->admin->email,
            'username' => $config->site->admin->username,
            'password' => sha1($config->site->secure->salt.$config->site->admin->password),
            'role' => 'administrator',
            'date' => time(),
            'status' => 'active'
        );
        
        $this->_db->insert($this->table, $admin);
    }
    
    public function down() {
        $this->_db->query("DROP TABLE IF EXISTS {$this->table}");
    }
}
