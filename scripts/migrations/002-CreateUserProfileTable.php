<?php
class CreateUserProfileTable extends Akrabat_Db_Schema_AbstractChange
{
    const TABLE = 'users_profile';
    
    public function up() {
        $dir = dirname(__FILE__);
        $files = scandir($dir);
        foreach ( $files as $file ) {
            if ( preg_match('/^\d+-CreateUsersTable\.php/', $file) ) {
                include_once $dir.DIRECTORY_SEPARATOR.$file;
                break;
            }
        }
            
        $config = Zend_Registry::get('config');
        
        $sql = "CREATE TABLE IF NOT EXISTS`" . self::TABLE . "` (
            `user_id` int(10) unsigned NOT NULL,
            `fullname` varchar(150) DEFAULT NULL,
            `forgot_key` char(32) DEFAULT NULL,
            PRIMARY KEY (`user_id`),
            UNIQUE KEY `forgot_key` (`forgot_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $this->_db->query($sql);
        
        $sql = "ALTER TABLE `".self::TABLE."` ADD FOREIGN KEY (`user_id`) REFERENCES `".CreateUsersTable::TABLE."` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT";
        $this->_db->query($sql);
        
        $admin = array(
            'email' => $config->site->admin->email,
            'username' => $config->site->admin->username,
            'password' => sha1($config->secure->salt.$config->site->admin->password),
            'role' => 'administrator',
            'date' => time(),
            'status' => 'active'
        );
        $this->_db->insert(CreateUsersTable::TABLE, $admin);
        
        $admin_profile = array(
            'user_id' => $this->_db->lastInsertId(CreateUsersTable::TABLE, 'id'),
            'fullname' => NULL,
            'forgot_key' => NULL
        );
        $this->_db->insert(self::TABLE, $admin_profile);
    }
    
    public function down() {
        $this->_db->query("DROP TABLE IF EXISTS ". self::TABLE);
    }
}
