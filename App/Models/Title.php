<?php

namespace App\Models;

use PDO;
/**
 * Class Title
 * 
 * PHP 7.3
 */
class Title extends \Core\Model
{
    /**
     * Title
     * 
     * @var string title
     */
    public $title;

    /**
     * Is public
     * 
     * @var string privated
     */
    public $privated;

    /**
     * Constructor 
     * 
     * @param string $title
     * 
     * @return void
     */
    public function __construct($title = "", $privated = 1)
    {
        $this->title = $title;
        $this->privated = $privated;
    }

    /**
     * Save title to title table
     * 
     * @param string $id is foreign key, pk of users table
     * 
     * @return boolean
     */
    public function save($id)
    {
        date_default_timezone_set('Asia/Kathmandu');
        $date = date("Y-m-d H:i:s", time());
    
        $sql = 'INSERT INTO title (title_name, is_private, date, id) VALUES (:title, :privated, :date, :id)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
            
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':privated', $this->privated, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Get latest titleId for upload
     * 
     * @param string $id id of user
     * 
     * @return mixed string if true, false otherwise
     */
    public static function getLatestTitleId($id)
    {
        $sql = 'SELECT * FROM title 
                WHERE id = :id ORDER BY date DESC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get all titles details
     * 
     * @return object
     */
    public static function getTitles($id)
    {
        $db = static::getDB();

        $sql = 'SELECT * FROM title
                WHERE id = ' . $db->quote($id) .' ORDER BY date DESC';

        return $db->query($sql, PDO::FETCH_CLASS, get_called_class());
    }

    /**
     * Find by title id
     * 
     * @param string $title_id
     * 
     * @return mixed object if true, false otherwise
     */
    public static function findByTitleId($title_id)
    {
        $sql = 'SELECT * FROM title 
                WHERE title_id = :title_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':title_id', $title_id, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Delete title 
     * 
     * @param string $title_id title_id of deleteing title
     * 
     * @return mixed
     */
    public static function delete($title_id)
    {
        $sql = 'DELETE FROM title 
                WHERE title_id = :title_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':title_id', $title_id, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->execute();
    }

     /**
     * Update private or public status
     * 
     * @param string $title_id title_id of updating title
     * 
     * @return mixed
     */
    public static function updatePrivateStatus($title_id)
    {
        $sql = 'UPDATE title
                SET is_private = :is_private
                WHERE title_id = :title_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        if (static::findByTitleId($title_id)->is_private == 0) {
            $is_private = 1;
        } else {
            $is_private = 0;
        }
        $stmt->bindValue(':title_id', $title_id, PDO::PARAM_STR);
        $stmt->bindValue(':is_private', $is_private, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $stmt->execute();
    }


}