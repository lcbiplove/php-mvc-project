<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Find by username
     * 
     * @param string $username username of users table
     * 
     * @return mixed object if true, false otherwise
     */
    public static function findByUsername($username)
    {
        $sql = 'SELECT * FROM users 
                WHERE username = :username';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Find By user id
     * 
     * 
     */
    public static function findByUserId($id)
    {
        $sql = 'SELECT * FROM users 
                WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Verify password
     * 
     * @return true if password is correct, false otherwise
     */
    public function verifyPassword()
    {
        $users = static::findByUsername($this->username);
        if (password_verify($this->password, $users->password)) {
            return true;            
        }
        return false;
    }

}
